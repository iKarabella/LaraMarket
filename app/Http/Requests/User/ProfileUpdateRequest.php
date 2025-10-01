<?php

namespace App\Http\Requests\User;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:50'],
            'patronymic' => ['string', 'nullable', 'max:50'],
            'surname' => ['string', 'nullable', 'max:50'],
            'email' => ['string', 'nullable', 'email', Rule::unique(User::class)->ignore($this->user()->id)],
            'nickname' => ['string', 'required', 'lowercase', 'regex:/^[A-Za-z0-9_-]+$/i', 'max:25', Rule::unique(User::class)->ignore($this->user()->id)],
            'phone' => ['required', 'integer', 'max:99999999999', Rule::unique(User::class)->ignore($this->user()->id)],
        ];
    }

    public function attributes(): array
    {
        return [
            'nickname' => 'Ник',
        ];
    }
}
