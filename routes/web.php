<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StoresController;
use App\Http\Controllers\ExportsController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\OAuth\ShopifyOAuthController;
use App\Http\Controllers\StoreConnectionController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::view('/dashboard', 'dashboard')->name('dashboard');

    Route::get('/stores', [StoresController::class, 'index'])->name('stores.index');
    Route::get('/stores/fragment', [StoresController::class, 'fragment'])->name('stores.fragment');
    Route::get('/stores/create', [StoresController::class, 'create'])->name('stores.create');
    Route::post('/stores', [StoresController::class, 'store'])->name('stores.store');

    Route::post('/stores/{store}/exports', [ExportsController::class, 'store'])->name('exports.store');

    Route::get('/stores/{store}/products', [ProductsController::class, 'index'])->name('products.index');
    Route::get('/stores/{store}/products/fragment', [ProductsController::class, 'fragment'])->name('products.fragment');

    Route::get('/stores/{store}/orders', [OrdersController::class, 'index'])->name('orders.index');
    Route::get('/stores/{store}/orders/fragment', [OrdersController::class, 'fragment'])->name('orders.fragment');

    Route::get('/oauth/shopify/{store}/redirect', [ShopifyOAuthController::class, 'redirect'])->name('oauth.shopify.redirect');
    Route::get('/oauth/shopify/{store}/callback', [ShopifyOAuthController::class, 'callback'])->name('oauth.shopify.callback');

    Route::get('/stores/{store}/connect/woocommerce', [StoreConnectionController::class, 'showWooForm'])->name('stores.connect.woocommerce');
    Route::post('/stores/{store}/connect/woocommerce', [StoreConnectionController::class, 'storeWooCredentials'])->name('stores.connect.woocommerce.store');

});

require __DIR__.'/auth.php';