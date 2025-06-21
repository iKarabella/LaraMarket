<?php

namespace App\Http\Controllers\Admin\Warehouses;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Warehouses\StoreWarehouseRequest;
use App\Http\Resources\Admin\Warehouses\WarehouseResource;
use App\Models\Offer;
use App\Models\StockBalance;
use App\Models\Warehouse;
use App\Traits\MarketControllerTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class WarehouseController extends Controller
{
    use MarketControllerTrait;

    public function manage(Request $request)
    {
        return Inertia::render('Admin/Warehouses/Manage', [
            'navigation'=>$this->getNavigation('warehouses'),
            'warehouses'=>Warehouse::all()
        ]);
    }

    public function edit(Request $request, $wh=null)
    {
        if ($wh) $warehouse = Warehouse::whereCode($wh)->firstOrFail();
        else $warehouse = (object)[];
        return Inertia::render('Admin/Warehouses/EditWarehouse', [
            'navigation'=>$this->getNavigation('warehouses'),
            'warehouse' => WarehouseResource::make($warehouse)->resolve(),
        ]);
    }

    public function store(StoreWarehouseRequest $request):RedirectResponse
    {
        Warehouse::whereId($request->id)->firstOrNew()->fill($request->validated())->save();

        return Redirect::route('admin.warehouses.manage');
    }
}
