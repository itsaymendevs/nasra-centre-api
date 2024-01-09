<?php

use App\Http\Controllers\StripeController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;



Route::get('/storage-link', function () {
    Artisan::call('storage:link');
});





// 1: Stripe Payment
Route::get('/stripe/{orderNumber}/makePayment', [StripeController::class, 'makePayment'])->name('stripe.makePayment');
Route::get('/stripe/{orderNumber}/confirmPayment', [StripeController::class, 'confirmPayment'])->name('stripe.confirmPayment');


Route::get('/stripe/success', [StripeController::class, 'success'])->name('stripe.success');


// --------------------------------------------------------------------------------------
// --------------------------------------------------------------------------------------






