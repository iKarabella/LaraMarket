<?php

namespace App\Services\Shipping\Carriers\Own;

use App\Services\Shipping\Contract\SendToShippingRequest;
use App\Models\Shipping;
use App\Services\Shipping\Contract\ShippingInterface;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Auth;

class OwnService implements ShippingInterface
{
    /**
     * Сохранить заказ
     * 
     * @param App\Services\Shipping\Contract\SendToShippingRequest $request
     * @return string|null
     */
    public function create(SendToShippingRequest $request):void
    {
        try {
            Shipping::create([
                'order_id'=>$request->preparing_fields['order_id'],     // ID заказа
                'warehouse_id'=>$request->preparing_fields['warehouse_id'], // ID склада
                'user_id'=>$request->user()->id,      // ID пользователя, создавшего отправление
                'shipping'=>'own',     // CODE сервиса (ENUM из ShippingService)
                'positions'=>$request->preparing_fields['body'],    // отправленные позиции
                'customer'=>$request->preparing_fields['customer'],     // Данные получателя [name=>фио, phone=>телефон]
                'address'=>"ул. {$request->preparing_fields['delivery']['street']} д.{$request->preparing_fields['delivery']['house']}, кв. {$request->preparing_fields['delivery']['apartment']}",      // адрес отправления
                'track'=>$request->trackNumber,        // трек-номер отправки
                'carrier_key'=>null,  // Key компании доставки (null при своей доставке)
                'carrier'=>null,      // Название компании доставки (null при своей доставке)
                'courier'=>null,      // ID users курьера
                'status'=>null,       // ID статус из site_entities_values, entity:15
                'weight'=>null,       // масса груза, г
                'width'=>null,        // ширина груза, мм
                'height'=>null,       // высота груза, мм
                'length'=>null        // длина груза, мм
            ]);
        }
        catch (Exception $e) {
            throw $e;
        }

    }

    public function canCreateShipping():bool
    {
        return true;
    }
    
    public function wh_required_fields():array
    {
        return [
            // 'trackNumber'=>[
            //     'type'=>'string',
            //     'required'=>true,
            //     'label'=>'Трекномер',
            //     'title'=>'Трекномер',
            //     'rules'=>'string|required|min:10|max:50',
            //     'default'=>null
            // ]
        ];
    }

    public function takeToDelivery(Shipping &$shipping, ?int $courier=null):void
    {
        if (!$courier) throw new Exception('Не указан курьер');

        $shipping->courier = $courier;

        if (!$shipping->save()) throw new Exception('Не удалось сохранить изменение статуса доставки');
    }

    public function delivered(Shipping &$shipping):void
    {
        $shipping->delivered = new Carbon();

        if (!$shipping->save()) throw new Exception('Не удалось сохранить изменение статуса доставки');
    }

    public function cancelled(Shipping &$shipping):void
    {
        $shipping->cancelled = new Carbon();
        if (!$shipping->save()) throw new Exception('Не удалось сохранить изменение статуса доставки');
    }

    // public function returned(Shipping &$shipping):void
    // {
    //     //WarehouseService::returnPositionsFromShipping($shipping);
        
    //     $shipping->status = 94;
    //     $isSaved = $shipping->save();
            
    //     if (!$isSaved) throw new Exception('Не удалось сохранить изменение статуса доставки');
    // }

    // public function failed(){
    //     dd('failed');
    // }

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
