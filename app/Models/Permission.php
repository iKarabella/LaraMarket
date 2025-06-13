<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',         // имя
        'code',        // код-строка
        'description' // Описание 
    ];

    
    function roles(){
        return $this->belongsToMany(Role::class, 'roles_has_permissions');
    }
}
