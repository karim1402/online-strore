<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentResultController extends Controller
{
    public function show(Request $request)
    {
        $success = $request->query('success') === 'true';
        $transactionId = $request->query('id');
        $amountCents = $request->query('amount_cents');
        $currency = $request->query('currency');
        $orderId = $request->query('order'); // This is the Paymob Order ID
        $errorMessage = $request->query('data_message') ?? $request->input('data.message') ?? 'Transaction Failed';

        // Check if pending (sometimes pending means wait for callback)
        $isPending = $request->query('pending') === 'true';

        return view('payment.result', [
            'success' => $success,
            'isPending' => $isPending,
            'transactionId' => $transactionId,
            'amount' => $amountCents ? number_format($amountCents / 100, 2) : '0.00',
            'currency' => $currency ?? 'EGP',
            'orderId' => $orderId,
            'errorMessage' => $errorMessage,
        ]);
    }
}
