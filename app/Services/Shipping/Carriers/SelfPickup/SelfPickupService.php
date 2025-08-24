<?php

namespace App\Services\Shipping\Carriers\SelfPickup;

use App\Services\Shipping\Contract\SendToShippingRequest;
use App\Models\Shipping;
use App\Models\Warehouse;
use App\Services\Shipping\Contract\ShippingInterface;
use App\Services\Shipping\Carriers\SelfPickup\Resources\WarehouseResource;
use Illuminate\Support\Facades\Auth;

class SelfPickupService implements ShippingInterface
{
    public function create(SendToShippingRequest $request):void
    {
        //
    }

    public function wh_required_fields():array
    {
        return [];
    }

    public static function required_fields():array
    {
        $auth = Auth::user();
        $whs = [];

        foreach(Warehouse::whereSelfPickup(true)->get() as $val)
        {
            $whs[$val->code]=[
                'title'=>$val->title,
                'code'=>$val->code,
                'description'=>"{$val->address}, тел. {$val->phone}"
            ];
        }

        return [
            'delivery'=>[
                'warehouse' => [
                    'type'=>'enum',
                    'rules'=>'string',
                    'label'=>'Выберите пункт выдачи заказов',
                    'title'=>'Пункт выдачи заказов',
                    'required'=>true,
                    'values'=>$whs,
                    'default'=>null,
                ]
            ],
            'customer'=>[
                'name'=>[
                    'type'=>'string',
                    'label'=>'Имя',
                    'rules'=>'string',
                    'required'=>true,
                    'default'=>$auth->name??null,
                ],
                'phone'=>[
                    'type'=>'string',
                    'label'=>'Телефон',
                    'rules'=>'numeric',
                    'required'=>true,
                    'default'=>$auth->phone??null,
                ]
            ]
        ];
    }
}
