<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\PaymentResultController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/payment/order', [PaymentResultController::class, 'show'])->name('user.payments.result');

Route::get('/.well-known/apple-developer-merchantid-domain-association', function () {
    $path = public_path('.well-known/apple-developer-merchantid-domain-association');
    abort_unless(file_exists($path), 404);
    return response()->file($path, ['Content-Type' => 'text/plain']);
});


 
