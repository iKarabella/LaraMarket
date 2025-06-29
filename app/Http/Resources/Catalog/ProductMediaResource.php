<?php

namespace App\Http\Resources\Catalog;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductMediaResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'preview' => empty($this->preview)?'':url('storage/'.$this->preview),      //preview media
            'path' => empty($this->path)?'':url('storage/'.$this->path),         //full media path
            'type' => $this->type??null,         //mime type
            'sort' => $this->sort??50,         //sorting index
        ];
    }
}