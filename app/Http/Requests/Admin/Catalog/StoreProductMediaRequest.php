<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\File;

class StoreProductMediaRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'product_id'=>'numeric|required_without:offer_id|nullable|exists:products,id',
            'offer_id'=>'numeric|required_without:product_id|nullable|exists:offers,id',
            'files'=>'array|min:1',
            'files.*'=>File::image()->max('5mb')->dimensions(Rule::dimensions()->minWidth(350)->minHeight(350)->maxWidth(3500)->maxHeight(3500))
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
