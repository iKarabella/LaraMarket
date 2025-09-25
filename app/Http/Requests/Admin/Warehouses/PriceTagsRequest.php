<?php

namespace App\Http\Requests\Admin\Warehouses;

use Illuminate\Foundation\Http\FormRequest;

class PriceTagsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'createFromReceipt'=>'numeric|nullable|exists:warehouse_acts,id',
            'selectedWh'=>'numeric|nullable|exists:warehouses,id'
        ];
    }
}
