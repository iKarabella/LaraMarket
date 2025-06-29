<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Orders\OrderCancelRequest;
use App\Http\Requests\Admin\Orders\OrderToAssemblyRequest;
use App\Http\Requests\Admin\Orders\OrderWaitingPaymentRequest;
use App\Models\Order;
use App\Models\StockReserve;
use App\Services\OrderService;
use App\Services\WarehouseService\DTO\ReservationDTO;
use App\Services\WarehouseService\WarehouseService;
use Illuminate\Validation\ValidationException;

class OrderStatusController extends Controller
{    
    public function waitingPayment(OrderWaitingPaymentRequest $request)
    {
       OrderService::setStatus($request->order_id, 6); 
    }

    public function cancel(OrderCancelRequest $request)
    {
        $cancelled = true; //TODO отменить списание товаров заказа со склада
        if ($cancelled===true) OrderService::setStatus($request->order_id, 11, null, $request->comment);
    }

    public function orderToAssembly(OrderToAssemblyRequest $request)
    {
        $validated = $request->validated();
        $order = Order::whereId($validated['order_id'])->firstOrFail();        
        $orderBody = collect($order->body);
        $toReserve=[];

        if($orderBody->count()!=count($validated['toAssembly'])) throw ValidationException::withMessages([
            'order_id' => ["Ошибка при формировании списания."],
        ]);

        if($order->status!=7)
        {
            $whwo=[];
            $whWriteOff = array_map(function($arr){
                return array_column($arr['writeOffWh'], 'id');
            }, $validated['toAssembly']);
    
            foreach($whWriteOff as $w) $whwo=[...$whwo, ...$w];
            
            if(count(array_unique($whwo))>1) throw ValidationException::withMessages([
                'order_id' => ["Заказ с нескольких складов нельзя поставить в сборку до оплаты."],
            ]);
        }
        foreach($validated['toAssembly'] as $position)
        {
            $inOrder = $orderBody->first(function($arr) use ($position) {return $arr['offer']==$position['offer'] && $arr['position']==$position['position'];});

            if (($inOrder['quantity'] != $position['quantity']) || ($position['quantity'] != array_sum(array_column($position['writeOffWh'], 'quantity'))))
            {
                throw ValidationException::withMessages([
                    'order_id' => ["Ошибка при формировании списания."],
                ]);
            }

            $reserve = array_map(function($p) use ($position, $order) {
                return new ReservationDTO(name:"{$position['product_title']}, {$position['offer_title']}", order_id:$order->id, product_id:$position['position'], offer_id:$position['offer'], warehouse_id:$p['id'], quantity:$p['quantity']);
            }, $position['writeOffWh']);

            array_push($toReserve, ...$reserve);
        }

        $reserved = WarehouseService::reservation($toReserve);
        
        if ($reserved===true) 
        {
            StockReserve::whereOrderId($order->id)->delete();
            OrderService::setStatus($order, 8);
        }     
    }
}
