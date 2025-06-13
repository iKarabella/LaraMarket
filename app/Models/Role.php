<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',        //Название
        'description', //описание
    ];

    function permissions(){
        return $this->belongsToMany(Permission::class, 'roles_has_permissions');
    }
}
