<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\CreateOrderRequest;
use App\Http\Requests\Catalog\StoreOrderRequest;
use App\Services\OrderService;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function create(CreateOrderRequest $request, OrderService $service)
    {
        return Inertia::render('Catalog/OrderCreate', [
            'order'  => $service->makeOrder($request),
        ]);
    }

    public function store(StoreOrderRequest $request)
    {
        dd($request->toArray());
    }
}
