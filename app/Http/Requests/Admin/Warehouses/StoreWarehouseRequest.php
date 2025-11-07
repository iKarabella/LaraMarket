<?php

namespace App\Http\Requests\Admin\Warehouses;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreWarehouseRequest extends FormRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'id'=>'numeric|nullable|exists:warehouses',
            'title'=>'string|required',
            'code'=>['string','required', Rule::unique('warehouses')->ignore($this->id, 'id')],
            'phone'=>'string|required',
            'address'=>'string|required',
            'description'=>'string|nullable',
            'self_pickup'=>'boolean'
        ];

        return $rules;
    }
}
