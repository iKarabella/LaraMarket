<?php

namespace App\Http\Resources\User;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $access=false;

        if($request->user() && $request->user()->id==$this->id) $access=true;

        return [
            'id'=>$this->id,
            'name'=>$this->name??'',
            'surname'=>$access ? $this->surname??'' : $this->firstLetter($this->surname),
            'patronymic'=>$access ? $this->patronymic??'':'',
            'phone'=>$access ? $this->phone??'' : '',
            'email'=>$access ? $this->email??'' : '',
            'birthday'=>$access ? $this->birthday() : $this->hideStr($this->birthday),
            'nickname'=>$this->nickname??'',
            'email_verified_at'=>!empty($this->email_verified_at),
        ];
    }

    private function firstLetter($str):string
    {
        if(empty($str)) return '';
        return mb_substr($str, 0, 1).'.';
    }
    
    private function hideStr($str):string
    {
        return preg_replace('/[A-Za-zА-Яа-я0-9]/', '#', $str);
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