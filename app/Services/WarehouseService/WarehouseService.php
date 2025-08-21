<?php

namespace App\Services\WarehouseService;

use App\Models\Offer;
use App\Models\Order;
use App\Models\ReservedProduct;
use App\Models\StockBalance;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WarehouseService
{
    /**
     * Списание товаров со склада
     */
    // public static function writeOff(array $positions, int $warehouse):bool
    // {
    //     try {
    //         DB::transaction(function() use ($positions, $warehouse) {
    //             foreach($positions as $item)
    //             {
    //                 StockBalance::whereWarehouseId($warehouse)->whereOfferId($item['offer_id'])->decrement('quantity', $item['quantity']);
    //             }
    //         });
    //     } catch (Exception $e) {
    //         return false; //throw $e;
    //     }

    //     return true;
    // }

    /**
     * Создание списка товаров из заказа к списанию со склада
     * 
     * @param Order $body тело заказа model Order->body
     * @param Collection $warehouses список складов, для расчета списания
     * @return array $body обновленный список товаров в заказе, с указанием списания со складов
     */
    // public static function writeOffCreate(Order $order, Collection $warehouses)
    // {
    //     $balance = []; //баланс на складах
    //     $writeOffList=[]; //список на списание
    //     $body = $order->body; //состав заказ

    //     $offers = Offer::whereIn('id', array_column($body, 'offer'))->with('stocks')->get();

    //     if($order->reserved_products && $order->reserved_products->count()) 
    //     {
    //         foreach($body as &$position)
    //         {
    //             $inReserved = $order->reserved_products->filter(function($arr) use ($position){
    //                 return $arr->offer_id==$position['offer'];
    //             });

    //             $map = function($arr) use ($warehouses){
    //                 $wh = $warehouses->first(function($w) use ($arr) {return $w->id==$arr->warehouse_id;});
    //                 return [
    //                     'id' => $arr->warehouse_id,
    //                     'code' => $wh->code??null,
    //                     'title' => $wh->title??null,
    //                     'address' => $wh->address??null,
    //                     'quantity' => $arr->quantity
    //                 ];
    //             };
                
    //             $position['writeOffWh'] = $inReserved->map($map)->values()->toArray();
    //             $position['stocks'] = $offers->first(function($o) use ($position){return $o->id==$position['offer'];})->stocks;
    //         }
    //     }
    //     else 
    //     {
    //         foreach($offers->pluck('stocks') as $block){
    //             foreach($block as $stock){
    //                 $balance[$stock->warehouse_id]['whid'] = $stock->warehouse_id;
    //                 $balance[$stock->warehouse_id]['items'][]=[
    //                     'warehouse_id'=>$stock->warehouse_id,
    //                     'offer_id'=>$stock->offer_id,
    //                     'quantity'=>$stock->quantity
    //                 ];
    //             }
    //         }
    
    //         foreach ($body as $position) 
    //         {
    //             $writeOffList[$position['offer']]=[
    //                 'offer_id'=>$position['offer'],
    //                 'writeoff'=>0, 
    //                 'total'=>$position['quantity']
    //             ];
    //         }

    //         usort($balance, function($a, $b){ return count($b['items'])-count($a['items']);});

    //         foreach($balance as $stock)
    //         {
    //             if (!count($stock['items'])) continue;

    //             foreach($stock['items'] as $i)
    //             {
    //                 $position = array_find_key($body, function($arr) use ($i) {
    //                     return $arr['offer'] == $i['offer_id'];
    //                 });

    //                 if($position!==null)
    //                 {
    //                     $whTick = $warehouses->first(function($wh) use ($stock){return $wh->id==$stock['whid'];});

    //                     $toWriteOff = $body[$position]['quantity']-$writeOffList[$body[$position]['offer']]['writeoff'];

    //                     if ($i['quantity']<$toWriteOff) $whq = $i['quantity'];
    //                     else $whq = $toWriteOff;

    //                     $writeOffList[$body[$position]['offer']]['writeoff']+=$whq;

    //                     if($whq>0)
    //                     {
    //                         $body[$position]['writeOffWh'][] = 
    //                         [
    //                             'id' => $i['warehouse_id'], 
    //                             'code' => $whTick->code,
    //                             'title' => $whTick->title,
    //                             'address' => $whTick->address,
    //                             'quantity' => $whq,
    //                         ];
    //                     }
    
    //                     $body[$position]['stocks'] = $offers->first(function($o) use ($body, $position){return $o->id==$body[$position]['offer'];})->stocks;
    //                 }
    //             }
                
    //             $balance = array_map(
    //                 function($arr) use ($writeOffList) 
    //                 {
    //                     $arr['items'] = array_filter($arr['items'], function($a) use ($writeOffList){
    //                         return $writeOffList[$a['offer_id']]['writeoff']<$writeOffList[$a['offer_id']]['total'];
    //                     });
    //                     return $arr;
    //                 }, 
    //                 $balance
    //             );
    //         }
    //     }

    //     return $body;
    // }

    /**
     * Резервирование товаров
     * 
     * @param array $toReserve массив позиций для резервирования
     * @return bool true при успешном резервировании
     * @throws \Exception
     */
    public static function reservation(array $toReserve):bool
    {
        try {
            DB::transaction(function() use ($toReserve) {

                if(!is_array($toReserve)) $toReserve = [$toReserve];

                foreach ($toReserve as $position) 
                {
                    ReservedProduct::create($position->toArray());
                    StockBalance::whereWarehouseId($position->warehouse_id)->whereOfferId($position->offer_id)->decrement('quantity', $position->quantity);
                }
            });
        } catch (Exception $e) {
            throw $e;
        }

        return true;
    }
}
