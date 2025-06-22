<?php

namespace App\Http\Requests\Admin\Warehouses;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseReceiptRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'warehouse'=>'numeric|required|exists:warehouses,id',
            'items'=>'array|required|min:1',
            'items.*'=>'array|required',
            'items.*.offer_id'=>'numeric|required|exists:offers,id',
            'items.*.price'=>'numeric|required',
            'items.*.quantity'=>'integer|required|min:1'
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'Категория',
        ];
    }
}
