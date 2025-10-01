<?php

namespace App\Http\Requests\Admin\Catalog;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SetCatSortRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        /**@var Request $this */
        if ($this->id) $rule = ['string','required', 'min:3', 'max:25', Rule::unique(Category::class)->ignore($this->id)];
        else $rule = 'string|required|min:3|max:25|unique:categories,code';

        return [
            'id'=>'numeric|required|exists:categories',
            'sort'=>'integer|max:100|min:0',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'Категория',
            'title'=>'Название',
            'code'=>'Код ссылки',
            'description'=>'Описание',
            'visibility'=>'Видимость',
            'parent'=>'Родительская категория'
        ];
    }
}
