<?php

use App\Http\Controllers\Admin\Catalog\CatalogController as AdminCatalogController;
use App\Http\Controllers\Admin\Catalog\ProductMediaController as AdminProductMediaController;
use App\Http\Controllers\Admin\RolesAndPermissions\RolesAndPermissionsController;
use App\Http\Controllers\Admin\UsersManage\UsersManageController;
use App\Http\Controllers\Admin\Warehouses\WarehouseController;
use App\Http\Controllers\Catalog\CatalogController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Catalog\ProductController;
use App\Http\Controllers\Catalog\UserCartController;
use App\Http\Controllers\Home\HomeController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('catalog/{category?}', [CatalogController::class, 'index'])->name('catalog');
Route::get('products/{code?}', [ProductController::class, 'index'])->name('catalog.product');
Route::post('catalog/actions/notifyAboutAdmission', [CatalogController::class, 'notifyAboutAdmission'])->name('catalog.notifyAboutAdmission');
Route::get('cart', [UserCartController::class, 'index'])->name('user.cart');
Route::post('cart/get_positions', [UserCartController::class, 'getCartPositions'])->name('catalog.usercart.getPosition');
Route::post('cart/check', [UserCartController::class, 'check'])->name('catalog.usercart.check');

Route::get('orders/new', [OrderController::class, 'create'])->name('order.create');
Route::post('orders/new', [OrderController::class, 'create']);
Route::post('orders/store', [OrderController::class, 'store'])->name('order.store');
Route::get('order/{uuid}', [OrderController::class, 'show'])->name('order.show');


Route::middleware('permission:users_manage')->group(function () {
    Route::get('admin/users', [UsersManageController::class, 'index'])->name('admin.users.manage');
    Route::post('admin/users', [UsersManageController::class, 'index']);
    Route::patch('admin/users', [UsersManageController::class, 'store'])->name('admin.users.update');
});

Route::middleware('permission:roles_and_permissions')->group(function () {
    Route::get('admin/roles_and_permissions', [RolesAndPermissionsController::class, 'index'])->name('admin.roles_and_permissions');
    Route::patch('admin/roles_and_permissions', [RolesAndPermissionsController::class, 'store'])->name('admin.roles_and_permissions.update');
    Route::delete('admin/roles_and_permissions', [RolesAndPermissionsController::class, 'delete'])->name('admin.roles_and_permissions.delete');
});

Route::middleware('permission:catalog_manage')->group(function () {
    Route::get('admin/catalog', [AdminCatalogController::class, 'index'])->name('admin.catalog.manage');
    Route::post('admin/catalog', [AdminCatalogController::class, 'index']);
    Route::post('admin/catalog/categories', [AdminCatalogController::class, 'storeCat'])->name('admin.catalog.catsManage');
    Route::delete('admin/catalog/categories', [AdminCatalogController::class, 'deleteCat']);
    Route::post('admin/catalog/categories/sorting_category', [AdminCatalogController::class, 'sortingCategory'])->name('admin.catalog.catsSortManage');

    Route::get('admin/catalog/product/edit', [AdminCatalogController::class, 'products'])->name('admin.products.manage');
    Route::get('admin/catalog/product/edit/{code}', [AdminCatalogController::class, 'products'])->name('admin.products.edit');
    Route::post('admin/catalog/product/store', [AdminCatalogController::class, 'storeProduct'])->name('admin.products.store');
    Route::post('admin/catalog/product/offers/store', [AdminCatalogController::class, 'storeOffer'])->name('admin.products.offers.store');
    Route::get('admin/catalog/products/{code}/offers/{offer_id}', [AdminCatalogController::class, 'offer'])->name('admin.products.editOffer');
    Route::get('admin/catalog/products/{code}/offers/', [AdminCatalogController::class, 'offer'])->name('admin.products.newOffer');

    Route::post('admin/catalog/media/store', [AdminProductMediaController::class, 'storeProductMedia'])->name('admin.products.media');
    Route::post('admin/catalog/media/sort', [AdminProductMediaController::class, 'storeProductMediaSorting'])->name('admin.products.media.sorting');
    Route::post('admin/catalog/media/remove', [AdminProductMediaController::class, 'removeProductMedia'])->name('admin.products.media.remove');
});

Route::middleware('permission:warehouses_manage')->group(function () {
    Route::post('admin/warehouse/store', [WarehouseController::class, 'store'])->name('admin.warehouses.store');
    Route::post('admin/warehouse/search_product', [WarehouseController::class, 'searchProduct'])->name('admin.warehouses.searchProduct');
    Route::get('admin/warehouse/new', [WarehouseController::class, 'edit'])->name('admin.warehouses.new');
    Route::put('admin/warehouse/storeReceipt', [WarehouseController::class, 'storeReceipt'])->name('admin.warehouse.storeReceipt');
    Route::get('admin/warehouses/{code?}', [WarehouseController::class, 'manage'])->name('admin.warehouses.manage');
    Route::get('admin/warehouses/{code?}/edit', [WarehouseController::class, 'edit'])->name('admin.warehouses.edit');
    Route::get('admin/warehouses/{code}/receipt', [WarehouseController::class, 'receipt'])->name('admin.warehouses.receipt');
});

require __DIR__.'/auth.php';

