<?php

namespace App\Http\Requests\Admin\Orders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderCancelRequest extends FormRequest
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
                $query->whereIn('status', [5,6,8,10]);
            })],
            'goods_returned'=>'boolean|required',
            'password'=>'required|current_password',
            'comment'=>'string|required|min:5'
        ];
    }

    public function attributes(): array
    {
        return [
            'order_id' => 'Заказ',
            'goods_returned' => 'Товары возвращены на склад',
            'comment' => 'Комментарий',
            'password' => 'Пароль'
        ];
    }

    public function messages():array
    {
        return [
            'order_id.exists'=>'Заказ не найден, либо его статус не предполагает возможность отмены.'
        ];
    }
}
