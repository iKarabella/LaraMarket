<?php

namespace App\Models;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

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
        'coeff',
        'weight',
        'length',
        'width',
        'height',
        'temp_field' //технический временный параметр
    ];

    protected $casts = [
        'visibility' => 'boolean',      
        'baseprice'=>'float:2',
        'price'=>'float:2'
    ];

    protected $appends = ['available', 'notify'];

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

    public function stocks_reserve(){
        return $this->hasMany(StockReserve::class, 'offer_id', 'id');
    }

    public function getAvailableAttribute():int
    {
        $count = $this->stocks->sum('quantity')-$this->stocks_reserve()->sum('quantity');
        return $count > 0 ? $count : 0;
    }

    public function getNotifyAttribute():bool
    {
        $user = Auth::user();

        if ($user) 
        {
            return ExpectedOffer::whereOfferId($this->id)
                                ->where(function(Builder $query) use ($user){
                                    $query->whereUserId($user->id);
                                    if ($user->email && $user->email_verified_at) $query->orWhere('email', '=', $user->email);
                                })
                                ->exists();
        }
        else return false;
    }
}
