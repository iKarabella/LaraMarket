<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\OrdersResource;
use App\Http\Resources\User\UserResource;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicPageController extends Controller
{
    //Различные GET запросы
    public function page(Request $request, string $nick)
    {
        $nick = preg_replace('/[^a-zа-яё0-9-_!]/ui', '',$nick);
        $user = User::whereNickname($nick)->firstOrFail();

        $orders = Order::whereUserId($request->user()->id)->with('status_info')->orderByDesc('id');
        $user = User::whereId($request->user()->id)->firstOrFail();
        
        return Inertia::render('User/PublicPage', [
            'userInfo'  => UserResource::make($user)->resolve(),
            'status' => session('status'),
            'orders' => OrdersResource::collection($orders->paginate(25, ['*'], 'page')->withQueryString()),
        ]);
    }
}
