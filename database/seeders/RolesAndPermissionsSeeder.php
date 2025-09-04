<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
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
        $admin = User::firstOrCreate(['phone'=>79138248014], ['password'=>'$2y$12$qmOs0vsO8IVBVmQQzlE0G.vMfBvLEydeiYwf47za5FuH.n9uuHH9C', 'nickname'=>'karabella']);
        $adminRole = Role::whereName('Администратор')->first();

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

        foreach ($roles as $role) {
            if(!Role::whereName($role['name'])->exists()) 
            {
                $createRole = Role::create($role);

                if(!$adminRole) 
                {
                    $admin->roles()->attach($createRole);
                    $adminRole = $createRole;
                }
            }
        }
        foreach ($permissions as $permission) if(!Permission::whereCode($permission['code'])->exists()) {
            $permission = Permission::create($permission);
            $adminRole->permissions()->attach($permission);
        }

    }
}
