<?php

namespace App\Services\User;

/**
 * Обработка заказов от пользователя
 */
class UserService 
{
    /**
     * Возвращает массив ссылок
     * 
     * @return array
     */
    public static function getHeadLinks():array
    {
        $rights = access_rights();
        $links = [];

        foreach ($rights as $permission) switch ($permission) {
            case 'users_manage': $links[]=['href'=>route('admin.users.manage'), 'title'=>'Пользователи']; break;
            case 'roles_and_permissions': $links[]=['href'=>route('admin.roles_and_permissions'), 'title'=>'Роли и права']; break;
            case 'orders_manage': $links[]=['href'=>route('admin.orders.manage'), 'title'=>'Заказы']; break;
            case 'warehouses_manage': $links[]=['href'=>route('admin.warehouses.manage'), 'title'=>'Склады']; break;
            case 'catalog_manage': $links[]=['href'=>route('admin.catalog.manage'), 'title'=>'Каталог']; break;
            case 'delivery_manage': $links[]=['href'=>route('admin.delivery.manage'), 'title'=>'Доставка']; break;
        }

        return $links;
    }
}