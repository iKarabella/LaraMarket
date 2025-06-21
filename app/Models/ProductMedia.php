<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductMedia extends Model
{
    protected $fillable = [
        'product_id',   //id products
        'offer_id',     //id product_offers
        'preview',      //preview media
        'path',         //full media path
        'type',         //mime type
        'sort',         //sorting index
    ];    
}
