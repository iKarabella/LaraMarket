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
        $order = Order::whereId($request->order_id)->with(['status_info', 'reserved_products'])->firstOrFail();

        $cancelled = $request->goods_returned ? WarehouseService::cancelOrderReservation($order->reserved_products, $order->id, $order->body, $order->warehouse_id) : true;
        if ($cancelled===true) OrderService::setStatus($order, 11, null, $request->comment);
    }

    public function orderToAssembly(OrderToAssemblyRequest $request)
    {
        OrderService::setStatus($request->order_id, 8);
    }
}
