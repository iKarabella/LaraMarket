<?php

namespace App\Http\Requests\Admin\UsersManage;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        /**@var Request $this */
        $rules = [
            'id' => 'required|integer|exists:users',
            'roles' => 'array|nullable',
            'roles.*' => 'array',
            'roles.*.id' => 'required|numeric|exists:roles,id',
            'name' => 'string|nullable|max:50',
            'patronymic' => 'string|nullable|max:50',
            'surname' => 'string|nullable|max:50',
            'email' => ['string', 'nullable', 'email', Rule::unique(User::class)->ignore($this->id)],
            'nickname' => ['string', 'nullable', 'lowercase', 'regex:/^[A-Za-z0-9_-]+$/i', 'max:25', Rule::unique(User::class)->ignore($this->id)],
        ];
        
        return $rules;
    }

    public function messages()
    {
        return [
            'id.exists' => 'Пользователь не найден.',
            'nickname.unique' => 'Такой псевдоним уже занят.',
            'email.unique' => 'Этот адрес емайл уже используется.'
        ];
    }
}
