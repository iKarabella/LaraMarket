<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Товары зарезервированные в заказах, списанные из stock_balance.
 */
class ReservedProduct extends Model
{
    protected $fillable = [
        'product_title', //title product, title offer string
        'order_id', //ID заказа, для которого резервированы товары from orders
        'product_id', //ID товара from products
        'offer_id', //ID ТП from product_offers
        'warehouse_id', //ID from warehouses
        'quantity', //количество в резерве
    ];
    
    public function offer()
    {
        return $this->hasOne(Offer::class, 'id', 'offer_id');
    }

    public function product()
    {
        return $this->hasOne(Product::class, 'id', 'product_id')->with('measure_value');
    }

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class, 'id', 'warehouse_id');
    }
}
