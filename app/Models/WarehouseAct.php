<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseAct extends Model
{
    protected $fillable = [
        'user_id', //id пользователя
        'warehouse_id', //id склада
        'type', //type enum 'write-off'-списание|'receipt'-поступление
        'act', //список товаров
    ];

    protected $casts = [
        'act' => 'array',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }
}
