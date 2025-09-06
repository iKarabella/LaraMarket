<?php

namespace App\Traits;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

trait BreadcrumbTrait
{
    /**
     * Возвращает массив пунктов для Breadcrumb строки в каталоги
     * 
     * @param ?Product $product если указан конкретный товар
     * @param ?string $cat если указана категория
     * @return array Массив с breakcrumb элементами 
     */
    private function getBreadcrumb(?Product $product=null, ?string $cat = null):array
    {
        $breadcrumb = [
            ['title'=>'Catalog', 'link'=>route('catalog')],
        ];

        if($product!=null) 
        {
            $cat = $product->categories->firstOrFail()->code;
        }

        if($cat==null){
            $getCats = Category::whereNull('parent')->whereVisibility(true)->orderBy('sort')->get();
            $cats=[];
            foreach ($getCats as $c) $cats[]=['title'=>$c->title, 'link'=>route('catalog', $c->code)];
            $breadcrumb[]=['title'=>'Категория', 'link'=>$cats];
            $currentCat=(object)['id'=>null];
        }
        else
        {
            $rawSql="WITH RECURSIVE Tree AS (
                    SELECT id, parent, title, code, sort FROM categories WHERE code = ?
                    UNION ALL
                    SELECT cc.id, cc.parent, cc.title, cc.code, cc.sort FROM categories cc
                    JOIN Tree ON cc.id = Tree.parent
                )
                SELECT * FROM Tree";

            $catsTree = DB::select($rawSql, [$cat]);

            $getCats = Category::where(function(Builder $query) use ($catsTree){
                $query->whereNull('parent');
                if (count($catsTree)) $query->orWhereIn('parent', array_column($catsTree, 'id'));
            })->get(); //->whereVisibility(true)

            $currentCat = $getCats->first(function($f) use ($cat) {return $f->code==$cat;});

            $func = function($arr) use ($getCats)
            {
                $links = $getCats->filter(function($f) use($arr){
                    return $f->parent == $arr->parent;
                });

                if (count($links)>1) {
                    $link = $links->map(function($l){
                        return [
                            'title'=>$l->title,
                            'link'=>route('catalog', $l->code)
                        ];
                    })->values();
                }
                else {
                    $link = route('catalog', $arr->code);
                }

                return [
                    'title'=>$arr->title,
                    'link'=>$link
                ];
            };

            $tree = array_map($func, array_reverse($catsTree));
            $childs = [];

            if($product==null)
            {
                $childCats = $getCats->filter(function($arr) use ($currentCat){
                    return $arr->parent == $currentCat->id;
                })->map(function($arr){
                    return [
                        'title'=>$arr->title,
                        'link'=>route('catalog', $arr->code)
                    ];
                })->values();
    
                if ($childCats->count()) $childs = [[
                    'title'=>'Выбрать',
                    'link'=>$childCats
                ]];
            }

            array_push($breadcrumb, ...$tree, ...$childs);
        }

        if ($product!=null) $breadcrumb[]=['title'=>$product->title, 'link'=>''];

        return $breadcrumb;
    }
}