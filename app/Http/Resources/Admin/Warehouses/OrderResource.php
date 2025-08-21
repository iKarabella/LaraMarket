<?php

namespace App\Http\Resources\Admin\Warehouses;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id??null,            
            'user_id'=>$this->user_id??null,
            'uuid' => $this->uuid??null,
            'status'  => $this->status??null,
            'amount' => $this->amount??null,
            'discount' => $this->discount??null,
            'body' => $this->body??[],
            'customer' => $this->customer??[],
            'customer_string' => is_array($this->customer)?implode(' ', $this->delivery):'',
            'delivery' => $this->delivery??[],
            'delivery_string' => is_array($this->delivery)?implode(' ', $this->delivery):'',
            'status_info' => $this->status_info?[
                'id' => $this->status_info->id,
                'value' => $this->status_info->value,
                'description' => $this->status_info->descr,
            ]:null,
            'reserved_products' => $this->reserved_products->values(),
            'comments' => $this->comments?OrderCommentResource::collection($this->comments)->resolve():[],
            'created_at' => new Carbon($this->created_at??null)->format('d.m.Y H:i:s'),
            'updated_at' => new Carbon($this->updated_at??null)->format('d.m.Y H:i:s'),
        ];
    }
}