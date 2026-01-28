<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Services\LocalizationService;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * Verify a voucher code.
     */
    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'order_amount' => 'nullable|numeric|min:0',
        ]);

        $code = $request->input('code');
        $orderAmount = $request->input('order_amount', 0);
        $user = auth('api')->user();

        $voucher = Voucher::where('code', $code)->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Voucher']),
            ], 404);
        }

        if (!$voucher->isValidForUser($user, $orderAmount)) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.invalid_voucher'), // You might need to add this message key
                'data' => [
                    'valid' => false,
                    'code' => null,
                    'type' => null,
                    'value' => null,
                    'discount_amount' => null,
                    'min_order_amount' => null,
                ]
            ], 400);
        }

        $discountAmount = $voucher->getDiscountAmount($orderAmount);

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => [
                'valid' => true,
                'code' => $voucher->code,
                'type' => $voucher->type,
                'value' => $voucher->value,
                'discount_amount' => $discountAmount,
                'min_order_amount' => $voucher->min_order_amount,
            ],
        ]);
    }
}
