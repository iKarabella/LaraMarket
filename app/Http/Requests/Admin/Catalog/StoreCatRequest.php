<?php

namespace App\Http\Requests\Admin\Catalog;

use App\Models\Category;
use App\Traits\StringTrait;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCatRequest extends FormRequest
{
    use StringTrait;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        if ($this->id) $rule = Rule::unique(Category::class)->where(fn (Builder $query) => $query->where('parent', $this->parent))->ignore($this->id);
        else $rule = Rule::unique(Category::class)->where(fn (Builder $query) => $query->where('parent', $this->parent));//unique:categories,code';

        return [
            'id'=>'numeric|nullable|exists:categories',
            'title'=>'string|min:3',
            'code'=>['string','required', 'min:3', 'max:25', $rule],
            'description'=>'string|nullable',
            'visibility'=>'boolean',
            'parent'=>'numeric|nullable|exists:categories,id'
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
