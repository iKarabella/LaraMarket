<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductMediaSortingRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'files'=>'array|min:1',
            'files.*'=>'array|required',
            'files.*.id'=>'numeric|required|exists:product_media',
            'files.*.sort'=>'integer|required',
        ];
    }
}
