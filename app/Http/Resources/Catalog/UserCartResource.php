<?php

namespace App\Http\Resources\Catalog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,               // id продукта
            'title' => $this->title,         // Название
            'price'  => round($this->price/100),
            'art'=>$this->art,
            'media' => $this->media?ProductMediaResource::collection($this->media):[], //медиа
            'available' => $this->available,
            'product' => UserCartOfferProductResource::make($this->product)
        ];
    }
}