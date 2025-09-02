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
            'courier'=>$this->courier,
            'delivered'=>$this->delivered,
            'cancelled'=>$this->cancelled,
            'warehouse_info'=>$this->warehouse_info ? [
                'id'=>$this->warehouse_info->id,
                'title'=>$this->warehouse_info->title,
                'phone'=>$this->warehouse_info->phone,
                'address'=>$this->warehouse_info->address,
                'code'=>$this->warehouse_info->code,
                'description'=>$this->warehouse_info->description
            ] : [],
            'order_info' => $this->order_info ? [
                'uuid' => $this->order_info->uuid,
                'body' => $this->order_info->body
            ] : [],
            //'courier_info'=>$this->courier_info ? ['id'=>$this->courier_info->id, 'name'=>$this->courier_info->name, 'nickname'=>$this->courier_info->nickname] : null,
            'created_at' => (new Carbon($this->created_at))->format('d.m.Y H:i:s'),
            'updated_at' => (new Carbon($this->updated_at))->format('d.m.Y H:i:s'),
        ];
    }
}