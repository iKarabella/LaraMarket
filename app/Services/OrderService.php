<?php

namespace App\Services;

use App\Http\Requests\Admin\Orders\OrderEditPositionRequest;
use App\Http\Requests\Catalog\CreateOrderRequest;
use App\Http\Requests\Catalog\StoreOrderRequest;
use App\Models\EntityValue;
use App\Models\Offer;
use App\Models\Order;
use App\Models\OrderComment;
use App\Models\StockReserve;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Обработка заказов от пользователя
 */
class OrderService 
{
    /**
     * Возвращает массив полей для создания заказа
     * 
     * @param Request $request 
     * @return array
     */
    public function makeOrder(CreateOrderRequest $request)
    {
        $validated = $request->validated();
        $offers = Offer::whereIn('id', array_column($validated['positions'], 'offer'))->with(['product', 'media'])->get();

        $order_positions = array_map(function($pos) use ($offers){
            $find = $offers->first(function($offer) use ($pos){return $offer->id==$pos['offer'] && $offer->product_id==$pos['position'];});
            if ($find) 
            {
                $quantity = $find->available<$pos['quantity']?$find->available:$pos['quantity'];
                return [
                    'position' => $find->product_id,
                    'offer' => $find->id,
                    'quantity' => $quantity,
                    'product_title'=>$find->product->title,
                    'offer_title'=>$find->title,
                    'measure'=>$find->product->measure_value->value,
                    'price'=>$find->price,
                    'total'=>$find->price*$quantity
                ];
            }
        }, $request->validated()['positions']);

        $order = [
            'total_sum'=>array_sum(array_column($order_positions, 'total')),
            'positions'=>$order_positions,
            'delivery'=>[
                'region'=>'Санкт-Петербург',
                'city'=>'Санкт-Петербург',
                'street'=>'наб. реки Фонтанки',
                'house'=>'123',
                'apartment'=>'123',
                'comment'=>'',
            ],
            'customer'=>[
                'name'=>$request->user()->name??'',
                'patronymic'=>$request->user()->patronymic??'',
                'surname'=>$request->user()->surname??'Фамилия',
                'phone'=>$request->user()->phone??'',
            ],
            'comment'=>'',
            'code'=>'',
        ];

        $request->session()->put('user.order_create', $order);

        return $order;
    }

    public function storeOrder(StoreOrderRequest $request):string|null
    {
        $validated = $request->validated();
        $uuid = null;

        try {
            $uuid = DB::transaction(function() use ($validated) 
            {
                $order = Order::create([
                    'user_id'   => (int) $validated['user_id'],
                    'amount'    => (int) $validated['total_sum'],
                    'body'      => $validated['positions'],
                    'customer'  => $validated['customer'],
                    'delivery'  => $validated['delivery'],
                    'shipping_code' => $validated['selected_shipping']
                ]);
                
                foreach ($validated['positions'] as $pos) StockReserve::create([
                    'offer_id' => $pos['offer'],
                    'order_id'    => $order->id,
                    'quantity' => $pos['quantity']
                ]);

                return $order->uuid;
            }, 3);
        }
        catch(Exception $e){}

        return $uuid;
    }

    /**
     * Установить статус
     *
     * @param $order Order|int Модель или ID заказа 
     * @param $statusId integer ID entity_value Entyty 2
     */
    public static function setStatus(Order|int $order, int $statusId, $title=null, $comment=null)
    {
        if (is_int($order)) $order = Order::whereId($order)->with(['status_info'])->firstOrFail();

        $isSaved=false;
        
        if ($order->status_info) $old = $order->status_info->value;
        else $old = '';

        $new = EntityValue::whereId($statusId)->firstOrFail(['value']);

        if($order->status!=$statusId)
        {
            $order->status=$statusId;
            $isSaved = $order->save();
        }
        else throw new Exception('Заказ уже имеет этот статус');

        if($isSaved) 
        {
            if(empty($title)) 
            {
                $user = Auth::user();
                $title = 'Пользователь <a href="'.route('user.page', [$user->nickname]).'" target="_blank" title="'.implode(' ', [$user->surname, $user->name, $user->patronymic]).'">'.$user->nickname.'</a> изменил статус заказа: ';
            }
            self::addComment($order->id, $title.'['.$old.'] -> ['.$new->value.']', $comment);
        }
        else throw new Exception('Не удалось сменить статус заказа');
    }

    public function orderEditPosition(Order $order, OrderEditPositionRequest $request)
    {
        $validated = $request->validated();
        
        $find = array_find_key($order->body, function($arr) use ($validated){
            return $arr['offer']==$validated['offer_id'];
        });

        $body = $order->body;

        if (count($body)<2 && $validated['quantity']<1) throw ValidationException::withMessages([
            'quantity' => ["Нельзя удалить единственную позицию заказа"],
        ]);

        if($find!==null) 
        {
            if($validated['quantity']>0) 
            {
                $offer = Offer::whereId($validated['offer_id'])->whereProductId($validated['product_id'])->with('stocks')->firstOrFail();
                $sum = $offer->stocks->pluck('quantity')->sum();

                if ($sum<$validated['quantity']) throw ValidationException::withMessages([
                    'quantity' => ["Количество позиций на складах ($sum) меньше указанного."],
                ]);
                
                $body[$find]['quantity']=(int)$validated['quantity'];
                $body[$find]['total']=$body[$find]['quantity']*$body[$find]['price'];
            }
            else unset($body[$find]);

            $order->body = $body;
            $order->amount = array_sum(array_column($order->body, 'total'));
            
            $order->save();
        }
    }

    public static function checkCompleted(Order|int $order)
    {
        if (is_int($order)) $order = Order::whereId($order)->with(['reserved_products'])->firstOrFail();
        
        return !array_any($order->body, function ($position) use ($order) {
            return $order->reserved_products->search(function($rp) use ($position){
                return $rp->product_id == $position['position'] && $rp->offer_id==$position['offer'];
            })===false;
        });
    }

    /**
     * Заказ получен клиентом
     */
    public static function orderDelivered(Order|int $order, ?string $comment = null)
    {
        self::setStatus($order, 12, $comment);
    }
    /**
     * Заказ отправлен клиенту
     */
    public static function orderSent(Order|int $order, ?string $comment = null)
    {
        self::addComment($order, '', $comment);
    }

    public static function addComment(Order|int $order, ?string $title = null, ?string $comment = null, bool $auto=true):void
    {
        if (is_int($order)) $order = Order::whereId($order)->with(['status_info'])->firstOrFail();

        OrderComment::create([
            'order_id' => $order->id,
            'auto' => $auto,
            'title' => $title,
            'comment' => $comment
        ]);
    }
}