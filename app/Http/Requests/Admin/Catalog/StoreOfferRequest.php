<?php

namespace App\Http\Requests\Admin\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOfferRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        /**@var Request $this */
        return [
            'id'=>'numeric|nullable|exists:offers',
            'product_id'=>['numeric','required','exists:products,id', Rule::excludeIf($this->id>0)],
            'title'=>'string|required|min:1',
            'baseprice'=>'integer|nullable',
            'price'=>'integer|required|min:0.01',
            'barcode'=>'string|nullable',
            'art'=>'string|nullable',
            'visibility'=>'boolean',
            'coeff'=>'integer|required',
            'weight'=>'integer|nullable',
            'length'=>'integer|nullable',
            'height'=>'integer|nullable',
            'width'=>'integer|nullable',
        ];
    }

    public function attributes(): array
    {
        return [
            'id' => 'Категория',
            'title'=>'Название',
            'baseprice'=>'Стоимость',
            'price'=>'Цена',
            'barcode'=>'Штрихкод',
            'art'=>'Артикул',
            'visibility'=>'Видимость',
            'to_caschier'=>'Передавать в кассу',
            'weight'=>'Масса',
            'length'=>'Длина',
            'height'=>'Высота',
            'width'=>'Ширина',
        ];
    }

    public function messages()
    {
        return [
            'price.decimal' => 'Число формата 0.00',
            'baseprice.decimal' => 'Число формата 0.00',
        ];
    }
}
