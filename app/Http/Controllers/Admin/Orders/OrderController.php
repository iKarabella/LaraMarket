<?php

namespace App\Http\Controllers\Admin\Orders;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Orders\OrderEditPositionRequest;
use App\Http\Requests\Admin\Orders\OrdersListRequest;
use App\Http\Requests\Admin\Orders\OrderStoreCommentRequest;
use App\Http\Resources\Admin\Orders\OrderResource;
use App\Http\Resources\EntityValueResource;
use App\Models\EntityValue;
use App\Models\Order;
use App\Models\OrderComment;
use App\Models\Warehouse;
use App\Services\OrderService;
use App\Services\WarehouseService\WarehouseService;
use App\Traits\MarketControllerTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    use MarketControllerTrait;

    public function edit(Request $request, string $uuid)
    {
        $order = Order::whereUuid($uuid)->with(['status_info', 'comments'])->firstOrFail();
        $whs = Warehouse::all(['id', 'title', 'code', 'address', 'phone']);
        $order->body = WarehouseService::writeOffCreate($order, $whs);
        return Inertia::render('Admin/Orders/Order', [
            'navigation'=>$this->getNavigation('orders'),
            'order'=>OrderResource::make($order)->resolve(),
            'warehouses'=>Warehouse::all(),
        ]);
    }

    public function addComment(OrderStoreCommentRequest $request)
    {
        OrderComment::create($request->validated());
    }
    
    public function editPosition(OrderEditPositionRequest $request, string $uuid, OrderService $service)
    {
        $order = Order::whereUuid($uuid)->firstOrFail();

        $service->orderEditPosition($order, $request);
    }

    public function manage(OrdersListRequest $request)
    {
        $validated = $request->validated();

        $orders = Order::with(['status_info']);

        $request->session()->forget('manage.orders.filters');

        $filters = $request->session()->get('manage.orders.filters', [
            'statuses' => EntityValue::whereEntity(2)->get()->map(function($arr){return ['status'=>$arr->id, 'name'=>$arr->value, 'on'=>true];}),
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

        $request->session()->put('manage.orders.filters', $filters);

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

        return Inertia::render('Admin/Orders/Manage', [
            'navigation'=>$this->getNavigation('orders'),
            'orders'=>OrderResource::collection($orders->paginate(30)),
            'filters'=>$filters
        ]);
    }
}
