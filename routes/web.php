<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\PaymentResultController;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/payment/order', [PaymentResultController::class, 'show'])->name('user.payments.result'); 
