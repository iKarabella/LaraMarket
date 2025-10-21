<?php

namespace App\Http\Resources\Catalog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OfferResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'title' => $this->title,
            'price' => round($this->price/100),
            'barcode' => $this->barcode,
            'art' => $this->art,
            'visibility' => $this->visibility,
            'coeff' => $this->coeff,
            'weight' => $this->weight,
            'length' => $this->length,
            'width' => $this->width,
            'height' => $this->height,
            'available'=>$this->available,
            'notify'=>$this->notify
        ];
    }
}