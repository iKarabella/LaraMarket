<?php

namespace App\Http\Requests\Admin\Delivery;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'filters'=>'array|nullable',
            'filters.statuses'=>'array|nullable',
            'filters.statuses.*.status'=>'string|required',
            'filters.statuses.*.on'=>'boolean|required',
            'filters.dates'=>'array|nullable|min:2',
            'filters.dates.*'=>'date|nullable',
            'filters.sortDesc'=>'boolean|nullable'
        ];
    }

    public function messages():array
    {
        return [
            'id.exists'=>'Доставка не найдена или недоступна.',
            'action.in'=>'Действие не распознано.',
            'comment.required_if'=>'Для данного действия необходим комментарий.'
        ];
    }
}
