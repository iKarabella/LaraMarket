<?php

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class CreateOrderRequest extends FormRequest
{
    /**
     * Подготовка к проверке данных
     */
    protected function prepareForValidation(): void
    {
        if(!$this->positions)
        {
            $saved_data = $this->session()->get('user.order_create', false);

            if($saved_data) $this->merge([
                'positions' => $saved_data['positions']
            ]);
            else abort(204);
        }
    }

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
