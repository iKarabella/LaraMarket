<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
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

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request.phone'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $backto = $request->session()->get('backto', false);
        $request->session()->forget('backto');

        $request->session()->regenerate();

        if($backto) return Redirect::to($backto);
        else return redirect()->route('home');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login'); //перенаправленение при выходе из аккаунта
    }
}
