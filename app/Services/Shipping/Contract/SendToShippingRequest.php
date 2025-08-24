<?php

namespace App\Services\Shipping\Contract;

use App\Models\Order;
use App\Services\Shipping\ShippingService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class SendToShippingRequest extends FormRequest
{
    private $additionalRules = [];

    public function prepareForValidation()
    {
        $order = Order::whereId($this->orderId)->with('reserved_products')->firstOrFail();

        if (!$order->shipping_code) throw ValidationException::withMessages(['orderId' => 'В заказе не указан тип доставки']);

        if (array_any($order->body, function($position) use ($order){
            return !$order->reserved_products->first(function($reserved) use ($position, $order){
                return $reserved->offer_id == $position['offer'] && 
                       $reserved->quantity == $position['quantity'] && 
                       $reserved->warehouse_id == $order->warehouse_id;
            });
        })) throw ValidationException::withMessages(['orderId' => 'Не все товары из заказа списаны со склада']);

        if ($order->shipping_code) foreach (ShippingService::client($order->shipping_code)->wh_required_fields() as $key=>$field) 
        {
            $this->additionalRules[$key]=$field['rules'];
        }

        $this->merge([
            'preparing_fields'=>[
                'shipping_code' => $order->shipping_code,
                'body'=>$order->body,
                'order_id'=>$order->id,
                'order_uuid'=>$order->uuid,
                'warehouse_id'=>$order->warehouse_id,
                'customer'=>$order->customer,
                'delivery'=>$order->delivery,
                'amount'=>$order->amount
            ]
        ]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'orderId'=>'numeric|required',
            'preparing_fields'=>'array|required|min:8',
            'preparing_fields.shipping_code'=>'string|required',
            'preparing_fields.body'=>'array|required',
            'preparing_fields.order_id'=>'numeric|required',
            'preparing_fields.order_uuid'=>'string|required',
            'preparing_fields.warehouse_id'=>'numeric|required',
            'preparing_fields.customer'=>'array|required',
            'preparing_fields.delivery'=>'array|required',
            'preparing_fields.amount'=>'numeric|required'
        ];

        return [...$rules, ...$this->additionalRules];
    }
}