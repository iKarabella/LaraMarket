<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpectedOffer extends Model
{
    protected $fillable = [
        'offer_id',
        'product_id',
        'user_id',
        'name',
        'email',
    ];

    public function offer()
    {
      return $this->hasOne(Offer::class, 'id', 'offer_id')->select(['title']);
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')->select(['title']);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->select(['id', 'name', 'patronymic', 'phone', 'email', 'phone_verified_at', 'email_verified_at']);
    }
}
