<?php

namespace App\Http\Requests\Catalog;

use App\Models\Offer;
use App\Services\Shipping\ShippingService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class StoreOrderRequest extends FormRequest
{
    private $additionalRules = [];

    /**
     * Подготовка к проверке данных
     * состав и сумму заказа берем сохраненную из сессии,
     * т.к. она не должна редактироваться со стороны пользователя
     */
    protected function prepareForValidation(): void
    {
        /**@var Request $this */
        $saved_data = $this->session()->get('user.order_create', false);
        $errors=[];
        $total=0;
        
        if (!$saved_data) $errors[]=['order'=>['Нет данных о заказе']];
        else 
        {    
            if ($this->selected_shipping) 
            {
                $shipping = ShippingService::get($this->selected_shipping);
                
                if ($shipping) foreach($shipping['service']::required_fields() as $key=>$block){
                    foreach($block as $kField=>$field){
                        if (isset($field['rules']) && !empty($field['rules'])) $this->additionalRules["{$key}.{$kField}"]=$field['rules'];
                    }
                }
            }

            $offers = Offer::whereIn('id', array_column($saved_data['positions'], 'offer'))->whereVisibility(true)->with(['product', 'media'])->get();
    
            foreach ($saved_data['positions'] as $key=>$pos) 
            {
                $offer = $offers->first(function($offer) use ($pos){return $offer->id==$pos['offer'] && $offer->product_id==$pos['position'];});
    
                if (!$offer) $errors["positions.{$key}.offer"]=['Предложение не найдено'];
                else if ($offer->available < $pos['quantity']) $errors["positions.{$key}.quantity"]=['Количество больше доступного для заказа'];
                else $total += $offer->price*$pos['quantity'];
            }

            if(!$errors && $total!=$saved_data['total_sum']) $errors['total']=['Сумма заказа отличается от расчетной'];
            else $this->merge([
                'total_sum' => $saved_data['total_sum'],
                'positions' => $saved_data['positions'],
                'user_id'   => $this->user()->id??null
            ]);
        }

        if ($errors) {
            $this->session()->put('create_order_failed', $errors);
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $rules = [
            'user_id' => 'numeric|nullable',
            'total_sum'=>'integer|required',
            'positions'=>'array|nullable|min:1',
            'positions.*.position'=>['numeric','required'],
            'positions.*.offer'=>['numeric','required'],
            'positions.*.quantity'=>'integer|required|min:1',
            'positions.*.product_title'=>'string|nullable',
            'positions.*.offer_title'=>'string|nullable',
            'positions.*.measure'=>'string|nullable',
            'positions.*.price'=>'integer|nullable',
            'positions.*.total'=>'integer|nullable',
            'code'=>'nullable|string|max:35',
            'comment'=>'nullable|string|max:256',
            'selected_shipping'=>[Rule::in(ShippingService::keys())],
        ];

        // 'customer'=>'array|required',
        // 'customer.name'=>'string|min:2|max:25',
        // 'customer.patronymic'=>'nullable|string|max:25',
        // 'customer.surname'=>'required|string|min:2|max:25',
        // 'customer.phone'=>'required|string',
        // 'delivery'=>'array|required',
        // 'delivery.region'=>'nullable|string|min:2|max:35',
        // 'delivery.city'=>'required|string|min:2|max:35',
        // 'delivery.street'=>'required|string|min:2|max:35',
        // 'delivery.house'=>'required|string|min:2|max:35',
        // 'delivery.apartment'=>'nullable|string|min:2|max:35',
        // 'delivery.comment'=>'nullable|string|max:256',

        return [...$rules, ...$this->additionalRules];
    }

    public function attributes(): array
    {
        return [
            'positions' => 'Состав заказа',
        ];
    }
}
