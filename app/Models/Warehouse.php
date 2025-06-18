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
    ];
}
