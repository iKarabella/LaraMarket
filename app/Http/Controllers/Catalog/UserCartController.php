<?php

namespace App\Http\Controllers\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Catalog\GetCartPositionsRequest;
use App\Http\Resources\Catalog\UserCartResource;
use App\Models\Offer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class UserCartController extends Controller
{

    public function getCartPositions(GetCartPositionsRequest $request): JsonResource
    {
        $offers = Offer::whereIn('id', array_column($request->positions, 'offer'))->with(['product', 'media', 'stocks'])->get();

        return UserCartResource::collection($offers);
    }

    public function check(GetCartPositionsRequest $request):JsonResponse
    {
        $offers = Offer::whereIn('id', array_column($request->positions, 'offer'))->with(['stocks'])->has('stocks')->get(['id']);
        $cleared = [];
        foreach($request->positions as $position)
        {
            $find = $offers->first(function($arr) use ($position) {
                return $arr->id==$position['offer'];
            });

            if($find){
                $quantity = $find->stocks->max('quantity');
                $cleared[]=[
                    'position' => $position['position'],
                    'offer' => $position['offer'],
                    'quantity' => $quantity<$position['quantity'] ? $quantity : $position['quantity'],
                    'toOrder' => $position['toOrder'],
                ];
            }
        }

        return response()->json($cleared);
    }
}
