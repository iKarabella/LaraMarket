<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Товары на балансе, зарезирвированные в заказах, но не списанные со склада.
 */
class StockReserve extends Model
{
    protected $fillable = [
        'offer_id',     // id торгового предложения
        'order_id',     // id заказа
        'quantity',     // количество
    ];

    public function offer(){
        return $this->hasOne(Offer::class, 'id', 'offer_id');
    }
}
