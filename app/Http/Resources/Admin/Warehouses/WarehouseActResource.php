<?php

namespace App\Http\Resources\Admin\Warehouses;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WarehouseActResource extends JsonResource
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
            'user_id' => $this->user_id??null,
            'warehouse_id' => $this->warehouse_id??null,
            'type' => $this->type??null,
            'act' => $this->act??null,
            'comment' => $this->comment??null,
            'user' => $this->user ? [
                'id'=>$this->user->id,
                'nickname'=>$this->user->nickname,
                'name'=>implode(' ', array_filter([$this->user->name, $this->user->surname]))
            ] : null,
            'created' => (new Carbon($this->created_at))->format('d.m.Y H:i:s'),
        ];
    }
}