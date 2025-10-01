<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Services\SearchService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function searchProduct(Request $request, SearchService $service)
    {
        dd($service->searchProduct($request));
    }
}