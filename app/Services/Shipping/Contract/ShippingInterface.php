<?php

namespace App\Services\Shipping\Contract;

use App\Services\Shipping\Contract\SendToShippingRequest;

interface ShippingInterface
{
    /**
     * Список обязательных полей для формирования заказа
     */
    public static function required_fields():array;

    /**
     * Список обязательных полей для склада, при передачи в доставку
     */
    public function wh_required_fields():array;

    /**
     * Создание доставки
     */
    public function create(SendToShippingRequest $request):void;
}