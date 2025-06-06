<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolesHasPermission extends Model
{
    protected $fillable = [
        'role_id',
        'permission_id',
    ];
}
