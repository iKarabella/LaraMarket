<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderComment extends Model
{
    protected $fillable = [
        'user_id',  // ID автора из users
        'order_id', // ID заказа из orders
        'auto',     // true, если комментарий создан автоматически от других действий
        'title',    // Заголовок 
        'comment',  // Комментарий
    ];
}
