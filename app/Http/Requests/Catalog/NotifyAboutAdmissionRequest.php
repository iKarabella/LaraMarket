<?php

namespace App\Http\Requests\Catalog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class NotifyAboutAdmissionRequest extends FormRequest
{
    private array $additionalRules = [];

    /**
     * Подготовка к проверке данных
     */
    protected function prepareForValidation(): void
    {
        /**@var Request $this */
        $this->merge([
            'user_id' => $this->user() ? $this->user()->id : null
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'product_id' => 'required|numeric|exists:products,id',
            'offer_id' => 'required|numeric|exists:offers,id',
            'user_id' => 'nullable|numeric|exists:users,id',
            'name'=>'required_without:user_id|nullable|string|min:1|max:25',
            'email'=>'required_without:user_id|nullable|email',
        ];

        return array_merge($rules, $this->additionalRules);
    }

    public function messages(): array
    {
        return [
            'name.required_without'=>'Поле "Имя" обязательно',
            'email.required_without'=>'Поле "E-mail" обязательно',
        ];
    }
}
