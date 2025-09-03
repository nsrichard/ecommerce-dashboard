<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

    Route::get('/stores', [\App\Http\Controllers\StoresController::class, 'index'])->name('stores.index');
    Route::get('/stores/fragment', [\App\Http\Controllers\StoresController::class, 'fragment'])->name('stores.fragment');
    Route::get('/stores/create', [\App\Http\Controllers\StoresController::class, 'create'])->name('stores.create');
    Route::post('/stores', [\App\Http\Controllers\StoresController::class, 'store'])->name('stores.store');

    Route::get('/stores/{store}/products', [\App\Http\Controllers\ProductsController::class, 'index'])->name('products.index');
    Route::get('/stores/{store}/orders', [\App\Http\Controllers\OrdersController::class, 'index'])->name('orders.index');

    Route::post('/stores/{store}/exports', [\App\Http\Controllers\ExportsController::class, 'store'])->name('exports.store');

});

require __DIR__.'/auth.php';