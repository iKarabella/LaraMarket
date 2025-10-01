<?php

namespace App\Http\Requests\Admin\Orders;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class OrderStoreCommentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    protected function prepareForValidation(): void
    {
        /**@var Request $this */
        $this->merge([
            'user_id'=>$this->user()->id,
            'auto'=>false,
            'title'=>'Пользователь <a href="'.route('user.page', [$this->user()->nickname]).'" target="_blank" title="'.implode(' ', [$this->user()->surname, $this->user()->name, $this->user()->patronymic]).'">'.$this->user()->nickname.'</a> оставил комментарий:'
        ]);
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'order_id'=>['numeric','required',Rule::exists('orders', 'id')->where(function ($query) {
                $query->whereNotIn('status', [11, 12]);
            })],
            'user_id'=>'numeric|required',
            'title'=>'string|nullable',
            'comment'=>'string|required|min:1',
            'auto'=>'boolean|required'
        ];
    }

    public function attributes(): array
    {
        return [
            'order_id' => 'Заказ',
            'title' =>'Заголовок',
            'comment'  => 'Комментарий'
        ];
    }

    public function messages():array
    {
        return [
            'order_id.exists'=>'Заказ не найден, либо его статус не предполагает возможность изменения.',
        ];
    }
}
