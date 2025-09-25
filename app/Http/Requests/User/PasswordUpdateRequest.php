<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PasswordUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => 'required|current_password',
            'password' => 'required|min:6|max:25|different:current_password',
            'password_confirmation' => 'required|same:password',
        ];
    }

    public function attributes(): array
    {
        return [
            'current_password' => 'Текущий пароль',
            'password' => 'Новый пароль',
            'password_confirmation' => 'Подтверждение пароля',
        ];
    }
}
