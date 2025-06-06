<?php

namespace App\Http\Resources\Admin\UsersManage;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsersResource extends JsonResource
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
            'nickname'=>$this->nickname??'',
            'name'=>$this->name??'',
            'surname'=>$this->surname??'',
            'patronymic'=>$this->patronymic,
            'phone'=>$this->phone??'',
            'email'=>$this->email??'',
            'birthday'=>$this->birthday(),
            'email_verified_at'=>!empty($this->email_verified_at),
            'phone_verified_at'=>!empty($this->phone_verified_at),
            'roles'=>$this->roles,
        ];
    }

    private function birthday(): string
    {
        if($this->birthday){
            $date = new Carbon($this->birthday);
            return $date->format('d.m.Y');
        }
        else return '';
    }
}