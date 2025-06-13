<?php

use App\Http\Controllers\Admin\Catalog\CatalogController as AdminCatalogController;
use App\Http\Controllers\Admin\RolesAndPermissions\RolesAndPermissionsController;
use App\Http\Controllers\Admin\UsersManage\UsersManageController;
use App\Http\Controllers\Home\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('permission:users_manage')->group(function () {
    Route::get('admin/users', [UsersManageController::class, 'index'])->name('admin.users.manage');
    Route::post('admin/users', [UsersManageController::class, 'index']);
    Route::patch('/admin/users', [UsersManageController::class, 'store'])->name('admin.users.update');
});

Route::middleware('permission:roles_and_permissions')->group(function () {
    Route::get('admin/roles_and_permissions', [RolesAndPermissionsController::class, 'index'])->name('admin.roles_and_permissions');
    Route::patch('admin/roles_and_permissions', [RolesAndPermissionsController::class, 'store'])->name('admin.roles_and_permissions.update');
    Route::delete('admin/roles_and_permissions', [RolesAndPermissionsController::class, 'delete'])->name('admin.roles_and_permissions.delete');
});

Route::middleware('permission:catalog_manage')->group(function () {
    Route::get('admin/catalog', [AdminCatalogController::class, 'index'])->name('admin.catalog.manage');
    Route::post('admin/catalog', [AdminCatalogController::class, 'storeCat']);
    Route::delete('admin/catalog', [AdminCatalogController::class, 'deleteCat']);
    Route::post('admin/catalog/sorting_category', [AdminCatalogController::class, 'sortingCategory'])->name('admin.catalog.catsSortManage');

    Route::get('admin/catalog/products/edit', [AdminCatalogController::class, 'products'])->name('admin.products.manage');
    Route::post('admin/catalog/products/store', [AdminCatalogController::class, 'storeProduct'])->name('admin.products.store');
});

require __DIR__.'/auth.php';
