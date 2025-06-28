<?php

namespace App\Http\Resources\Order;

use App\Http\Resources\EntityValueResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id'         => $this->id??null,
            'user_id'    => $this->user_id??null,
            'status'     => $this->status??null,
            'uuid'       => $this->uuid??null,
            'amount'     => $this->amount??null,
            'discount'   => $this->discount??null,
            'body'       => $this->body??[],
            'customer'   => $this->customer??[],
            'delivery'   => $this->delivery??[],
            'created_at' => new Carbon($this->created_at??null)->format('d.m.Y H:i'),
            'updated_at' => new Carbon($this->updated_at??null)->format('d.m.Y H:i'),
            'status_info'=> $this->status_info?EntityValueResource::make($this->status_info):null,
        ];
    }
}