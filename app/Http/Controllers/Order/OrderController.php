<?php

namespace App\Http\Controllers\Order;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\GetCartPositionsRequest;
use App\Http\Resources\Catalog\ProductResource;
use App\Http\Resources\Catalog\UserCartResource;
use App\Models\Offer;
use App\Models\Product;
use App\Traits\BreadcrumbTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{

    public function create(Request $request)
    {
        dd($request->toArray());
    }
}
