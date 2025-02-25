<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuUserController;
use App\Http\Controllers\WebhookMidtrans;

Route::get('/', [MenuUserController::class, 'index'])->name('welcome');


Route::get('/checkout', [MenuUserController::class, 'checkout'])->name('checkout');
Route::post('/add-to-cart', [MenuUserController::class, 'addToCart'])->name('add-to-cart');
Route::get('/get-cart-data', [MenuUserController::class, 'getCartData'])->name('get-cart-data');
Route::post('/update-cart-quantity', [MenuUserController::class, 'updateCartQuantity'])->name('update-cart-quantity');
Route::post('/remove-from-cart', [MenuUserController::class, 'removeFromCart'])->name('remove-from-cart');
Route::post('/checkout', [MenuUserController::class, 'doCheckout'])->name('checkout-post');
Route::post('/save-table', [MenuUserController::class, 'saveTable']);
Route::post('/add-note', [MenuUserController::class, 'addNoteToCart']);


Route::post('/cash-payment', [MenuUserController::class, 'doCashCheckout'])->name('cash-payment');
Route::get('/cash-payment/{orderId}', [MenuUserController::class, 'showInvoice'])->name('cashPayment');

Route::get('/order/{order}/print', [MenuUserController::class, 'printReceipt'])->name('printReceipt');

Route::post('/webhook/payment', [WebhookMidtrans::class, 'payment'])->name('webhook-payment');
// Route::get('/debug', [MenuUserController::class, 'debug'])->name('debug');
