<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\GetCartPositionsRequest;

class OrderController extends Controller
{

    public function create(GetCartPositionsRequest $request)
    {
        dd($request->toArray());
    }
}
