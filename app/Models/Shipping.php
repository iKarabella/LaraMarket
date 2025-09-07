<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    protected $fillable = [
        'order_id',     // ID заказа
        'warehouse_id', // ID склада
        'user_id',      // ID пользователя, создавшего отправление
        'shipping',     // CODE сервиса (ENUM из ShippingService)
        'positions',    // отправленные позиции
        'customer',     // Данные получателя [name=>фио, phone=>телефон]
        'address',      // адрес отправления
        'track',        // трек-номер отправки
        'carrier_key',  // Key компании доставки (null при своей доставке)
        'carrier',      // Название компании доставки (null при своей доставке)
        'courier',      // ID users курьера
        'delivered',    // отметка о доставке
        'cancelled',    // отметка об отмене
        'weight',       // масса груза, г
        'width',        // ширина груза, мм
        'height',       // высота груза, мм
        'length'        // длина груза, мм
    ];
    
    protected $casts = [
        'positions' => 'array',
        'customer' => 'array',
        'delivered' => 'array',
        'cancelled' => 'array'
    ];

    protected $appends = ['status'];

    public function getStatusAttribute()
    {
        if ($this->delivered!=null) return 'delivered';
        else if ($this->cancelled!=null) return 'cancelled';
        else if ($this->courier!=null) return 'processed';
        else return 'awaiting';
    }

    public function order_info()
    {
        return $this->hasOne(Order::class, 'id', 'order_id');
    }

    public function courier_info()
    {
        return $this->hasOne(User::class, 'id', 'courier');
    }

    public function warehouse_info()
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }
}
