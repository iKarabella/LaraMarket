<?php

namespace App\Services\WarehouseService;

use App\Http\Requests\Admin\Warehouses\StoreWarehouseReceiptRequest;
use App\Models\Offer;
use App\Models\Order;
use App\Models\ReservedProduct;
use App\Models\StockBalance;
use App\Models\StockReserve;
use App\Models\WarehouseAct;
use Exception;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class WarehouseService
{
    /**
     * Списание товаров со склада
     */
    public static function writeOff(array $validated):bool
    {
        if(ReservedProduct::whereOfferId($validated['offer_id'])->whereOrderId($validated['order_id'])->whereProductId($validated['product_id'])->exists()) throw ValidationException::withMessages([
            'product_title' => ["Такое списание уже было произведено"],
        ]);

        try {    
            DB::transaction(function() use ($validated) {            
                ReservedProduct::create([
                    'product_title'=>$validated['product_title'],
                    'order_id'=>$validated['order_id'],
                    'product_id'=>$validated['product_id'],
                    'offer_id'=>$validated['offer_id'],
                    'warehouse_id'=>$validated['warehouse_id'],
                    'quantity'=>$validated['quantity']
                ]);
                StockReserve::whereOrderId($validated['order_id'])->whereOfferId($validated['offer_id'])->delete();
                StockBalance::whereWarehouseId($validated['warehouse_id'])->whereOfferId($validated['offer_id'])->decrement('quantity', $validated['quantity']);
            });
        } catch (Exception $e) {
            return false; //throw $e;
        }

        return true;
    }

    /**
     * Приход на склад
     * 
     * @param StoreWarehouseReceiptRequest $request массив позиций для сохранения
     * @return void
     */
    public static function storeReceipt(StoreWarehouseReceiptRequest $request):void
    {
        DB::transaction(function() use ($request) {
            
            foreach ($request->items as $item) 
            {
                StockBalance::updateOrCreate(
                    [
                        'warehouse_id'=>$request->warehouse, 
                        'offer_id'=>$item['offer_id']
                    ], 
                    [
                        'quantity'=>DB::raw('quantity + '.$item['quantity'])
                    ]
                );

                Offer::whereId($item['offer_id'])->update(['baseprice'=>floatval($item['price'])]);
            }

            WarehouseAct::create(['user_id'=>$request->user()->id, 'warehouse_id'=>$request->warehouse, 'type'=>'receipt', 'act'=>$request->items]);
        });
    }

    public static function storeWriteOff(StoreWarehouseReceiptRequest $request):void
    {
        DB::transaction(function() use ($request) 
        { 
            foreach ($request->items as $item) StockBalance::whereWarehouseId($request->warehouse)
                                                           ->whereOfferId($item['offer_id'])
                                                           ->decrement('quantity', $item['quantity']);                                                           
            WarehouseAct::create([
                'user_id'=>$request->user()->id, 
                'warehouse_id'=>$request->warehouse, 
                'type'=>'write-off', 
                'act'=>$request->items, 
                'comment'=>$request->reason
            ]);
        });
    }

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

    /**
     * Список товаров на складе
     * 
     * @param int $warehouse ID склада из warehouses
     * @param ?string $search поисковая строка
     * @return Builder запрос к БД
     */
    public static function getStockInList(int $warehouse, ?string $search=null):Builder
    {
        $offers = DB::table('offers')
                     ->leftJoin('products', 'products.id', '=', 'offers.product_id')
                     ->leftJoin('entity_values', 'entity_values.id', '=', 'products.measure')
                     ->leftJoin('stock_reserves', 'stock_reserves.offer_id', '=', 'offers.id')
                     ->leftJoin('stock_balances', function($join) use ($warehouse){
                        $join->on('stock_balances.offer_id', '=', 'offers.id')->where('stock_balances.warehouse_id', '=', $warehouse);
                     })
                     ->select([
                        'products.title as product_title', 
                        'products.id as product_id', 
                        'offers.id as offer_id', 
                        'offers.title as offer_title',
                        'offers.art', 
                        'offers.baseprice',
                        'offers.price',
                        'offers.coeff',
                        'offers.visibility',
                        'stock_balances.quantity as stock_in',
                        'stock_reserves.quantity as stock_reserved',
                        'entity_values.value as measure_val'
                     ]);

        if($search){
            $offers->where(function($query) use ($search){
                $query->whereLike('offers.title', '%'.$search.'%')
                      ->orWhereLike('products.title', '%'.$search.'%')
                      ->orWhereLike('offers.art', '%'.$search.'%');
            });
        }

        return $offers;
    }
}
