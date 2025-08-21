<?php

namespace App\Http\Requests\Admin\Warehouses;

use Illuminate\Foundation\Http\FormRequest;

class OrdersListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
           'filters'=>'array|nullable',
           'filters.statuses'=>'array|nullable',
           'filters.statuses.*.status'=>'numeric|required|exists:entity_values,id',
           'filters.statuses.*.on'=>'boolean|required',
           'filters.dates'=>'array|nullable|min:2',
           'filters.dates.*'=>'date|nullable',
           'filters.sortDesc'=>'boolean|nullable'
        ];
    }
}
