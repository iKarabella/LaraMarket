<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\PasswordUpdateRequest;
use App\Http\Requests\User\ProfileUpdateRequest;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        return Inertia::render('User/Edit', [
            'user'=>UserResource::make($request->user())->resolve(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): void
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }
        if (intval($request->user()->phone)!=$request->user()->getRawOriginal('phone')) {
            //isDirty не подходит, т.к. с формы приходит +7 формат, а в базе без плюса, чисто инт
            $request->user()->phone_verified_at = null;
        }

        $request->user()->save();
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate(['password' => 'required|current_password']);

        Auth::logout();

        $request->user()->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function passwordUpdate(PasswordUpdateRequest $request):void
    {
        $request->user()->update(['password'=>$request->password]);
    }
}