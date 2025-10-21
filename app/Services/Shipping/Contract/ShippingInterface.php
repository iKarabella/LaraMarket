<?php

namespace App\Services\Shipping\Contract;

use App\Models\Shipping;
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

    /**
     * Может ли сервис создавать доставку
     */
    public function canCreateShipping():bool;

    /**
     * Метод, вызываемый при приеме сервисом заказа на доставку.
     */
    public function takeToDelivery(Shipping &$shipping):void;

    /**
     * Метод, вызываемый при успешном завершении доставки.
     */
    public function delivered(Shipping &$shipping):void;

    /**
     * Метод, вызываемый при отмене доставки.
     */
    public function cancelled(Shipping &$shipping):void;
}