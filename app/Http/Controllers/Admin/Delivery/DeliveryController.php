<?php

namespace App\Http\Controllers\Admin\Delivery;

use App\Http\Controllers\Controller;
use App\Http\Resources\Admin\Delivery\ShippingResource;
use App\Models\Shipping;
use App\Traits\MarketControllerTrait;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DeliveryController extends Controller
{
    use MarketControllerTrait;

    public function manage(Request $request)
    {
        $shippings = Shipping::where(function($query)use($request){$query->whereNull('courier')->orWhere('courier', '=', $request->user()->id);});

        return Inertia::render('Admin/Delivery/Manage', [
            'navigation'=>$this->getNavigation('delivery'),
            'orders' => ShippingResource::collection($shippings->paginate(1))
        ]);
    }
}
