<?php

namespace App\Services\Search;

use App\Http\Resources\Catalog\ProductResource;
use App\Models\Category;
use App\Models\Product;
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
     */
    public function searchProducts(string $search):array
    {
        $categories = collect();
        $autocomplete = collect();
        $found = [];

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

        if ($this->manticore) {
            $query = Builder::table('products')->limit(10)->match($search);

            $found = $this->manticore->get($query);
            
            if (count($found))
            {
                $categories = Category::whereHas('products', function($query) use ($found) {
                    $query->whereIn('product_id', array_column($found, 'product_id'));
                })->get()->map($catMap);
                $found = array_map($proMap, $found);
            }
            else {
                //TODO если не нашлось
            }
        }
        else {
            $str='%'.str_replace(' ', '%', $search).'%';

            $found = Product::with(['offers'])->where('title', 'LIKE', $str)->limit(10)->get();

            $found -> map(function($arr){return $arr->categories->values();})
                   -> each(function($cats) use ($categories, $catMap){
                        $categories->push(...$cats->map($catMap));
                   });
            $found = $found->map($proMap);
        }

        return [
            'found'=>$found,
            'categories'=>$categories->unique()->values(),
            'recommended'=>$this->recommended(),
            'frequently'=>$this->frequently(),
            'autocomplete'=>$autocomplete
        ];
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