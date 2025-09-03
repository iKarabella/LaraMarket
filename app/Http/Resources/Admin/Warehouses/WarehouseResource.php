<?php

namespace App\Http\Resources\Admin\Warehouses;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => $this->id??null,
            'title' => $this->title??null,
            'code' => $this->code??null,
            'phone' => $this->phone??null,
            'address' => $this->address??null,
            'description' => $this->description??null,
            'cash_registers' => $this->cash_registers??null
        ];
    }
}