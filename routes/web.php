<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/payments/{event}', [PaymentController::class, 'show'])
    ->name('payments.show');

Route::post('/payments/stripe', [PaymentController::class, 'stripePay'])
    ->name('payments.stripe');

Route::post('/payments/confirm', [PaymentController::class, 'stripeConfirm'])
    ->name('payments.confirm');

Route::get('/payments/success/{payment}', [PaymentController::class, 'success'])
    ->name('payments.success');

