<?php

namespace App\Services\Shipping;

use App\Services\Shipping\Contract\ShippingInterface;
use Exception;

class ShippingService
{
    private static array $services = [
        'own'=>[
            'name'=>'Доставка',
            'title'=>'Собственная служба доставки',
            'key'=>'own',
            'trackNumberField'=>false,
            'site_available'=>true,
            'service'=>'App\Services\Shipping\Carriers\Own\OwnService'
        ],
        'self_pickup'=>[
            'name'=>'Самовывоз',
            'title'=>'Самовывоз из магазина',
            'key'=>'self_pickup',
            'trackNumberField'=>false,
            'site_available'=>true,
            'service'=>'App\Services\Shipping\Carriers\SelfPickup\SelfPickupService'
        ],
        // 'sdek'=>[
        //     'name'=>'СДЭК',
        //     'title'=>'Служба доставки "СДЭК"',
        //     'key'=>'sdek',
        //     'site_available'=>true,
        //     'trackNumberField'=>true,
        //     'service'=>null
        // ],
    ];

    public static function client($key):ShippingInterface
    {
        $client = self::get($key);

        if ($client == null) throw new Exception('Неверный ключ сервиса');
        if ($client['service'] == null) throw new Exception('Не указан обработчик сервиса');

        return new $client['service']();
    }

    public static function getPublicServices()
    {
        return array_filter(self::$services, function ($a){return $a['site_available']==true;});
    }

    public static function get($key): array|null
    {
        if(isset(self::$services[$key])) return self::$services[$key];
        else return null;
    }

    public static function keys():array
    {
        return array_keys(self::$services);
    }
}
