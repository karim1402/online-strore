<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Result - Makook</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">

    <div class="bg-white p-8 rounded-2xl shadow-xl max-w-md w-full text-center">
        
        @if($success)
            <!-- Success Icon -->
            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-6">
                <svg class="h-12 w-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h2 class="text-3xl font-bold text-gray-900 mb-2">Payment Successful!</h2>
            <p class="text-gray-500 mb-8">Your transaction has been completed successfully.</p>

            <div class="bg-gray-50 rounded-lg p-4 mb-8 text-left">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500 text-sm">Transaction ID</span>
                    <span class="font-semibold text-gray-900">{{ $transactionId }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500 text-sm">Amount</span>
                    <span class="font-semibold text-gray-900">{{ $amount }} {{ $currency }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Reference</span>
                    <span class="font-semibold text-gray-900">#{{ $orderId }}</span>
                </div>
            </div>

        @elseif($isPending)
            <!-- Pending Icon -->
             <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-yellow-100 mb-6">
                <svg class="h-12 w-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0"></path>
                </svg>
            </div>

            <h2 class="text-3xl font-bold text-gray-900 mb-2">Payment Pending</h2>
            <p class="text-gray-500 mb-8">Your transaction is currently being processed. Please wait for confirmation.</p>

        @else
            <!-- Failure Icon -->
            <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 mb-6">
                <svg class="h-12 w-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>

            <h2 class="text-3xl font-bold text-gray-900 mb-2">Payment Failed</h2>
            <p class="text-gray-500 mb-8">Something went wrong with your transaction.</p>

            @if($errorMessage)
            <div class="bg-red-50 text-red-700 p-3 rounded-lg text-sm mb-6">
                {{ $errorMessage }}
            </div>
            @endif
        @endif

        <a href="makook://payment/callback?success={{ $success ? 'true' : 'false' }}&id={{ $transactionId }}" 
           class="inline-block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-3 px-6 rounded-xl transition duration-200">
            Return to App
        </a>
        
        <p class="mt-4 text-xs text-gray-400">
            You will be redirected automatically...
        </p>
    </div>

    <!-- Auto-redirect script for mobile app deep linking -->
    <script>
        setTimeout(function() {
            window.location.href = "makook://payment/callback?success={{ $success ? 'true' : 'false' }}&id={{ $transactionId }}";
        }, 3000);
    </script>
</body>
</html>
