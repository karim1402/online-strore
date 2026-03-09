<?php

namespace App\Http\Controllers\Api\User;

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

        // Check module restriction before generic validation to give a specific error message
        if ($voucher->failsModuleRestriction($cartItems)) {
            $moduleName = $voucher->module->name ?? 'the required module';
            return response()->json([
                'success' => false,
                'message' => "This voucher is only valid for {$moduleName} products. Your cart does not contain any products from this module.",
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

        if (!$voucher->isValidForUser($user, $orderAmount, $cartItems)) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.invalid_voucher'),
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
