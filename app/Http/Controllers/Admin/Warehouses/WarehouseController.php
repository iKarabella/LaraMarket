<?php

namespace App\Http\Controllers\Admin\Warehouses;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SearchProductRequest;
use App\Http\Requests\Admin\Warehouses\PriceTagsPrintRequest;
use App\Http\Requests\Admin\Warehouses\PriceTagsRequest;
use App\Http\Requests\Admin\Warehouses\StockInListRequest;
use App\Http\Requests\Admin\Warehouses\StoreWarehouseReceiptRequest;
use App\Http\Requests\Admin\Warehouses\StoreWarehouseRequest;
use App\Http\Resources\Admin\Catalog\ProductOfferResource;
use App\Http\Resources\Admin\Warehouses\WarehouseActResource;
use App\Http\Resources\Admin\Warehouses\WarehouseResource;
use App\Http\Resources\Admin\Warehouses\WarehouseStocksInResource;
use App\Models\CashRegister;
use App\Models\Offer;
use App\Models\Warehouse;
use App\Models\WarehouseAct;
use App\Services\ModulKassa\ModulKassa;
use App\Services\WarehouseService\Requests\SetCashRegistersRequest;
use App\Services\WarehouseService\WarehouseService;
use App\Traits\MarketControllerTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class WarehouseController extends Controller
{
    use MarketControllerTrait;

    public function manage(Request $request, ?string $code=null)
    {
        if ($code) 
        {
            $warehouse = Warehouse::whereCode($code)->firstOrFail();
            $request->session()->put('admin.manage_warehouses.selectedWh', $warehouse->id);
        }

        return Inertia::render('Admin/Warehouses/Manage', [
            'navigation'=>$this->getNavigation('warehouses'),
            'warehouses'=>Warehouse::all(),
            'selectedWh'=>$request->session()->get('admin.manage_warehouses.selectedWh', null)
        ]);
    }

    public function priceTags(PriceTagsRequest $request)
    {
        if($request->createFromReceipt)
        {
            $act = WarehouseAct::whereId($request->createFromReceipt)->first();
            $offers = Offer::whereIn('id', array_column($act->act, 'offer_id'))->with('product')->get();
            $positions = $offers->map(function($offer){
                return [
                    'offer_id'=>$offer->id,
                    'product_id'=>$offer->product->id,
                    'price'=>number_format($offer->price/100, 2, '.', ""),
                    'barcode'=>$offer->barcode,
                    'title'=>"{$offer->product->title}, {$offer->title}",
                    'measure'=>$offer->product->measure_value->value,
                    'quantity'=>1
                ];
            });
        }
        else $positions = [];

        return Inertia::render('Admin/Warehouses/PriceTags', [
            'navigation'=>$this->getNavigation('warehouses'),
            'warehouses'=>Warehouse::all(),
            'selectedWh'=>$request->session()->get('admin.manage_warehouses.selectedWh', null),
            'positions'=>$positions
        ]);
    }

    public function printPriceTags(PriceTagsPrintRequest $request)
    {
        $positions = [];

        foreach($request->positions as $item)
        {
            $add = [
                'title'=>$item['title'],
                'price'=>$item['price'],
                'offer_id'=>$item['offer_id']
            ];

            for ($i=0; $i<$item['quantity']; $i++) $positions[]=$add;
        }

        return view('PrintPriceTipsPages.'.$request->format, ['positions' => $positions, 'companyName'=>env('COMPANY_NAME_SHORT', '')]);
    }

    public function edit(Request $request, ?string $code=null)
    {
        if ($code) $warehouse = Warehouse::whereCode($code)->with('cash_registers')->firstOrFail();
        else $warehouse = (object)[];
        
        return Inertia::render('Admin/Warehouses/EditWarehouse', [
            'navigation'=>$this->getNavigation('warehouses'),
            'warehouse' => WarehouseResource::make($warehouse)->resolve(),
            'selectedWh'=>$request->session()->get('admin.manage_warehouses.selectedWh', null),
            'filters'=>[
                'search'=>$request->search,
                'page'=>$request->page
            ],
        ]);
    }

    public function deleteCashRegister(Request $request, string $code)
    {
        CashRegister::whereWarehouseId($request->warehouse_id)->whereCrId($request->guid)->delete();
    }

    public function getCashRegisters()
    {
        $usedCashRegisters = CashRegister::whereNotNull('warehouse_id')->get(['cr_id']);

        return (new ModulKassa())->getRetailPoints()->filter(function($point) use ($usedCashRegisters){
            return $usedCashRegisters->doesntContain(function($a) use ($point){return $a->cr_id==$point['id'];});
        });
    }

    public function storeCashRegisters(SetCashRegistersRequest $request, string $code)
    {        
        WarehouseService::setCashRegisters($request);
    }

    public function store(StoreWarehouseRequest $request):RedirectResponse
    {
        Warehouse::whereId($request->id)->firstOrNew()->fill($request->validated())->save();

        return Redirect::route('admin.warehouses.manage');
    }

    public function newReceipt(Request $request, string $code):Response
    {
        $warehouse = Warehouse::whereCode($code)->firstOrFail();
        $request->session()->put('admin.manage_warehouses.selectedWh', $warehouse->id);

        return Inertia::render('Admin/Warehouses/NewReceipt', [
            'navigation'=>$this->getNavigation('warehouses'),
            'warehouses'=>Warehouse::all(),
            'selectedWh'=>$request->session()->get('admin.manage_warehouses.selectedWh', null)
        ]);
    }

    public function receipt(Request $request, string $code):Response
    {
        $warehouse = Warehouse::whereCode($code)->firstOrFail();
        $request->session()->put('admin.manage_warehouses.selectedWh', $warehouse->id);
        $acts = WarehouseAct::whereWarehouseId($warehouse->id)->whereType('receipt')->with('user')->orderByDesc('created_at');

        return Inertia::render('Admin/Warehouses/Receipt', [
            'navigation'=>$this->getNavigation('warehouses'),
            'warehouses'=>Warehouse::all(),
            'selectedWh'=>$request->session()->get('admin.manage_warehouses.selectedWh', null),
            'acts'=>WarehouseActResource::collection($acts->paginate(30)),
            'offersEditable'=>access_rights('catalog_manage')
        ]);
    }

    public function writeOff(Request $request, string $code):Response
    {
        $warehouse = Warehouse::whereCode($code)->firstOrFail();
        $request->session()->put('admin.manage_warehouses.selectedWh', $warehouse->id);
        $acts = WarehouseAct::whereWarehouseId($warehouse->id)->whereType('write-off')->with('user')->orderByDesc('created_at');

        return Inertia::render('Admin/Warehouses/WriteOff', [
            'navigation'=>$this->getNavigation('warehouses'),
            'warehouses'=>Warehouse::all(),
            'selectedWh'=>$request->session()->get('admin.manage_warehouses.selectedWh', null),
            'acts'=>WarehouseActResource::collection($acts->paginate(30))
        ]);
    }

    public function newWriteOff(Request $request, string $code):Response
    {
        $warehouse = Warehouse::whereCode($code)->firstOrFail();
        $request->session()->put('admin.manage_warehouses.selectedWh', $warehouse->id);

        return Inertia::render('Admin/Warehouses/NewWriteOff', [
            'navigation'=>$this->getNavigation('warehouses'),
            'warehouses'=>Warehouse::all(),
            'selectedWh'=>$request->session()->get('admin.manage_warehouses.selectedWh', null)
        ]);
    }

    public function stockIn(StockInListRequest $request, string $code):Response
    {
        $warehouse = Warehouse::whereCode($code)->firstOrFail();
        $request->session()->put('admin.manage_warehouses.selectedWh', $warehouse->id);
        $perPage = 50;

        $offers = WarehouseService::getStockInList($warehouse->id, $request->search);

        if ($offers->count()/$perPage < $request->page) $request->merge(['page' => 1]);

        return Inertia::render('Admin/Warehouses/StockIn', [
            'navigation'=>$this->getNavigation('warehouses'),
            'warehouses'=>Warehouse::all(),
            'selectedWh'=>$request->session()->get('admin.manage_warehouses.selectedWh', null),
            'stocks'=>WarehouseStocksInResource::collection($offers->paginate($perPage)),
            'filters'=>[
                'search'=>$request->search,
                'page'=>$request->page
            ],
            'brief_summary'=>WarehouseService::briefSummary($warehouse->id),
        ]);
    }

    public function searchProduct(SearchProductRequest $request):array
    {
        $select = [
            'products.title as product_title', 
            'products.id as product_id', 
            'offers.id', 
            'offers.art', 
            'offers.title',
            'offers.baseprice',
            'offers.price',
            'offers.coeff',
            'entity_values.value as measure_val'
        ];

        $search = DB::table('offers')
                    ->leftJoin('products', 'products.id', '=', 'offers.product_id')
                    ->leftJoin('entity_values', 'entity_values.id', '=', 'products.measure')
                    ->where(function($query) use ($request){
                        $query->whereLike('offers.art', '%'.$request->search.'%')
                              ->orWhereLike('offers.barcode', '%'.$request->search.'%')
                              ->orWhereIn('offers.product_id', DB::table('products')->select('id')->whereLike('title', '%'.$request->search.'%'));
                    });

        if($request->warehouse) {
            $search->leftJoin('stock_balances', function($join) use ($request){
                $join->on('stock_balances.offer_id', '=', 'offers.id')->where('stock_balances.warehouse_id', '=', $request->warehouse);
            });
            $select[]='stock_balances.quantity as quantity';
        }

        $search->select($select)->limit(15);

        return ProductOfferResource::collection($search->get())->resolve();
    }

    public function storeReceipt(StoreWarehouseReceiptRequest $request)
    {
        WarehouseService::storeReceipt($request);
    }

    public function storeWriteOff(StoreWarehouseReceiptRequest $request)
    {
        WarehouseService::storeWriteOff($request);
    }
}
