<?php

namespace App\Http\Requests\Admin\Orders;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderToAssemblyRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'order_id'=>['numeric','required',Rule::exists('orders', 'id')->where(function (Builder $query) {
                $query->whereIn('status', [5,6,7]);
            })],
            'toAssembly'=>'array|required|min:1',
            'toAssembly.*.product_title'=>'string|required',
            'toAssembly.*.offer_title'=>'string|nullable',
            'toAssembly.*.offer'=>'numeric|required|exists:offers,id',
            'toAssembly.*.position'=>'numeric|required|exists:products,id',
            'toAssembly.*.quantity'=>'numeric|required|min:1',
        ];
    }

    public function attributes(): array
    {
        return [
            'order_id' => 'Заказ'
        ];
    }

    public function messages():array
    {
        return [
            'order_id.exists'=>'Заказ не найден, либо его статус не предполагает возможность изменения.',
        ];
    }
}
