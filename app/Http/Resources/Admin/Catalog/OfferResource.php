<?php

namespace App\Http\Resources\Admin\Catalog;

use App\Http\Resources\Admin\Catalog\ProductMediaResource;
use Carbon\Carbon;
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
            'id' => $this->id??null,               // id продукта
            'product_id'=>$this->product_id??null,
            'title' => $this->title??'',         // Название
            'baseprice'  => $this->baseprice??null, //линк
            'price' => $this->price??null,     // Описание
            'barcode'=>$this->barcode??'',
            'art'=>$this->art??'',
            'visibility' => $this->visibility??true, // Видимость
            'weight' => $this->weight??null, 
            'length' => $this->length??null, //категории
            'height'    => $this->height??null,
            'width'    => $this->width??null,
            'media'=>ProductMediaResource::collection($this->media)->resolve(),
            'created' => new Carbon($this->created_at??null)->format('d.m.Y H:i:s'), //создан
            'updated' => new Carbon($this->updated_at??null)->format('d.m.Y H:i:s'), //обновлен
        ];
    }
}