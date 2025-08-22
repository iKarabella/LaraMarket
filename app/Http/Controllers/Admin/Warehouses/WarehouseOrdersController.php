<?php

namespace App\Http\Controllers\Admin\Warehouses;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SearchProductRequest;
use App\Http\Requests\Admin\Warehouses\OrderMarkWhRequest;
use App\Http\Requests\Admin\Warehouses\OrdersListRequest;
use App\Http\Requests\Admin\Warehouses\StoreWarehouseReceiptRequest;
use App\Http\Requests\Admin\Warehouses\StoreWarehouseRequest;
use App\Http\Resources\Admin\Catalog\ProductOfferResource;
use App\Http\Resources\Admin\Warehouses\OrderResource;
use App\Http\Resources\Admin\Warehouses\WarehouseResource;
use App\Models\EntityValue;
use App\Models\Offer;
use App\Models\Order;
use App\Models\ReservedProduct;
use App\Models\StockBalance;
use App\Models\StockReserve;
use App\Models\Warehouse;
use App\Models\WarehouseAct;
use App\Services\OrderService;
use App\Services\WarehouseService\WarehouseService;
use App\Traits\MarketControllerTrait;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class WarehouseOrdersController extends Controller
{
    use MarketControllerTrait;

    public function manage(OrdersListRequest $request, ?string $code=null)
    {
        $orders = Order::with(['status_info']);

        if ($code) 
        {
            $warehouse = Warehouse::whereCode($code)->firstOrFail();
            $request->session()->put('admin.manage_warehouses.selectedWh', $warehouse->id);
            $orders->whereWarehouseId($warehouse->id);
        }

        $validated = $request->validated();

        $filters = $request->session()->get('whmanage.orders.filters', [
            'statuses' => EntityValue::whereIn('id', [8,10])->get()->map(function($arr){return ['status'=>$arr->id, 'name'=>$arr->value, 'on'=>true];}),
            'dates' => [new Carbon()->startOfWeek(), new Carbon()->endOfDay()],
            'sortDesc' => false
        ]);

        if(isset($validated['filters']['statuses'])) 
        {
            if(array_any($validated['filters']['statuses'], function($a){return $a['on']==true;}))
            {
                $filters['statuses'] = $filters['statuses']->map(function($s) use ($validated){
                    $s['on'] = array_find($validated['filters']['statuses'], function($f) use ($s){return $f['status']==$s['status'];})['on'];
                    return $s;
                });
            }
        }
        if(isset($validated['filters']['dates'])) $filters['dates'] = $validated['filters']['dates'];
        if(isset($validated['filters']['sortDesc'])) $filters['sortDesc'] = $validated['filters']['sortDesc'];

        $request->session()->put('whmanage.orders.filters', $filters);

        if($filters['statuses']) $orders->whereIn('status', $filters['statuses']->filter(function($f){return $f['on'];})->pluck('status'));
        if(isset($filters['dates']) && count($filters['dates'])) 
        {
            if ($filters['dates'][0]) $orders->where('created_at', '>', new Carbon($filters['dates'][0])->startOfDay());
            if ($filters['dates'][1]) $orders->where('created_at', '<', new Carbon($filters['dates'][1])->endOfDay());
        }
        if(isset($filters['sortDesc'])) 
        {
            if($filters['sortDesc']) $orders->orderByDesc('created_at');
            else $orders->orderBy('created_at');
        }
        else $orders->orderBy('created_at');

        return Inertia::render('Admin/Warehouses/Orders', [
            'navigation'=>$this->getNavigation('warehouses'),
            'warehouses'=>Warehouse::all(),
            'selectedWh'=>$request->session()->get('admin.manage_warehouses.selectedWh', null),
            'orders'=>OrderResource::collection($orders->paginate(30)),
            'filters'=>$filters
        ]);
    }

    public function order(Request $request, string $code, string $uuid)
    {
        $order = Order::whereUuid($uuid)->with(['status_info', 'comments', 'reserved_products'])->firstOrFail();

        $warehouses = Warehouse::all();
        $warehouse = $warehouses->firstOrFail(function($arr) use ($code){return $arr->code==$code;});
        $request->session()->put('admin.manage_warehouses.selectedWh', $warehouse->id);

        return Inertia::render('Admin/Warehouses/Order', [
            'navigation'=>$this->getNavigation('warehouses'),
            'warehouses'=>Warehouse::all(),
            'selectedWh'=>$request->session()->get('admin.manage_warehouses.selectedWh', null),
            'order'=>OrderResource::make($order)->resolve(),
        ]);
    }

    public function markWh(OrderMarkWhRequest $request, string $code, string $uuid)
    {
        WarehouseService::writeOff($request->validated());
    }

    public function readyForPickup(Request $request)
    {
        $order = Order::whereId($request->order_id)->with(['reserved_products', 'status_info'])->firstOrFail();
        if (OrderService::checkCompleted($order)) OrderService::setStatus($order, 10);
    }

    public function orderSent(Request $request)
    {
        $order = Order::whereId($request->order_id)->with(['reserved_products', 'status_info'])->firstOrFail();

        if(OrderService::checkCompleted($order) && in_array($order->status, [8,10])) OrderService::setStatus($order, 9);
    }

    public function orderIssued(Request $request)
    {
        $order = Order::whereId($request->order_id)->with(['reserved_products', 'status_info'])->firstOrFail();
        if(OrderService::checkCompleted($order) && in_array($order->status, [10])) OrderService::setStatus($order, 12);
    }
}