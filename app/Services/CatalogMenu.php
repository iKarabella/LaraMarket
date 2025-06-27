<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * Главное меню
 */
class CatalogMenu 
{
    /**
     * Массив с полученными из БД категориями
     */
    private array $categories = [];

    /**
     * Кэшированное меню
     */
    private ?array $cashed = null;

    /**
     * Массив условий для запроса категорий в БД
     */
    private array $where = [];

    public function __construct()
    {
        //TODO проверяем, есть ли кэшированное меню
        //если есть вносим в $this->cashed
        //если нет -- запрашиваем категории.

        if(!config('app.catalog.show_categories_without_products', false)) $this->where[] = 'id IN (SELECT DISTINCT category_id FROM product_categories)';
        if(!access_rights('catalog_manage')) $this->where[] = 'visibility = 1';

        $where = (count($this->where)) ? 'WHERE '.implode(' AND ', $this->where) : '';

        $this->categories = DB::select(
            "WITH RECURSIVE CatsTree AS (
                SELECT id, title, code, parent FROM categories $where
                UNION ALL
                SELECT c.id, c.title, c.code, c.parent FROM categories c
                JOIN CatsTree ON c.id = CatsTree.parent
             )
             SELECT DISTINCT id, title, code, parent FROM CatsTree"
        );
    }

    /**
     * Запрос главного меню
     * 
     * @return array массив основного меню, в формате: 
     * [ 
     *      [
     *          'title' => название, 
     *          'link' => code, 
     *          'children' => [...]
     *      ], 
     *      ...
     * ]
     */
    public function get():array
    {
        if ($this->cashed) return $this->cashed;

        $parent = array_filter($this->categories, function($a){
            return is_null($a->parent);
        });

        return $this->CatsTree($parent);
    }

    /**
     * Рекурсивная функция построения меню с вложенными дочерними элементами.
     * 
     * @param array $tree Массив с элементами, в которые нужно добавить дочерние, при их наличии
     * @return array Массив с добавленными дочерними элементами
     */
    private function CatsTree($tree):array
    {
        return array_map(function($a)
            {
                $return = [
                    'title'=>$a->title,
                    'link'=>route('catalog', [$a->code]),
                ];
                
                $children = array_filter($this->categories, function($b) use ($a){return $b->parent==$a->id;});

                if (count($children)) $return['children']=$this->CatsTree($children);

                return $return;
            }, 
        $tree);
    }
}