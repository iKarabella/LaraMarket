<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Http\Requests\Search\SearchRequest;
use App\Services\Search\SearchService;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    /**
     * Поиск в бд
     * 
     * @param App\Http\Requests\Search\SearchRequest $request запрос
     */
    public function searchProducts(SearchRequest $request, SearchService $service):Response|JsonResponse
    {
        $results = $service->searchProducts($request);

        if ($request->method()=='POST') return response()->json($results);
        
        else return Inertia::render('Search/Search', [
            'status' => session('status'),
            'results'=> $results,
            'search' => $request->search
        ]);
    }
}