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

        if ($action=='takeToDelivery') 
        {
            $this->additionalRules['id'] = [
                'numeric', 
                'required', 
                Rule::exists('shippings', 'id')->whereNull('courier')->whereNull('delivered')->whereNull('cancelled')
            ];
        }
        elseif($action=='cancelled' || $action == 'delivered')
        {
            $this->additionalRules['id'] = [
                'numeric', 
                'required', 
                Rule::exists('shippings', 'id')->where('courier', $this->user()->id)->whereNull('delivered')->whereNull('cancelled')
            ];
        }
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
            'action'=>['required', 'string', Rule::In(['takeToDelivery', 'delivered', 'cancelled', 'addComment'])],
            'comment'=>'string|nullable|required_if:action,cancelled,addComment'
        ];
        
        return array_merge($rules, $this->additionalRules);
    }

    public function messages():array
    {
        return [
            'id.exists'=>'Доставка не найдена или недоступна.',
            'action.in'=>'Действие не распознано.',
            'comment.required_if'=>'Для данного действия необходим комментарий.'
        ];
    }
}
