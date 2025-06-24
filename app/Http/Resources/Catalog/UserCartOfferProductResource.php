<?php

namespace App\Http\Resources\Catalog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCartOfferProductResource extends JsonResource
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
            'code'  => $this->code, //линк
            'short_description'=>$this->short_description,
            'offersign' => $this->offersign, 
            'measure'   => $this->measure_value??null,
            'media' => $this->media?ProductMediaResource::collection($this->media):[], //медиа
        ];
    }
}