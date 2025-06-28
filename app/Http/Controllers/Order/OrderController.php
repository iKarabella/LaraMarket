<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\CreateOrderRequest;
use App\Http\Requests\Catalog\StoreOrderRequest;
use App\Http\Resources\Order\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function create(CreateOrderRequest $request, OrderService $service):Response
    {
        return Inertia::render('Order/OrderCreate', [
            'order'  => $service->makeOrder($request),
        ]);
    }

    public function store(StoreOrderRequest $request, OrderService $service):Response
    {
        $uuid = $service->storeOrder($request);
        $order = $request->session()->get('user.order_create', []);
        $request->session()->forget('user.order_create');
        
        return Inertia::render('Order/OrderCreate', [
            'order' => $order,
            'order_uuid'  => $uuid,
            'remove_from_cart' => array_column($order['positions'], 'offer')
        ]);
    }

    public function show(Request $request, $uuid)
    {
        $order = Order::whereUuid($uuid)->with(['user_info', 'status_info'])->firstOrFail();

        if ($order->user_id && ($request->user()==null || $order->user_id != $request->user()->id)) abort(404);

        return Inertia::render('Order/Order', [
            'order' => OrderResource::make($order)->resolve()
        ]);
    }
}
