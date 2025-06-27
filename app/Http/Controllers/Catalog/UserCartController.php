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

    public function getCartPositions(GetCartPositionsRequest $request): JsonResource
    {
        $offers = Offer::whereIn('id', array_column($request->positions, 'offer'))->with(['product', 'media'])->get();

        return UserCartResource::collection($offers);
    }

    public function check(GetCartPositionsRequest $request):JsonResponse
    {
        $offers = Offer::whereIn('id', array_column($request->positions, 'offer'))->get(['id']);
        $cleared = [];
        foreach($request->positions as $position)
        {
            $find = $offers->first(function($arr) use ($position) {
                return $arr->id==$position['offer'];
            });

            if($find){
                $cleared[]=[
                    'position' => $position['position'],
                    'offer' => $position['offer'],
                    'quantity' => $find->available<$position['quantity'] ? $find->available : $position['quantity'],
                    'toOrder' => $position['toOrder'],
                ];
            }
        }

        return response()->json($cleared);
    }
}
