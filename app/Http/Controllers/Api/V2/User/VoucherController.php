<?php

namespace App\Http\Controllers\Api\V2\User;

use App\Http\Controllers\Controller;
use App\Models\Cart;
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
            'code'         => 'required|string',
            'order_amount' => 'nullable|numeric|min:0',
        ]);

        $code        = $request->input('code');
        $orderAmount = $request->input('order_amount', 0);
        $user        = auth('api')->user();

        $voucher = Voucher::with('module')->where('code', $code)->first();

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Voucher']),
            ], 400);
        }

        // Load the authenticated user's cart items (with product → category for module_id resolution)
        $cartItems = collect();
        $cart = Cart::with(['items.product.category'])->where('user_id', $user->id)->first();
        if ($cart) {
            $cartItems = $cart->items;
        }

        $validationResult = $voucher->validateForUser($user, $orderAmount, $cartItems);

        if ($validationResult !== true) {
            $messageKey = $validationResult;
            $messageParams = [];
            
            if ($messageKey === 'errors.voucher_module_restricted') {
                $messageParams['module'] = $voucher->module->name ?? 'the required module';
            }

            if ($messageKey === 'errors.voucher_min_order_amount') {
                $messageParams['amount'] = number_format($voucher->min_order_amount, 2);
            }

            if ($messageKey === 'errors.voucher_min_module_order_amount') {
                $messageParams['amount'] = number_format($voucher->min_order_amount, 2);
                $messageParams['module'] = $voucher->module->name ?? 'the required module';
            }

            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage($messageKey, $messageParams),
                'data'    => [
                    'valid'            => false,
                    'code'             => null,
                    'type'             => null,
                    'value'            => null,
                    'discount_amount'  => null,
                    'min_order_amount' => null,
                    'module'           => $voucher->module ? ['id' => $voucher->module->id, 'name' => $voucher->module->name] : null,
                ],
            ], 400);
        }

        $discountAmount = $voucher->getDiscountAmount($orderAmount);

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data'    => [
                'valid'            => true,
                'code'             => $voucher->code,
                'type'             => $voucher->type,
                'value'            => $voucher->value,
                'discount_amount'  => $discountAmount,
                'min_order_amount' => $voucher->min_order_amount,
                'module'           => $voucher->module ? ['id' => $voucher->module->id, 'name' => $voucher->module->name] : null,
            ],
        ]);
    }
}
