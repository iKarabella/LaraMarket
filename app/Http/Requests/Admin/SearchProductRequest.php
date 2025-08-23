<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SearchProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'search'=>'string|required|min:1',
            'warehouse'=>'numeric|nullable|exists:warehouses,id'
        ];
    }

    public function attributes(): array
    {
        return [
            'search' => 'Поисковый запрос',
            'warehouse'=>'ID склада'
        ];
    }
}
