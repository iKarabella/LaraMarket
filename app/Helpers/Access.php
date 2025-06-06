<?php

use App\Models\Permission;
use App\Models\RolesHasPermission;
use App\Models\UsersHasRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

if (! function_exists('set_role_permissions')) {
    /**
     * Установить разрешения для роли
     * @param int $role_id ID роли
     * @param array $permissions<mixed, Item> список разрешений
     */
    function set_role_permissions(int $role_id, array $permissions):void
    {
        //
    }
}

if (! function_exists('set_user_roles')) {
    /**
     * Установить роли для пользователя
     * @param int $user_id ID пользователя
     * @param array $actualRoles<mixed, Item> список ролей пользоваля [[role_id=>int, user_id=>int, school_id=>int]]
     */
    function set_user_roles(int $user_id, array $actualRoles=[]):void
    {
        //
    }
}

if (! function_exists('access_rights')) {
    /**
     * Список разрешений для пользователя
     * @param string|null $code код конкретного разрешения
     * @return bool|array ['permission code','permission code']
     */
    function access_rights(?string $code=null):bool|array
    {
        $cache = session()->get('access_rights', false);

        if(!$cache || $cache['time']+60<time()){
            $cache = [
                'rights'=>[],
                'time'=>time()
            ];
    
            $user = Auth::user();

            if($user)
            {
                $get_permissions = DB::table('roles_has_permissions')
                                ->leftJoin('permissions', 'permissions.id', '=', 'roles_has_permissions.permission_id')
                                ->whereRaw('(`roles_has_permissions`.`role_id`, `roles_has_permissions`.`school_id`) IN (SELECT `role_id`, `school_id` FROM `users_has_roles` WHERE `user_id` = ?)', $user->id)
                                ->get(['permissions.code', 'roles_has_permissions.school_id']);
                    
                foreach ($get_permissions as $permission) $cache['rights'][] = $permission->code;
            }

            session()->put('access_rights', $cache);
        }
        
        if ($code) return in_array($code, $cache['rights']);
        else return $cache['rights'];
    }
}