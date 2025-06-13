<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entity extends Model
{
    protected $fillable = [
        'entiname',
        'description'
    ];

    public function values(){
        return $this->hasMany(EntityValue::class, 'entity', 'id');
    }
}
