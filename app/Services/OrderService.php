<?php

namespace App\Services;

use App\Models\Offer;

/**
 * Главное меню
 */

class OrderService 
{
    /**
     * Возвращает массив полей для создания заказа
     * 
     * @param Request $request 
     * @return array
     */
    public function makeOrder($request){
        $offers = Offer::whereIn('id', array_column($request->positions, 'offer'))->with(['product', 'media', 'stocks'])->get();

        $order_positions = array_map(function($pos) use ($offers){
            $find = $offers->first(function($offer)use($pos){return $offer->id==$pos['offer'] && $offer->product_id==$pos['position'];});
            if ($find) 
            {
                $quantity = $find->stocks->max('quantity')<=$pos['quantity']?$find->stocks->max('quantity'):$pos['quantity'];
                return [
                    'position' => $find->product_id,
                    'offer' => $find->id,
                    'quantity' => $quantity,
                    'product_title'=>$find->product->title,
                    'offer_title'=>$find->title,
                    'measure'=>$find->product->measure_value->value,
                    'price'=>$find->price,
                    'total'=>$find->price*$quantity
                ];
            }
        }, $request->validated()['positions']);

        $order = [
            'total_sum'=>array_sum(array_column($order_positions, 'total')),
            'positions'=>$order_positions,
            'delivery'=>[
                'region'=>'',
                'city'=>'',
                'street'=>'',
                'house'=>'',
                'comment'=>'',
            ],
            'customer'=>[
                'name'=>$request->user()->name??'',
                'patronymic'=>$request->user()->patronymic??'',
                'surname'=>$request->user()->surname??'',
                'phone'=>$request->user()->phone??'',
            ],
            'comment'=>'',
            'code'=>'',
        ];

        $request->session()->put('user.order_create', $order);

        return $order;
    }
}