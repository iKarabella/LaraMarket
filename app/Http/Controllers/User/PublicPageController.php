<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\OrderResource;
use App\Http\Resources\User\UserResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PublicPageController extends Controller
{
    //Различные GET запросы
    public function page(Request $request, string $nick)
    {
        $nick = preg_replace('/[^a-zа-яё0-9-_!]/ui', '',$nick);
        $user = User::whereNickname($nick)->firstOrFail();
        $orders = [];

        if (Auth::check() && $user->id == $request->user()->id) 
        {
            $getOrders = Order::whereUserId($request->user()->id)
                              ->with('status_info')
                              ->orderByDesc('id');
            $orders = OrderResource::collection($getOrders->paginate(25, ['*'], 'orders_page')->withQueryString());
        }

        return Inertia::render('User/PublicPage', [
            'userInfo'  => UserResource::make($user)->resolve(),
            'status' => session('status'),
            'orders' => $orders,
        ]);
    }
}
