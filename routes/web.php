<?php

use App\Http\Controllers\Admin\UsersManage\UsersManageController;
use App\Http\Controllers\Home\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


// Route::middleware('permission:users_manage')->group(function () {
    Route::get('admin/users', [UsersManageController::class, 'index'])->name('admin.users.manage');
// });

require __DIR__.'/auth.php';
