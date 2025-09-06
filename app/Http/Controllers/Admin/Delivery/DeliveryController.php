<?php

namespace App\Http\Controllers\Admin\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Delivery\DeliveryActionRequest;
use App\Http\Resources\Admin\Delivery\ShippingResource;
use App\Models\CashRegister;
use App\Models\Order;
use App\Models\OrderComment;
use App\Models\Shipping;
use App\Services\ModulKassa\ModulKassa;
use App\Services\OrderService;
use App\Services\Shipping\ShippingService;
use App\Traits\MarketControllerTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DeliveryController extends Controller
{
    use MarketControllerTrait;

    public function manage(Request $request):Response
    {
        $shippings = Shipping::where(function($query)use($request){$query->whereNull('courier')->orWhere('courier', '=', $request->user()->id);})
                             ->with(['warehouse_info', 'order_info']);

        return Inertia::render('Admin/Delivery/Manage', [
            'navigation'=>$this->getNavigation('delivery'),
            'orders' => ShippingResource::collection($shippings->paginate(30))
        ]);
    }

    public function takeToDelivery(DeliveryActionRequest $request)
    {
        $shipping = Shipping::whereId($request->id)->with(['order_info'])->firstOrFail();
        $courier = empty($request->user()->name) ? $request->user()->nickname : $request->user()->name.' '.$request->user()->surname;
        $comment = 'Принят в доставку со склада <b>"'.$shipping->warehouse_info->code.'</b>". Курьер: <a href="'.route('user.page', [$request->user()->nickname]).'" target="_blank">'.$courier.'</a>';

        $cashRegisters = CashRegister::whereUserId($request->user()->id)->get(['cr_id'])->pluck('cr_id');
        
        ShippingService::client($shipping->shipping)->takeToDelivery($shipping, $request->user()->id);

        (new ModulKassa())->sendOrder($cashRegisters, $shipping->order_info);

        OrderService::orderSent($shipping->order_info, $comment);
    }

    public function delivered(DeliveryActionRequest $request)
    {
        $shipping = Shipping::whereId($request->id)->firstOrFail();

        ShippingService::client($shipping->shipping)->delivered($shipping);
        
        OrderService::orderDelivered($shipping->order_id);

        (new ModulKassa())->cancelOrder($shipping->order_id);
    }

    public function cancelled(DeliveryActionRequest $request)
    {
        dd($request->toArray());
    }

    public function addComment(DeliveryActionRequest $request)
    {
        $shipping = Shipping::whereId($request->id)->firstOrFail();
        $title = '<a href="'.route('user.page', [$request->user()->nickname]).'" target="_blank">'.$request->user()->name.' '.$request->user()->surname.'</a> оставил комментарий.';

        OrderService::addComment($shipping->order_id, $title, $request->comment, false);
    }
}
