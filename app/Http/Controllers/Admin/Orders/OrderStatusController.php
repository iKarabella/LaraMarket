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
        $cancelled = true; //TODO отменить резерв товаров
        if ($cancelled===true) OrderService::setStatus($request->order_id, 11, null, $request->comment);
    }

    public function orderToAssembly(OrderToAssemblyRequest $request)
    {
        OrderService::setStatus($request->order_id, 8);

        // $validated = $request->validated();
        // $order = Order::whereId($validated['order_id'])->firstOrFail();        
        // $orderBody = collect($order->body);
        // $toReserve=[];

        // foreach($validated['toAssembly'] as $position)
        // {
        //     $inOrder = $orderBody->first(function($arr) use ($position) {return $arr['offer']==$position['offer'] && $arr['position']==$position['position'];});

        //     if (($inOrder['quantity'] != $position['quantity']) || ($position['quantity'] != array_sum(array_column($position['writeOffWh'], 'quantity'))))
        //     {
        //         throw ValidationException::withMessages([
        //             'order_id' => ["Ошибка при формировании списания."],
        //         ]);
        //     }

        //     $reserve = array_map(function($p) use ($position, $order) {
        //         return new ReservationDTO(name:"{$position['product_title']}, {$position['offer_title']}", order_id:$order->id, product_id:$position['position'], offer_id:$position['offer'], warehouse_id:$p['id'], quantity:$p['quantity']);
        //     }, $position['writeOffWh']);

        //     array_push($toReserve, ...$reserve);
        // }

        // $reserved = WarehouseService::reservation($toReserve);
        
        // if ($reserved===true) 
        // {
        //     StockReserve::whereOrderId($order->id)->delete();
        //     OrderService::setStatus($order, 8);
        // }     
    }
}
