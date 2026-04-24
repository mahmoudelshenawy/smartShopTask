<?php

use App\Livewire\Cart\Cart;
use App\Livewire\Products\ProductDetails;
use App\Livewire\Products\Products;
use Illuminate\Support\Facades\Route;

Route::get('/', Products::class)->name('home');
Route::get('products', Products::class)->name('products.index');
Route::livewire('/products/{product}', ProductDetails::class)->name('products.show');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('cart', Cart::class)->name('cart.index');
});

Route::view('insight', 'insight')->name('insight');

Route::middleware(['auth', 'verified'])->group(function () {});
require __DIR__.'/settings.php';
