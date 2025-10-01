<?php

namespace App\Http\Controllers\Admin\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Delivery\DeliveryActionRequest;
use App\Http\Requests\Admin\Delivery\DeliveryListRequest;
use App\Http\Resources\Admin\Delivery\ShippingResource;
use App\Models\CashRegister;
use App\Models\Shipping;
use App\Services\ModulKassa\ModulKassa;
use App\Services\OrderService;
use App\Services\Shipping\ShippingService;
use App\Traits\MarketControllerTrait;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DeliveryController extends Controller
{
    use MarketControllerTrait;

    /**
     * Список заказов
     * 
     * @param App\Http\Requests\Admin\Delivery\DeliveryActionRequest $request
     * @return Response
     */
    public function manage(DeliveryListRequest $request):Response
    {
        $shippings = Shipping::where(function($query)use($request){$query->whereNull('courier')->orWhere('courier', '=', $request->user()->id);});

        $validated = $request->validated();
        $filters = $request->session()->get('delivery_manage.filters', [
            'statuses' => [
                ['status'=>'delivered', 'name'=>'Доставлен', 'on'=>true],
                ['status'=>'cancelled', 'name'=>'Отменен', 'on'=>true],
                ['status'=>'processed', 'name'=>'В работе', 'on'=>true],
                ['status'=>'awaiting', 'name'=>'В ожидании', 'on'=>true],
            ],
            'dates' => [(new Carbon())->subDays(7), (new Carbon())->endOfDay()],
            'sortDesc' => false
        ]);

        if(isset($validated['filters']['statuses'])) 
        {
            if(array_any($validated['filters']['statuses'], function($a){return $a['on']==true;}))
            {
                $filters['statuses'] = array_map(function($s) use ($validated){
                    $s['on'] = array_find($validated['filters']['statuses'], function($f) use ($s){return $f['status']==$s['status'];})['on'];
                    return $s;
                }, $filters['statuses']);
            }
        }
        if(isset($validated['filters']['dates'])) $filters['dates'] = $validated['filters']['dates'];
        if(isset($validated['filters']['sortDesc'])) $filters['sortDesc'] = $validated['filters']['sortDesc'];

        $request->session()->put('delivery_manage.filters', $filters);
        
        $checks = array_filter($filters['statuses'], function($arr){return $arr['on'];});
        if ($checks) $shippings->where(function($query) use($checks) {
            foreach($checks as $index=>$check) {
                switch ($check['status']) {
                    case 'delivered': $query->{$index==0?'where':'orWhere'}('delivered', '!=', null); 
                        break;
                    case 'cancelled': $query->{$index==0?'where':'orWhere'}('cancelled', '!=', null); 
                        break;
                    case 'processed': $query->{$index==0?'where':'orWhere'}(function($subQuery){
                                            $subQuery->whereNotNull('courier')
                                                    ->whereNull('delivered')
                                                    ->whereNull('cancelled');
                                      }); 
                        break;
                    case 'awaiting': $query->{$index==0?'where':'orWhere'}(function($subQuery){
                                            $subQuery->whereNull('courier')
                                                    ->whereNull('delivered')
                                                    ->whereNull('cancelled');
                                     });  
                        break;
                }
            }
        });

        if(isset($filters['dates']) && count($filters['dates'])) 
        {
            if ($filters['dates'][0]) $shippings->where('created_at', '>', (new Carbon($filters['dates'][0]))->startOfDay());
            if ($filters['dates'][1]) $shippings->where('created_at', '<', (new Carbon($filters['dates'][1]))->endOfDay());
        }
        if(isset($filters['sortDesc'])) 
        {
            if($filters['sortDesc']) $shippings->orderByDesc('created_at');
            else $shippings->orderBy('created_at');
        }
        else $shippings->orderBy('created_at');

        return Inertia::render('Admin/Delivery/Manage', [
            'navigation' => $this->getNavigation('delivery'),
            'orders' => ShippingResource::collection($shippings->with(['warehouse_info', 'order_info'])->paginate(30)),
            'filters' => $filters
        ]);
    }

    /**
     * Принято в доставку
     * 
     * @param App\Http\Requests\Admin\Delivery\DeliveryActionRequest $request запрос
     * @return void
     */
    public function takeToDelivery(DeliveryActionRequest $request):void
    {
        

        $shipping = Shipping::whereId($request->id)->with(['order_info'])->firstOrFail();
        $courier = empty($request->user()->name) ? $request->user()->nickname : $request->user()->name.' '.$request->user()->surname;
        $comment = 'Принят в доставку со склада <b>"'.$shipping->warehouse_info->code.'</b>". Курьер: <a href="'.route('user.page', [$request->user()->nickname]).'" target="_blank">'.$courier.'</a>';

        $cashRegisters = CashRegister::whereUserId($request->user()->id)->get(['cr_id'])->pluck('cr_id');
        
        ShippingService::client($shipping->shipping)->takeToDelivery($shipping, $request->user()->id);

        (new ModulKassa())->sendOrder($cashRegisters, $shipping->order_info);

        OrderService::orderSent($shipping->order_info, $comment);
    }

    /**
     * Доставлено
     * 
     * @param App\Http\Requests\Admin\Delivery\DeliveryActionRequest $request запрос
     * @return void
     */
    public function delivered(DeliveryActionRequest $request):void
    {

        $shipping = Shipping::whereId($request->id)->firstOrFail();

        ShippingService::client($shipping->shipping)->delivered($shipping);
        
        OrderService::orderDelivered($shipping->order_id);

        (new ModulKassa())->cancelOrder($shipping->order_id);
    }


    /**
     * Отменено
     * 
     * @param App\Http\Requests\Admin\Delivery\DeliveryActionRequest $request запрос
     * @return void
     */
    public function cancelled(DeliveryActionRequest $request):void
    {
        dd($request->toArray());
    }


    /**
     * Добавить комментарий
     * 
     * @param App\Http\Requests\Admin\Delivery\DeliveryActionRequest $request запрос
     * @return void
     */
    public function addComment(DeliveryActionRequest $request):void
    {
        $shipping = Shipping::whereId($request->id)->firstOrFail();
        $author = empty($request->user()->name) ? $request->user()->nickname : $request->user()->name.' '.$request->user()->surname;
        $title = '<a href="'.route('user.page', [$request->user()->nickname]).'" target="_blank">'.$author.'</a> оставил комментарий.';

        OrderService::addComment($shipping->order_id, $title, $request->comment, false);
    }
}
