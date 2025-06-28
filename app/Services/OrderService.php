<?php

namespace App\Services;

use App\Http\Requests\Catalog\CreateOrderRequest;
use App\Http\Requests\Catalog\StoreOrderRequest;
use App\Models\Offer;
use App\Models\Order;
use App\Models\StockReserve;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
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
                    'delivery'  => $validated['delivery']
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
}