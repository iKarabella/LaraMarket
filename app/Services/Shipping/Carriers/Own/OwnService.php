<?php

namespace App\Services\Shipping\Carriers\Own;

use App\Models\Shipping;
use App\Services\Shipping\Contract\ShippingInterface;
use Exception;
use Illuminate\Support\Facades\Auth;

class OwnService implements ShippingInterface
{
    public function takeToDelivery(Shipping &$shipping):string
    {
        if (Auth::check()) new Exception('Не удалось сохранить изменение статуса доставки');

        $user = Auth::user();
        $shipping->courier = $user->id;
        $shipping->status  = 91;

        $res = $shipping->save();

        if ($res) return 'Принят в доставку со склада <b>"'.$shipping->warehouse_info->code.'</b>". Курьер: <a href="'.route('user.page', [$user->login]).'" target="_blank">'.$user->name.' '.$user->surname.'</a>';
        else throw new Exception('Не удалось сохранить изменение статуса доставки');
    }

    public function delivered(Shipping &$shipping):bool
    {
        $shipping->status = 92;
        $res = $shipping->save();

        if($res) return true;
        else throw new Exception('Не удалось сохранить изменение статуса доставки');
    }

    public function returned(Shipping &$shipping):void
    {
        //WarehouseService::returnPositionsFromShipping($shipping);
        
        $shipping->status = 94;
        $isSaved = $shipping->save();
            
        if (!$isSaved) throw new Exception('Не удалось сохранить изменение статуса доставки');
    }

    public function failed(){
        dd('failed');
    }

    public static function required_fields():array
    {
        $auth = Auth::user();
        return [
            'delivery' => [
                // 'city' => [
                //     'type'=>'string',
                //     'required'=>true,
                //     'label'=>'Город',
                //     'rules'=>'string',
                //     'title'=>'Город',
                //     'default'=>null,
                // ],
                'street' => [
                    'type'=>'string',
                    'required'=>true,
                    'label'=>'Улица',
                    'title'=>'Улица',
                    'rules'=>'string',
                    'default'=>null,
                ],
                'house' => [
                    'type'=>'string',
                    'required'=>true,
                    'label'=>'Дом',
                    'title'=>'Дом',
                    'default'=>null,
                    'rules'=>'string',
                ],
                'apartment' => [
                    'type'=>'string',
                    'required'=>true,
                    'label'=>'Квартира',
                    'title'=>'Квартира',
                    'default'=>null,
                    'rules'=>'string',
                ]
            ],
            'customer' => [
                'name'=>[
                    'type'=>'string',
                    'label'=>'Имя',
                    'required'=>true,
                    'default'=>$auth->name??null,
                    'rules'=>'string',
                ],
                'phone'=> [
                    'type'=>'string',
                    'required'=>true,
                    'label'=>'Телефон',
                    'title'=>'Телефон',
                    'default'=>$auth->phone??null,
                    'rules'=>'numeric',
                ],
            ]
        ];
    }
}
