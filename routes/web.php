<?php

use App\Livewire\Cart\Cart;
use App\Livewire\Products\ProductDetails;
use App\Livewire\Products\Products;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::view('insight', 'insight')->name('insight');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('products', Products::class)->name('products.index');

    Route::livewire('/products/{product}', ProductDetails::class)->name('products.show');

    Route::get('cart', Cart::class)->name('cart.index');
});
require __DIR__.'/settings.php';
