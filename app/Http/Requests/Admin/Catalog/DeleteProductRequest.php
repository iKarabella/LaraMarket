<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class DeleteProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'id'=>'integer|required|exists:products',
            'password' => 'required|current_password'
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'ID товара',
        ];
    }
}
