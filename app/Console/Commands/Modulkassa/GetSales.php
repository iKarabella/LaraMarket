<?php

namespace App\Console\Commands\Modulkassa;

use App\Models\CashRegister;
use App\Models\Log;
use App\Models\Offer;
use App\Models\WarehouseAct;
use App\Services\ModulKassa\ModulKassa;
use App\Services\OrderService;
use App\Services\WarehouseService\WarehouseService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GetSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modulkassa:get-sales';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Получить список продаж из системы "Модулькасса" для списания товаров с баланса склада';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modulKassa = new ModulKassa();
        $cashRegisters = CashRegister::whereNotNull('warehouse_id')->get(['cr_id', 'warehouse_id']);

        foreach($cashRegisters as $cashRegister)
        {
            $shifts = $modulKassa->getShifts($cashRegister->cr_id, 10);

            foreach($shifts->reverse() as $shift) 
            {
                $docs = $modulKassa->getCashDocs($cashRegister->cr_id, $shift['id']);
                $acts = WarehouseAct::where('doc_id->system', '=', 'modulkassa')->whereIn('doc_id->id', $docs->pluck('id'))->get(['doc_id']);
                
                $docs = $docs->filter(function($doc) use ($acts){
                    return false === $acts->search(function($act) use ($doc){
                        return $act->doc_id['id']==$doc['id'];
                    });
                });

                $positions=[];

                foreach ($docs as $doc) array_push($positions, ...array_map(function($arr){
                    return $arr['inventCode']; //TODO форматировать inventCode в integer
                }, $doc['inventPositions']));
                
                $offers = Offer::whereIn('temp_field', $positions)->with(['product'])->get(); //TODO заменить temp_field на id
                unset($positions, $doc);

                foreach($docs as $doc) 
                {
                    $positions = array_map(function($arr) use ($offers)
                    {
                        $quantity = $arr['quantity'];
                        $offer = $offers->first(function($o) use ($arr){return $o->temp_field==$arr['inventCode'];}); //TODO заменить temp_field на id
                        
                        if($arr['measure']=='kg') //если кг, то смотрим какая единица у нас на продукте
                        {
                            if($offer->product->measure==1) $quantity=$arr['quantity']*1000;
                        }

                        return [
                            'offer_id'=>$offer->id,
                            'title'=>"{$offer->product->title}, {$offer->title}",
                            'measure_val'=>$offer->product->measure_value->value,
                            'quantity'=>(int) $quantity,
                            'price'=>$arr['price'],
                            'amount'=>$arr['quantity']*$arr['price']
                         ];
                    }, $doc['inventPositions']);

                    $reason = [
                        'system'=>'modulkassa', 
                        'id'=>$doc['id'],
                        'type'=>($doc['docType']=='SALE' ? 'Продажа' : (($doc['docType']=='RETURN_BY_SALE' || $doc['docType'] == 'RETURN_BY_SALE') ? 'Возврат' : 'Неопределен'))
                    ];

                    switch($doc['docType'])
                    {
                        case 'SALE': 
                            if (empty($doc['remoteId']) && empty($doc['linkedDocId'])) WarehouseService::otherWriteOff($positions, 1, $reason); 
                            else {
                                OrderService::orderDelivered((int)(empty($doc['remoteId'])?$doc['linkedDocId']:$doc['remoteId']));
                                Log::create([
                                    'author'=>'getSales',
                                    'message'=>'order?',
                                    'object'=>$doc
                                ]);
                            }
                            break;
                        //case 'RETURN': WarehouseService::returnPositions($positions, 1, $reason); break;
                        case 'RETURN_BY_SALE': WarehouseService::otherWriteOff($positions, 1, $reason, true); break;
                        // case 'ORDER': break; //TODO работа с заказами
                    }
                }
            }
        }
    }
}
