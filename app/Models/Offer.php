<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    protected $fillable = [
        'product_id',
        'title',
        'baseprice',
        'price',
        'barcode',
        'art',
        'visibility',
        'weight',
        'length',
        'width',
        'height'
    ];

    protected $casts = [
        'visibility' => 'boolean',      
        'baseprice'=>'float:2',
        'price'=>'float:2'
    ];

    public function product()
    {
      return $this->hasOne(Product::class, 'id', 'product_id')->with('measure_value');
    }
}
