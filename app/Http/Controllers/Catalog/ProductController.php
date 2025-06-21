<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Catalog\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{

    public function index(Request $request, string $code): Response
    {
        dd($code);
    }
}
