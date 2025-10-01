<?php

namespace App\Http\Requests\Admin\UsersManage;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModulkassaUserRequest extends FormRequest
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
            'user_id' => 'required|numeric|exists:users,id',
            'cashierRegister'=>'required|string'
        ];

        if($this->method()=='POST'){
            $rules = [
                ...$rules, 
                [
                    'cashier' => 'required|string',
                    'point' => 'required|array',
                    'point.name'=>'required|string',
                    'point.address'=>'required|string',
                    'point.phone'=>'required|string'
                ]
            ];
        }
        
        return $rules;
    }

    public function messages()
    {
        return [
            'user_id.exists' => 'Пользователь не найден.',
        ];
    }
}
