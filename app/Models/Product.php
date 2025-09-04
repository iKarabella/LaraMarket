<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'title',         //название
        'code',         //символьная ссылка для url
        'short_description', //краткое описание
        'description', //описание
        'visibility', //видимость на сайте
        'offersign', //признак торгового предложения (чем отличаются тп друг от друга: цвет, размер, кол-во, вес и т.п.)
        'measure',  //единица измерения из entities_values
        'sort', //индекс сортировки
        'temp_field' //технический временный параметр
    ];

    protected $casts = [
        'visibility' => 'boolean',
    ];
    
    public function offers()
    {
      return $this->hasMany(Offer::class, 'product_id', 'id')->with(['stocks']);
    }

    public function publicOffersWithRel()
    {
        return $this->hasMany(Offer::class, 'product_id', 'id')
                    ->whereRaw('visibility = true') // AND id IN (SELECT stocks_balances.offer_id FROM stocks_balances WHERE stocks_balances.offer_id=product_offers.id)
                    ->with(['stocks']);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories', 'product_id', 'category_id');
    }

    public function media()
    {
        return $this->hasMany(ProductMedia::class, 'product_id', 'id')->orderBy('sort');
    }
    
    public function measure_value()
    {
        return $this->hasOne(EntityValue::class, 'id', 'measure');
    }
}
