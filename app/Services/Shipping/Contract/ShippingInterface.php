<?php

namespace App\Services\Shipping\Contract;

use App\Models\Shipping;

interface ShippingInterface
{
    /**
     * Заказ принят службой доставки
     * @param Shiping $shipping Модель Shipping
     * @return string Комментарий для заказа
     */
    public function takeToDelivery(Shipping &$shipping):string;

    /**
     * Заказ доставлен получателю
     * @param App\Models\Shipping Модель Shipping
     */
    public function delivered(Shipping &$shipping);

    /**
     * Заказ возвращен на склад
     * @param App\Models\Shipping Модель Shipping
     */
    public function returned(Shipping &$shipping);

    /**
     * Доставка заказа прервана
     */
    public function failed();

    /**
     * Список обязательных полей для формирования заказа
     */
    public static function required_fields():array;
}