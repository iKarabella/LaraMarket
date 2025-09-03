<?php

namespace App\Services\WarehouseService\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetCashRegistersRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'warehouse_id'=>'numeric|required|exists:warehouses,id',
            'cash_registers'=>'array|required|min:1',
            'cash_registers.*.id'=>'string|required',
            'cash_registers.*.name'=>'string|required',
            'cash_registers.*.address'=>'string|required',
            'cash_registers.*.phone'=>'string|required',
            'cash_registers.*.addForWarehouse'=>'boolean|nullable'
        ];
    }

    public function attributes(): array
    {
        return [
            'positions' => 'Позиции',
        ];
    }
}
