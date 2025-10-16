<?php

namespace App\Services\Search;

use App\Http\Requests\Search\SearchRequest;
use App\Http\Resources\Catalog\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\SearchLog;
use App\Services\ManticoreSearch\Builder;
use App\Services\ManticoreSearch\ManticoreService;

class SearchService
{
    private ?ManticoreService $manticore = null;

    public function __construct()
    {
        $manticore_host = env('MANTICORE_MYSQL_HOST');
        $manticore_port = env('MANTICORE_MYSQL_PORT');

        if ($manticore_host && $manticore_port) 
        {
            $manticore = new ManticoreService($manticore_host, $manticore_port);

            if ($manticore->ready) $this->manticore = $manticore;
        }
    }

    /**
     * Поиск по товарам
     * 
     * @param App\Http\Requests\Search\SearchRequest $request запрос
     */
    public function searchProducts(SearchRequest $request):array
    {
        $categories = collect();
        $autocomplete = collect();
        $found = [];
        $driver = 'database';
        $limit = $request->method()=='POST'?10:30;
        $meta = [
            'current_page'=>intval($request->page>0 ? $request->page : 1),
            'total_products'=>0,
            'total'=>0,
            'per_page'=>$limit,
            'links'=>[],
        ];

        $catMap = function($c){
            return [
                'id'=>$c->id,
                'title'=>$c->title,
                'code'=>$c->code,
                'visibility'=>$c->visibility,
                'sort'=>$c->sort
            ];
        };
        
        $proMap = function($arr){
            return [
                'code'=>$arr['code'],
                'image'=>$arr['image'],
                'price'=>sprintf('%.2f', $arr['price']/100),
                'product_id'=>$arr['product_id'],
                'short_description'=>$arr['short_description'],
                'title'=>$arr['title']
            ];
        };

        if ($this->manticore) 
        {
            $driver='manticore';

            $query = Builder::table('products')->match($request->search)->limit($limit, $request->page)->filters($request->filters);

            $result = $this->manticore->get($query);

            if (count($result['found']))
            {
                $meta_total = array_find($result['meta'], function($arr){
                    return $arr['Variable_name']=='total_found';
                });

                if ($meta_total) {
                    $meta['total_products'] = (int) $meta_total['Value'];
                    $meta['total'] = (int) ceil($meta_total['Value']/$limit);
                }

                $categories = Category::whereHas('products', function($query) use ($result) {
                    $query->whereIn('product_id', array_column($result['found'], 'product_id'));
                })->get()->map($catMap);
                

                if ($request->method()=='GET') 
                {
                    $products = Product::with(['offers', 'categories', 'media'])
                                       ->whereIn('id', array_column($result['found'], 'product_id'))
                                       ->get();
                                       
                    $found = ProductResource::collection($products)->resolve();
                }
                else $found = array_map($proMap, $result['found']);
            }
            else {
                //TODO если не нашлось
            }
        }
        else {
            $str='%'.str_replace(' ', '%', $request->search).'%';

            $found = Product::with(['offers', 'categories', 'media'])->where('title', 'LIKE', $str)->limit($limit)->get();

            $found -> map(function($arr){return $arr->categories->values();})
                   -> each(function($cats) use ($categories, $catMap){
                        $categories->push(...$cats->map($catMap));
                   });
            $found = $found->map($proMap);
        }

        SearchLog::create([
            'search' => $request->search,
            'driver' => $driver,
            'user_id' => $request->user() ? $request->user()->id : null,
            'session_id' => $request->session()->getId(),
            'results' =>$found,
        ]);

        $meta['links'] = $this->metaLinks($meta, $request->search);

        return [
            'found'=>$found,
            'categories'=>$categories->unique()->values(),
            'recommended'=>$this->recommended(),
            'frequently'=>$this->frequently(),
            'autocomplete'=>$autocomplete,
            'meta'=>$meta
        ];
    }

    private function metaLinks(array $meta, string $search):array
    {
        $arr=[
            [
                'url'=>$meta['current_page']>1 ? route('searchProducts', ['page'=>$meta['current_page']-1, 'search'=>$search]) : '',
                'active'=>false,
                'label'=>'«'
            ]
        ];

        for ($i=1; $i<=$meta['total']; $i++) $arr[]=[
            'url'=>route('searchProducts', ['page'=>$i, 'search'=>$search]),
            'active'=>$meta['current_page']==$i,
            'label'=>$i
        ];

        $arr[]=[
            'url'=>$meta['current_page']<$meta['total'] ? route('searchProducts', ['page'=>$meta['current_page']+1, 'search'=>$search]) : '',
            'active'=>false,
            'label'=>'»'
        ];

        return $arr;
    }

    /**
     * Рекомендуемые
     */
    public function recommended()
    {
        return collect([]);
    }

    /**
     * Часто покупаемые
     */
    public function frequently()
    {
        return collect([]);
    }
}