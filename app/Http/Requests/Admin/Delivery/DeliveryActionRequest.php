<?php

namespace App\Http\Requests\Admin\Delivery;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeliveryActionRequest extends FormRequest
{
    private array $additionalRules = [];

    protected function prepareForValidation(): void
    {
        $action = explode('@', $this->route()->getActionName())[1];

        $this->merge([
            'action'=>$action
        ]);

        if ($action == 'takeToDelivery') $this->additionalRules['id'] = [
            'numeric', 
            'required', 
            Rule::exists('shippings', 'id')->whereNull('courier')->whereNull('delivered')->whereNull('cancelled')
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'id'=>'numeric|required|exists:shippings',
            'action'=>['required', 'string', Rule::In(['takeToDelivery'])]
        ];
        
        return array_merge($rules, $this->additionalRules);
    }

    public function messages():array
    {
        return [
            'id.exists'=>'Доставка не найдена или недоступна.',
            'action.in'=>'Действие не распознано.'
        ];
    }
}
