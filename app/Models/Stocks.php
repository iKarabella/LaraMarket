<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stocks extends Model
{
    protected $fillable = [
        'warehouse_id', // id склада
        'offer_id',     // id торгового предложения
        'quantity',     // количество
    ];

    public function warehouse(){
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }
    public function offer(){
        return $this->hasOne(Offer::class, 'id', 'offer_id');
    }
}
