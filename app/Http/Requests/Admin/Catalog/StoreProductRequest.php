<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'id'=>'numeric|nullable|exists:products',
            'title'=>'string|required|min:5',
            'link'=>['string','required', Rule::unique('products')->ignore($this->id)],
            'short_description'=>'string|required|min:5',
            'description'=>'string|required|min:5',
            'visibility'=>'boolean',
            'offersign'=>'string|nullable',
            'categories'=>'array|required|min:1',
            'categories.*'=>'array',
            'categories.*.id'=>'numeric|required|exists:categories',
            'measure'=>'numeric|required|exists:entity_values,id'
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'Категория',
            'title'=>'Название',
            'link'=>'Код ссылки',
            'short_description'=>'Краткое описание',
            'description'=>'Описание',
            'visibility'=>'Видимость'
        ];
    }
}
