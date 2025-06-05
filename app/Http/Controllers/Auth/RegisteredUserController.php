<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Master;
use App\Models\Apprentice;
use App\Models\UsersSubscriptions;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): Response
    {
        if($request->server('HTTP_REFERER'))
        {
            $checkhost = parse_url($request->server('HTTP_REFERER'));
            
            if($checkhost && $checkhost['host']==env('APP_URL') && $request->server('HTTP_REFERER')!=route('login') && $request->server('HTTP_REFERER')!=route('register'))
            {
                $request->session()->put('backto', $request->server('HTTP_REFERER'));
            }
        }

        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'phone' => 'required|numeric|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'phone' => $request->phone,
            'nickname' => 'user'.rand(1111,9999).User::count(),
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);
        
        return redirect()->route('home'); //return redirect()->route('verification.phone');
    }
}
