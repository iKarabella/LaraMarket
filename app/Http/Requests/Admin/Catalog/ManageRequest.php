<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class ManageRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'category'=>'integer|nullable|exists:categories,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'category' => 'Категория',
        ];
    }
}
