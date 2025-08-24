<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=RolesAndPermissionsSeeder
     */
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Администратор',
                'description' => 'Администратор'
            ]
        ];
        $permissions = [
            [
                'name' => 'Управление пользователями',
                'code' => 'users_manage',
                'description' => 'Управление пользователями'
            ],
            [
                'name' => 'Роли и права',
                'code' => 'roles_and_permissions',
                'description' => 'Управление ролями и правами пользователей'
            ],
            [
                'name' => 'Управление заказами',
                'code' => 'orders_manage',
                'description' => 'Управление заказами'
            ],
            [
                'name' => 'Управление складами',
                'code' => 'warehouses_manage',
                'description' => 'Управление складами'
            ],
            [
                'name' => 'Управление каталогом',
                'code' => 'catalog_manage',
                'description' => 'Управление каталогом'
            ],
            [
                'name' => 'Доставка заказов',
                'code' => 'delivery_manage',
                'description' => 'Доставка заказов'
            ]
        ];
        foreach ($roles as $role) if(!Role::whereName($role['name'])->exists()) Role::create($role);
        foreach ($permissions as $permission) if(!Permission::whereCode($permission['code'])->exists()) Permission::create($permission);
    }
}
