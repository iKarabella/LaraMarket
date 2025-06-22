<?php

namespace App\Http\Controllers\Admin\Warehouses;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SearchProductRequest;
use App\Http\Requests\Admin\Warehouses\StoreWarehouseReceiptRequest;
use App\Http\Requests\Admin\Warehouses\StoreWarehouseRequest;
use App\Http\Resources\Admin\Catalog\ProductOfferResource;
use App\Http\Resources\Admin\Warehouses\WarehouseResource;
use App\Models\Offer;
use App\Models\StockBalance;
use App\Models\Warehouse;
use App\Models\WarehouseAct;
use App\Traits\MarketControllerTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
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

    public function edit(Request $request, ?string $code=null)
    {
        if ($code) $warehouse = Warehouse::whereCode($code)->firstOrFail();
        else $warehouse = (object)[];
        return Inertia::render('Admin/Warehouses/EditWarehouse', [
            'navigation'=>$this->getNavigation('warehouses'),
            'warehouse' => WarehouseResource::make($warehouse)->resolve(),
            'selectedWh'=>$request->session()->get('admin.manage_warehouses.selectedWh', null)
        ]);
    }

    public function store(StoreWarehouseRequest $request):RedirectResponse
    {
        Warehouse::whereId($request->id)->firstOrNew()->fill($request->validated())->save();

        return Redirect::route('admin.warehouses.manage');
    }

    public function receipt(Request $request, string $code):Response
    {
        $warehouse = Warehouse::whereCode($code)->firstOrFail();
        $request->session()->put('admin.manage_warehouses.selectedWh', $warehouse->id);

        return Inertia::render('Admin/Warehouses/Receipt', [
            'navigation'=>$this->getNavigation('warehouses'),
            'warehouses'=>Warehouse::all(),
            'selectedWh'=>$request->session()->get('admin.manage_warehouses.selectedWh', null)
        ]);
    }

    public function searchProduct(SearchProductRequest $request):array
    {
        $search = DB::table('offers')
                    ->select([
                        'products.title as product_title', 
                        'products.id as product_id', 
                        'offers.id', 
                        'offers.art', 
                        'offers.title',
                        'offers.baseprice',
                        'offers.price',
                        'entity_values.value as measure_val'
                    ])
                    ->leftJoin('products', 'products.id', '=', 'offers.product_id')
                    ->leftJoin('entity_values', 'entity_values.id', '=', 'products.measure')
                    ->whereLike('offers.art', '%'.$request->search.'%')
                    ->orWhereLike('offers.barcode', '%'.$request->search.'%')
                    ->orWhereIn('offers.product_id', DB::table('products')->select('id')->whereLike('title', '%'.$request->search.'%'))
                    ->limit(15)->get();
        return ProductOfferResource::collection($search)->resolve();
    }

    public function storeReceipt(StoreWarehouseReceiptRequest $request)
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
}
