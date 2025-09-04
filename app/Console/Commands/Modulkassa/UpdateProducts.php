<?php

namespace App\Console\Commands\Modulkassa;

use App\Models\CashRegister;
use App\Services\ModulKassa\ModulKassa;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'modulkassa:update-products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление товаров в кассовой системе "Модулькасса"';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $modulKassa = new ModulKassa();
        $cashRegisters = CashRegister::whereNotNull('warehouse_id')->get(['cr_id', 'warehouse_id']);
        foreach($cashRegisters->pluck('warehouse_id')->unique() as $wh)
        {
            $offers = DB::table('stock_balances')
                        ->leftJoin('offers', 'offers.id', 'stock_balances.offer_id')
                        ->leftJoin('products', 'products.id', 'offers.product_id')
                        ->where('stock_balances.quantity', '>', 0)
                        ->where('stock_balances.warehouse_id', '=', $wh)
                        ->select([
                            'products.id as product_id',
                            'products.title as product_title',
                            'products.code as product_code',
                            'products.offersign as offersign',
                            'products.measure as product_measure',
                            'offers.id as offer_id',
                            'offers.title as offer_title',
                            'offers.price as price',
                            'offers.barcode as barcode',
                            'offers.weight as weight',
                            'offers.length as length',
                            'offers.width as width',
                            'offers.height as height' 
                        ])
                        ->get();
            
            if ($offers->count()) {
                $guids = $cashRegisters->filter(function($arr)use($wh){return $arr->warehouse_id==$wh;})->pluck('cr_id');
                $modulKassa->catalogChanges($offers, $guids);
            }
        }
    }
}
