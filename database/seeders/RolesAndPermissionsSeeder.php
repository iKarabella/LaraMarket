<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            'name' => 'Администратор',
            'description' => 'Администратор'
        ]);

        DB::table('permissions')->insert([
            'name' => 'Управление пользователями',
            'code' => 'users_manage',
            'description' => 'Управление пользователями'
        ]);

        DB::table('permissions')->insert([
            'name' => 'Роли и права',
            'code' => 'roles_and_permissions',
            'description' => 'Управление ролями и правами пользователей'
        ]);

        DB::table('permissions')->insert([
            'name' => 'Управление заказами',
            'code' => 'orders_manage',
            'description' => 'Управление заказами'
        ]);

        DB::table('permissions')->insert([
            'name' => 'Управление складами',
            'code' => 'warehouses_manage',
            'description' => 'Управление складами'
        ]);

        DB::table('permissions')->insert([
            'name' => 'Управление каталогом',
            'code' => 'catalog_manage',
            'description' => 'Управление каталогом'
        ]);
    }
}
