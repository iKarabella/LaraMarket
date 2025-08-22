<?php

namespace App\Http\Resources\Admin\Warehouses;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseStocksInResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'offer_id'=>$this->offer_id??null,
            'product_id'=>$this->product_id??null,
            'product_title'=>$this->product_title??null,
            'offer_title'=>$this->offer_title??null,
            'baseprice'=>$this->baseprice??null,
            'price'=>$this->price/100??null,
            'visibility'=>$this->visibility??null,
            'stock_in'=>$this->stock_in??0,
            'stock_reserved'=>$this->stock_reserved??0,
            'measure_val'=>$this->measure_val??null,
        ];
    }
}