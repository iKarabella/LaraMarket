<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
