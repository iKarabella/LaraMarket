<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'user_id',  //ID заказчика из users
        'uuid',     //UUID
        'status',   //Статус заказа (Сущность)
        'amount',   //Сумма к оплате в копейках (1/100 рубля).
        'discount', //Скидка в копейках (1/100 рубля).
        'body',     //состав заказа
        'customer', //получатель
        'delivery', //доставка
        'shipping_code', //код сервиса доставки
        'warehouse_id', //склад списания
    ];

    protected $casts = [
        'body' => 'array',
        'customer' => 'array',
        'delivery' => 'array'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            $order->uuid = (string) Str::uuid();
        });
    }

    public function status_info()
    {
      return $this->hasOne(EntityValue::class, 'id', 'status');
    }

    public function user_info()
    {
      return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function stock_reserve()
    {
        return $this->hasMany(StockReserve::class, 'order_id', 'id');
    }

    public function reserved_products()
    {
      return $this->hasMany(ReservedProduct::class, 'order_id', 'id');
    }
    
    public function comments()
    {
      return $this->hasMany(OrderComment::class, 'order_id', 'id')->orderBy('id');
    }
}
