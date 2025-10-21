<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\GetCartPositionsRequest;
use App\Http\Resources\Catalog\UserCartResource;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Inertia\Inertia;
use Inertia\Response;

class UserCartController extends Controller
{

    public function index(Request $request):Response
    {
        $message = $request->session()->pull('create_order_failed', null);
        return Inertia::render('Catalog/UserCart', [
            'create_order_failed' => $message
        ]);
    }

    /**
     * Список товаров в корзине
     * 
     * @param App\Http\Requests\Catalog\GetCartPositionsRequest $request
     * @return JsonResource
     */
    public function getCartPositions(GetCartPositionsRequest $request): JsonResource
    {
        $offers = Offer::whereIn('id', array_column($request->positions, 'offer'))->with(['product', 'media'])->get();

        return UserCartResource::collection($offers);
    }

    /**
     * Список товаров в корзине
     * 
     * @param App\Http\Requests\Catalog\GetCartPositionsRequest $request
     * @return JsonResource
     */
    public function check(GetCartPositionsRequest $request):JsonResponse
    {
        $offers = Offer::whereIn('id', array_column($request->positions, 'offer'))->get(['id', 'product_id']);
        $cleared = [];

        if (is_array($request->positions)) foreach($request->positions as $position)
        {
            $find = $offers->first(function($arr) use ($position) {
                return $arr->id==$position['offer'];
            });

            if($find && !array_any($cleared, function($arr) use ($find){ return $arr['offer']==$find->id;})){
                $cleared[]=[
                    'position' => $find->product_id,
                    'offer' => $find->id,
                    'quantity' => $find->available < $position['quantity'] ? $find->available : $position['quantity'],
                    'toOrder' => $position['toOrder']??false,
                ];
            }
        }

        return response()->json($cleared);
    }
}
