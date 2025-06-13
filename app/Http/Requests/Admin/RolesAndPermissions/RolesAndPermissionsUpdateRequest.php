<?php

namespace App\Http\Requests\Admin\RolesAndPermissions;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RolesAndPermissionsUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'role_id' => 'nullable|numeric|exists:roles,id',
            'name' => 'required|string|min:1',
            'description' => 'string|nullable|max:350',
            'permissions'=>'array|nullable',
            'permissions.*'=>'array',
            'permissions.*.id'=>'required|exists:permissions,id',
        ];
    }

    public function attributes(): array
    {
        return [
            'name' => 'Название роли',
        ];
    }
}
