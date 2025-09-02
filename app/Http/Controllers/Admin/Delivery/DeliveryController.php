<?php

namespace App\Http\Controllers\Admin\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Delivery\DeliveryActionRequest;
use App\Http\Resources\Admin\Delivery\ShippingResource;
use App\Models\OrderComment;
use App\Models\Shipping;
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
            'orders' => ShippingResource::collection($shippings->paginate(1))
        ]);
    }

    public function takeToDelivery(DeliveryActionRequest $request)
    {
        $shipping = Shipping::whereId($request->id)->firstOrFail();
        $comment = 'Принят в доставку со склада <b>"'.$shipping->warehouse_info->code.'</b>". Курьер: <a href="'.route('user.page', [$request->user()->nickname]).'" target="_blank">'.$request->user()->name.' '.$request->user()->surname.'</a>';

        ShippingService::client($shipping->shipping)->takeToDelivery($shipping, $request->user()->id);

        OrderService::orderSent($shipping->order_id, $comment);
    }

    public function delivered(DeliveryActionRequest $request)
    {
        $shipping = Shipping::whereId($request->id)->firstOrFail();

        ShippingService::client($shipping->shipping)->delivered($shipping);
        
        OrderService::orderDelivered($shipping->order_id);
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
