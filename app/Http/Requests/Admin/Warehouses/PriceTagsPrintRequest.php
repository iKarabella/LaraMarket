<?php

namespace App\Http\Requests\Admin\Warehouses;

use Illuminate\Foundation\Http\FormRequest;

class PriceTagsPrintRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'format'=>'string|required|in:A4', //пока доступен только шаблон для A4 формата. значение параметра - название blade шаблона в views/PrintPriceTipsPages
            'warehouse_id'=>'nullable|exists:warehouses,id',
            'positions'=>'array|required|min:1',
            'positions.*'=>'array',
            'positions.*.title'=>'string|required',
            'positions.*.price'=>'string|required',
            'positions.*.offer_id'=>'numeric|required|exists:offers,id',
            'positions.*.quantity'=>'numeric|required|min:1|max:100',
            'positions.*.measure'=>'string|nullable',

        ];
    }
}
