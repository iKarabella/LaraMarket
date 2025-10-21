<?php

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class GetCartPositionsRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'positions'=>'array|required',
            'positions.*'=>'array|nullable',
            'positions.*.position'=>'numeric|nullable|exists:products,id',
            'positions.*.offer'=>'numeric|nullable|exists:offers,id',
            'positions.*.quantity'=>'numeric|nullable|max:99999'
        ];
    }

    public function attributes(): array
    {
        return [
            'positions' => 'Позиции',
        ];
    }
}
