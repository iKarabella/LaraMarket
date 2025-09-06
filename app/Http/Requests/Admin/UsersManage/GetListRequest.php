<?php

namespace App\Http\Requests\Admin\UsersManage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetListRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'phone' => ['integer', 'nullable'],
            'name' => ['string', 'nullable'],
            'surname' => ['string', 'nullable'],
            'login' => ['string', 'nullable'],
            'perPage' => ['integer', 'nullable'],
            'order' => ['string', 'nullable', Rule::in(['phone', 'name', 'surname', 'login'])],
            'desc' => ['boolean', 'nullable'],
            'page' => ['integer', 'nullable'],
            'couriers'=>['boolean', 'nullable']
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'phone' => $this->phone?intval($this->phone):null,
            'name' => $this->name?$this->name:'',
            'surname' => $this->surname?$this->surname:'',
            'login' => $this->login?$this->login:'',
            'perPage' => $this->perPage?$this->perPage:25,
            'order' => $this->order,
            'desc' =>(bool) $this->desc
        ]);
    }
}
