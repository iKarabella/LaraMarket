<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'title', //название
        'code', //код
        'phone', //телефон
        'address', //адрес
        'caschier', //кассовая система
        'description', //описание
        'self_pickup', //возможность самовывоза
    ];

    public function cash_registers()
    {
        return $this->hasMany(CashRegister::class, 'warehouse_id', 'id');
    }
}
