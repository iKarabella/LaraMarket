<?php

namespace App\Http\Resources\Admin\Catalog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductOfferResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'product_title' => $this->product_title??'',
            'product_id' => $this->product_id??null,
            'id' => $this->id??null,
            'art' => $this->art??'',
            'title' => $this->title??'',
            'baseprice' => $this->baseprice?number_format($this->baseprice, 2, '.', ''):0,
            'price' => $this->price?number_format($this->price, 2, '.', ''):'',
            'coeff' => $this->coeff??0,
            'quantity' => $this->quantity??0,
            'measure_val' => $this->measure_val
        ];
    }
}