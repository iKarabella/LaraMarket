<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
  0 => "users_manage"
  1 => "catalog_manage"
  2 => "warehouses_manage"
  3 => "roles_and_permissions"
  4 => "orders_manage"
 */

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
                                ->whereRaw('(`roles_has_permissions`.`role_id`) IN (SELECT `role_id` FROM `users_has_roles` WHERE `user_id` = ?)', $user->id)
                                ->get(['permissions.code']);
                    
                foreach ($get_permissions as $permission) $cache['rights'][] = $permission->code;
            }

            session()->put('access_rights', $cache);
        }
        
        if ($code) return in_array($code, $cache['rights']);
        else return $cache['rights'];
    }
}