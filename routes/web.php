<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/payments/{event}', [PaymentController::class, 'show'])
    ->name('payments.show');

Route::post('/payments/confirm', [PaymentController::class, 'stripeConfirm'])
    ->name('payments.confirm');

Route::get('/my-events', [PaymentController::class, 'myEvents'])
    ->name('payments.my-events');

    Route::get('/my-events', [PaymentController::class, 'myEvents'])
    ->name('payments.my-events');

    Route::get('/transactions', [PaymentController::class, 'transactionHistory'])
    ->name('payments.transaction-history');