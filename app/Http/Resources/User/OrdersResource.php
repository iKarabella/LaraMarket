<?php

namespace App\Http\Resources\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrdersResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'user_id'=>$this->user_id??'',
            'status'=>$this->status??'',
            'amount'=>$this->amount??'',
            'discount'=>$this->discount??'',
            'body'=>$this->body??'',
            'status_info'=>$this->status_info?$this->status_info->value:'',
            'created_at' => new Carbon($this->created_at??null)->format('d.m.Y H:m:s'),
            'updated_at' => new Carbon($this->updated_at??null)->format('d.m.Y H:m:s'),
        ];
    }
}