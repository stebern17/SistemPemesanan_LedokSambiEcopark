<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuUserController;

Route::get('/', [MenuUserController::class, 'index'])->name('welcome');
Route::get('/checkout', [MenuUserController::class, 'checkout'])->name('checkout');
Route::post('/add-to-cart', [MenuUserController::class, 'addToCart'])->name('add-to-cart');
Route::get('/get-cart-data', [MenuUserController::class, 'getCartData'])->name('get-cart-data');
Route::post('/update-cart-quantity', [MenuUserController::class, 'updateCartQuantity'])->name('update-cart-quantity');
