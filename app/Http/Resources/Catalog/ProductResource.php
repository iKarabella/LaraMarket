<?php

namespace App\Http\Resources\Catalog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'code'  => $this->code,          //линк
            'short_description'=>$this->short_description,
            'description' => $this->description,     // Описание
            'visibility'=> $this->visibility, // Видимость
            'offersign' => $this->offersign, 
            'categories'=> $this->categories, //категории
            'offers'    => $this->offers?OfferResource::collection($this->offers)->resolve():[],
            'measure'   => $this->measure??null,
            'media' => $this->media?ProductMediaResource::collection($this->media):[], //медиа
        ];
    }
}