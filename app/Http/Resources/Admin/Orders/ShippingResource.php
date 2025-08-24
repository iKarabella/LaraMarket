<?php

namespace App\Http\Resources\Admin\Orders;

use App\Services\Shipping\ShippingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $shipping = ShippingService::get($this->shipping);
        return [
            'id'=>$this->id,
            'order_id'=>$this->order_id,
            'warehouse_id'=>$this->warehouse_id,
            'address'=>$this->address,
            'carrier_key'=>$this->carrier_key,  // Key компании доставки (null при своей доставке)
            'carrier'=>$this->carrier,      // Название компании доставки (null при своей доставке),
            'courier'=>$this->courier ? ['id'=>$this->courier->id, 'name'=>$this->courier->name, 'nickname'=>$this->courier->nickname] : null,
            'track'=>$this->track??null,
            'shipping'=>[
                'name'=>$shipping['name']??'',
                'title'=>$shipping['title']??'',
                'key'=>$shipping['key']??''
            ]
        ];
    }
}