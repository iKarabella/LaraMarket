<?php

use App\Http\Controllers\Admin\Catalog\CatalogController as AdminCatalogController;
use App\Http\Controllers\Admin\Catalog\ProductMediaController as AdminProductMediaController;
use App\Http\Controllers\Admin\Orders\OrderStatusController;
use App\Http\Controllers\Admin\Orders\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\RolesAndPermissions\RolesAndPermissionsController;
use App\Http\Controllers\Admin\UsersManage\UsersManageController;
use App\Http\Controllers\Admin\Warehouses\WarehouseController;
use App\Http\Controllers\Admin\Warehouses\WarehouseOrdersController;
use App\Http\Controllers\Catalog\CatalogController;
use App\Http\Controllers\Order\OrderController;
use App\Http\Controllers\Catalog\ProductController;
use App\Http\Controllers\Catalog\UserCartController;
use App\Http\Controllers\Home\HomeController;
use App\Http\Controllers\User\PublicPageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/@{nick}', [PublicPageController::class, 'page'])->name('user.page');

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

Route::middleware('permission:orders_manage')->group(function () {
    Route::get('admin/orders', [AdminOrderController::class, 'manage'])->name('admin.orders.manage');
    Route::post('admin/orders', [AdminOrderController::class, 'manage']);
    Route::post('admin/orders/set_status/waitingPayment', [OrderStatusController::class, 'waitingPayment'])->name('admin.order.waitingPayment');
    Route::post('admin/orders/set_status/cancel', [OrderStatusController::class, 'cancel'])->name('admin.order.cancel');
    Route::post('admin/orders/set_status/orderToAssembly', [OrderStatusController::class, 'orderToAssembly'])->name('admin.order.toAssembly');
    Route::post('admin/order/{uuid}/editPosition', [AdminOrderController::class, 'editPosition'])->name('admin.order.editPosition');
    Route::get('admin/order/{uuid}', [AdminOrderController::class, 'edit'])->name('admin.order.manage');
    Route::post('admin/order/{uuid}/add_сomment', [AdminOrderController::class, 'addComment'])->name('admin.order.addComment');
    Route::post('admin/order/{uuid}/set_warehouse', [AdminOrderController::class, 'setWarehouse'])->name('admin.order.setWarehouse');
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
    Route::get('admin/warehouses/{code}/edit', [WarehouseController::class, 'edit'])->name('admin.warehouses.edit');
    Route::get('admin/warehouses/{code}/receipt', [WarehouseController::class, 'receipt'])->name('admin.warehouses.receipt');
    Route::get('admin/warehouses/{code}/orders', [WarehouseOrdersController::class, 'manage'])->name('admin.warehouses.orders');
    Route::post('admin/warehouses/{code}/orders', [WarehouseOrdersController::class, 'manage']);
    Route::get('admin/warehouses/{code}/orders/{uuid}', [WarehouseOrdersController::class, 'order'])->name('admin.warehouses.order');
    Route::post('admin/warehouses/{code}/orders/{uuid}/markWh', [WarehouseOrdersController::class, 'markWh'])->name('admin.warehouses.order.markWh');
    Route::post('admin/warehouses/{code}/orders/{uuid}/readyForPickup', [WarehouseOrdersController::class, 'readyForPickup'])->name('admin.warehouses.order.readyForPickup');
    Route::post('admin/warehouses/{code}/orders/{uuid}/sent', [WarehouseOrdersController::class, 'orderSent'])->name('admin.warehouses.order.sent');
    Route::post('admin/warehouses/{code}/orders/{uuid}/issued', [WarehouseOrdersController::class, 'orderIssued'])->name('admin.warehouses.order.issued');
});

require __DIR__.'/auth.php';

