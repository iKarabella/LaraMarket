<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ModulkassaDocs extends Model
{
    protected $fillable = [
        'order_id', //ID заказа из Orders
        'guid', //ID заказа в торговой точке
        'point', //ID торговой точки
        'status', //статус заказа в торговой точке
        'order_info', //информация по заказу в торговой точке
    ];

    protected $casts = [
        'order_info' => 'array',
    ];
}
