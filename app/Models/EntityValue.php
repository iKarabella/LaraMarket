<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EntityValue extends Model
{
    protected $fillable = [
        'entity',
        'value',
        'description',
        'available',
    ];

}
