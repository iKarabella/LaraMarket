<?php

namespace App\Http\Requests\Catalog;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOrderRequest extends FormRequest
{
    /**
     * Подготовка к проверке данных
     * состав и сумму заказа берем сохраненную из сессии,
     * т.к. она не должна редактироваться со стороны пользователя
     */
    protected function prepareForValidation(): void
    {
        $saved_data = $this->session()->get('user.order_create', false);

        if(!$saved_data){}
            
        $this->merge([
            'total_sum' => $saved_data['total_sum'],
            'positions' => $saved_data['positions']
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'total_sum'=>'numeric|required',
            'positions'=>'array|nullable|min:1',
            'positions.*.position'=>['numeric','required', Rule::exists('products', 'id')->where(function (Builder $query) {
                return $query->whereVisibility(1);
            })],
            'positions.*.offer'=>['numeric','required', Rule::exists('offers', 'id')->where(function (Builder $query) {
                return $query->whereVisibility(1);
            })],
            'positions.*.quantity'=>'integer|required|min:1',
            'customer'=>'array|required',
            'customer.name'=>'string|min:2|max:25',
            'customer.patronymic'=>'nullable|string|max:25',
            'customer.surname'=>'required|string|min:2|max:25',
            'customer.phone'=>'required|string|numeric',
            'delivery'=>'array|required',
            'delivery.region'=>'nullable|string|min:2|max:35',
            'delivery.city'=>'required|string|min:2|max:35',
            'delivery.street'=>'required|string|min:2|max:35',
            'delivery.house'=>'required|string|min:2|max:35',
            'delivery.apartment'=>'nullable|string|min:2|max:35',
            'delivery.comment'=>'nullable|string|max:256',
            'code'=>'nullable|string|max:35',
            'comment'=>'nullable|string|max:256'
        ];
    }

    public function attributes(): array
    {
        return [
            'positions' => 'Состав заказа',
        ];
    }
}
