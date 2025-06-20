<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Catalog\ProductResource;
use App\Models\Product;
use App\Traits\BreadcrumbTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    use BreadcrumbTrait;

    public function index(Request $request, string $code): Response
    {
        $product = Product::whereCode($code)
                          ->with(['categories', 'media', 'publicOffersWithRel', 'measure_value'])
                          ->firstOrFail();
        return Inertia::render('Catalog/ProductCard', [
            'status' => session('status'),
            'product'   => ProductResource::make($product)->resolve(),
            'breadcrumb' => $this->getBreadcrumb($product)
        ]);
    }
}
