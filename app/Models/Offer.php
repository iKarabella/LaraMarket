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
        'height',
    ];

    protected $casts = [
        'visibility' => 'boolean',      
        'baseprice'=>'float:2',
        'price'=>'float:2'
    ];

    public function product()
    {
      return $this->hasOne(Product::class, 'id', 'product_id')->with(['measure_value', 'media']);
    }

    public function stocks()
    {
        return $this->hasMany(StockBalance::class, 'offer_id', 'id')->where('quantity', '>', 0);
    }

    public function media()
    {
        return $this->hasMany(ProductMedia::class, 'offer_id', 'id')->orderBy('sort');
    }
}
