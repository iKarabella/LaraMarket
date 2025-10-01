<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Orders\OrderCancelRequest;
use App\Http\Requests\Admin\Orders\OrderToAssemblyRequest;
use App\Http\Requests\Admin\Orders\OrderWaitingPaymentRequest;
use App\Models\Order;
use App\Services\ModulKassa\ModulKassa;
use App\Services\OrderService;
use App\Services\WarehouseService\WarehouseService;

class OrderStatusController extends Controller
{    
    /**
     * Задать статус "Ожидание оплаты"
     * 
     * @param App\Http\Requests\Admin\Orders\OrderWaitingPaymentRequest $request
     * @return void
     */
    public function waitingPayment(OrderWaitingPaymentRequest $request):void
    {
       OrderService::setStatus($request->order_id, 6); 
    }

    /**
     * Отмена заказа
     * 
     * @param App\Http\Requests\Admin\Orders\OrderCancelRequest $request
     * @return void
     */
    public function cancel(OrderCancelRequest $request):void
    {
        $order = Order::whereId($request->order_id)->with(['status_info', 'reserved_products'])->firstOrFail();

        $cancelled = ($request->goods_returned && $order->warehouse_id) ? WarehouseService::cancelOrderReservation($order->reserved_products, $order->id, $order->body, $order->warehouse_id) : true;

        (new ModulKassa())->cancelOrder($order->id);
        
        if ($cancelled===true) OrderService::setStatus($order, 11, null, $request->comment);
    }

    /**
     * Управление заказами
     * 
     * @param App\Http\Requests\Admin\Orders\OrderToAssemblyRequest $request
     * @return void
     */
    public function orderToAssembly(OrderToAssemblyRequest $request):void
    {
        OrderService::setStatus($request->order_id, 8);
    }
}
