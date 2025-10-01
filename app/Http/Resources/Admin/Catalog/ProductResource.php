<?php

namespace App\Http\Resources\Admin\Catalog;

use App\Http\Resources\Admin\Catalog\ProductMediaResource;
use Carbon\Carbon;
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
            'code'  => $this->code, //линк
            'short_description'=>$this->short_description,
            'description' => $this->description,     // Описание
            'visibility' => $this->visibility, // Видимость
            'offersign' => $this->offersign, 
            'categories' => $this->categories, //категории
            'offers'    => $this->offers??[],
            'media' => $this->media?ProductMediaResource::collection($this->media):[], //медиа
            'measure'   => $this->measure??null,
            'created' => (new Carbon($this->created_at))->format('d.m.Y H:i:s'), //создан
            'updated' => (new Carbon($this->updated_at))->format('d.m.Y H:i:s'), //обновлен
        ];
    }
}