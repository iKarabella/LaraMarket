<?php

namespace App\Http\Requests\Admin\Warehouses;

use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderMarkWhRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'offer_id'=>'required|numeric|exists:offers,id',
            'order_id'=>['required','numeric',Rule::exists('orders', 'id')->where(function (Builder $query) {
                $query->where('status', 8);
            })],
            'product_id'=>'required|numeric|exists:products,id',
            'quantity'=>'required|numeric',
            'product_title'=>'required_without:id|string',
            'warehouse_id'=>'required|numeric|exists:warehouses,id'
        ];
    }
}