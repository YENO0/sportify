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

    Route::get('/transactions', [PaymentController::class, 'transactionHistory'])
    ->name('payments.transaction-history');

    Route::post('/payment/send-verification-code', [PaymentController::class, 'sendVerificationCode'])
    ->name('payment.send-verification');
    
    Route::post('/payment/verify-code', [PaymentController::class, 'verifyCode'])
    ->name('payment.verify-code');
    
    Route::post('/payment/check-verification', [PaymentController::class, 'checkVerification'])
    ->name('payment.check-verification');