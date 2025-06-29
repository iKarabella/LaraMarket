<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PublicPageController extends Controller
{
    //Различные GET запросы
    public function page(Request $request, string $nick)
    {
        dd('user.page');
        $nick = preg_replace('/[^a-zа-яё0-9-_!]/ui', '',$nick);
        
        $user = User::whereLogin($nick)->with(['avatar','achievements'])->firstOrFail();
        
        //
    }
}
