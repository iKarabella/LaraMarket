<?php

namespace App\Http\Requests\Admin\Orders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderEditPositionRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'order_id'=>['numeric', 'required', Rule::exists('orders', 'id')->where(function ($query) {
                $query->whereStatus(5);
            })],
            'offer_id'=>'numeric|required|exists:offers,id',
            'product_id'=>'numeric|required|exists:products,id',
            'quantity'=>'integer|nullable',
            'amount'=>'nullable',
        ];
    }

    public function attributes(): array
    {
        return [
            'order_id' => 'Заказ', 
            'quantity' => 'Количество'
        ];
    }

    public function messages():array
    {
        return [
            'order_id.exists'=>'Заказ не найден, либо его статус не предполагает возможность изменения.'
        ];
    }
}
