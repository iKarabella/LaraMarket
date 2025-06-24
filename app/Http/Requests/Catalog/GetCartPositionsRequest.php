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
            'positions'=>'array|required|min:1',
            'positions.*'=>'array|required',
            'positions.*.position'=>'numeric|required|exists:products,id',
            'positions.*.offer'=>'numeric|required|exists:offers,id',
            'positions.*.quantity'=>'numeric|required|max:99999'
        ];
    }

    public function attributes(): array
    {
        return [
            'positions' => 'Позиции',
        ];
    }
}
