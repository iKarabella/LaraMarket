<?php

namespace App\Http\Requests\Admin\Orders;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderSatWhRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'order_id'=>['numeric', 'required', Rule::exists('orders', 'id')->where(function (Builder $query) {
                $query->whereStatus(5);
            })],
            'warehouse_id'=>'numeric|required|exists:warehouses,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'order_id' => 'Заказ',
            'warehouse_id' => 'Комментарий',
        ];
    }

    public function messages():array
    {
        return [
            'order_id.exists'=>'Заказ не найден, либо его статус не предполагает возможность отмены.'
        ];
    }
}
