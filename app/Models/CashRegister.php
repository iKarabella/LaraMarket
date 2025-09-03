<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashRegister extends Model
{
    protected $fillable = [
        'system', //сервис кассы
        'cr_id', //ID кассы
        'warehouse_id', //ID склада
        'user_id', //ID пользователя
        'details' //описание кассы массив [name=>'', address=>'', phone=>'']
    ];

    protected $casts = [
        'details' => 'array',
    ];
}
