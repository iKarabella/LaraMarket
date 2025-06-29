<?php

namespace App\Traits;

trait MarketControllerTrait
{
    private array $sections = [
        [
            'name'=>'Каталог',
            'link'=>'admin.catalog.manage',
            'key'=>'categories'
        ],
        [
            'name'=>'Заказы',
            'link'=>'admin.orders.manage',
            'key'=>'orders'
        ],
        [
            'name'=>'Склады',
            'link'=>'admin.warehouses.manage',
            'key'=>'warehouses'
        ],
        [
            'name'=>'Доставка',
            'link'=>'admin.catalog.manage',
            'key'=>'delivery'
        ]
    ];

    private function getNavigation($current=null):array
    {
        return array_values(array_map(function($arr) use ($current){
            return [
                'name'=>$arr['name'], 
                'link'=>route($arr['link']), 
                'active'=>$current==$arr['key']
            ];
        }, $this->sections));
    }
}