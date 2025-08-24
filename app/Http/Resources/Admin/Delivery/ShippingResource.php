<?php

namespace App\Http\Resources\Admin\Delivery;

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
        return [
            'id'=>$this->id,
            'order_id'=>$this->order_id,
            'warehouse_id'=>$this->warehouse_id,
            'address'=>$this->address,
            'carrier_key'=>$this->carrier_key,  // Key компании доставки (null при своей доставке)
            'carrier'=>$this->carrier,      // Название компании доставки (null при своей доставке),
            'courier'=>$this->courier ? ['id'=>$this->courier->id, 'name'=>$this->courier->name, 'nickname'=>$this->courier->nickname] : null,
            'delivered'=>$this->delivered,
            'cancelled'=>$this->cancelled,
            'created_at' => (new Carbon($this->created_at))->format('d.m.Y H:i:s'),
            'updated_at' => (new Carbon($this->updated_at))->format('d.m.Y H:i:s'),
        ];
    }
}