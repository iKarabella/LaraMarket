<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',       //Название
        'description',//описание
    ];

    function permissions(){
        return $this->hasMany(RolesHasPermission::class, 'role_id', 'id');
    }
}
