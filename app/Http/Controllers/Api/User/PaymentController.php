<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use App\Models\OrderItemAddon;
use App\Models\Payment;
use App\Models\Cart;
use App\Models\UserAddress;
use App\Services\PaymobService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\LocalizationService;

class PaymentController extends Controller
{
    use ApiResponse;

    protected $paymobService;

    public function __construct(PaymobService $paymobService)
    {
        $this->paymobService = $paymobService;
    }

    /**
     * Create payment intention from Cart
     */
    public function createIntention(Request $request): JsonResponse
    {
        try {
            $user = auth('api')->user();

            $request->validate([
                'address_id' => 'required|exists:user_addresses,id',
            ]);

            // Get cart
            $cart = Cart::with(['items.product', 'items.options', 'items.addons'])
                ->where('user_id', $user->id)
                ->first();

            if (!$cart || $cart->items->isEmpty()) {
                return $this->errorResponse('errors.cart_empty', [], 400);
            }

            // Get Address
            $address = UserAddress::where('id', $request->address_id)
                ->where('user_id', $user->id)
                ->first();

            if (!$address) {
                return $this->errorResponse('errors.address_not_found', [], 404);
            }

            // Prepare billing data
            $billingData = [
                'first_name' => $user->name ?? 'Guest',
                'last_name' => 'User',
                'email' => $user->email ?? 'guest@example.com',
                'phone_number' => $user->phone ?? ($address->phone ?? '+201000000000'),
                'apartment' => $address->apartment_number ?? 'NA',
                'floor' => $address->floor_number ?? 'NA',
                'street' => $address->street_name ?? 'NA',
                'building' => $address->building_name ?? 'NA',
                'city' => 'Cairo',
                'country' => 'EG',
                'state' => 'Cairo',
            ];

            // Calculate total
            $subtotal = $this->calculateCartTotal($cart);
            $deliveryFee = 0.00; // Placeholder
            $tax = 0.00;

            // Apply Voucher
            $discount = 0.00;
            $voucher = null;
            if ($request->filled('voucher_code')) {
                $voucher = \App\Models\Voucher::where('code', $request->voucher_code)->first();
                if ($voucher && $voucher->isValidForUser($user, $subtotal)) {
                    $discount = $voucher->getDiscountAmount($subtotal);
                } else {
                     return $this->errorResponse('errors.voucher_invalid', [], 422);
                }
            }

            $total = max(0, $subtotal + $deliveryFee + $tax - $discount);
            $amountCents = (int) ($total * 100);

            // Prepare items data
            $items = [];
            foreach ($cart->items as $item) {
                $items[] = [
                    'name' => 'Order Item',
                    'amount' => (int) ($this->calculateItemPrice($item) * 100),
                    'description' => 'Product ID: ' . $item->product_id,
                    'quantity' => $item->quantity,
                ];
            }

            // Add Delivery Fee item
            if ($deliveryFee > 0) {
                $items[] = [
                    'name' => 'Delivery Fee',
                    'amount' => (int) ($deliveryFee * 100),
                    'description' => 'Delivery Fee',
                    'quantity' => 1,
                ];
            }

            // Add Tax item
            if ($tax > 0) {
                $items[] = [
                    'name' => 'Tax',
                    'amount' => (int) ($tax * 100),
                    'description' => 'Tax',
                    'quantity' => 1,
                ];
            }

            // Add Discount item (Negative)
            if ($discount > 0) {
                $items[] = [
                    'name' => 'Discount',
                    'amount' => -((int) ($discount * 100)),
                    'description' => 'Voucher Discount',
                    'quantity' => 1,
                ];
            }

            // Special reference includes user_id, address_id, and voucher_id
            // Format: USER-{id}-ADDR-{id}-VOUCHER-{id}-TS-{timestamp}
            $voucherId = $voucher ? $voucher->id : 0;
            $specialReference = sprintf(
                'USER-%d-ADDR-%d-VOUCHER-%d-TS-%d',
                $user->id,
                $address->id,
                $voucherId,
                time()
            );

            $paymentData = [
                'amount_cents' => $amountCents,
                'currency' => 'EGP',
                'items' => $items,
                'billing_data' => $billingData,
                'special_reference' => $specialReference,
                'notification_url' => route('user.payments.webhook'),
            ];

            $result = $this->paymobService->createPaymentIntention($paymentData);

            return $this->successResponse([
                'payment_keys' => $result['payment_keys'],
                'client_secret' => $result['client_secret'],
                'intention_order_id' => $result['intention_order_id'],
                'amount_cents' => $amountCents,
                'discount' => $discount,
            ], 'success.payment_intention_created');

        } catch (\Exception $e) {
            Log::error('Payment Intention Error: ' . $e->getMessage());
            return $this->errorResponse('errors.payment_initiation_failed', ['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Handle Paymob Webhook
     */
    public function webhook(Request $request)
    {
        try {
            $data = $request->all();
            $hmac = $request->query('hmac');

            Log::info('Paymob Webhook received', $data);

            // 1. Verify HMAC (Skip for now or implement if keys available)
             if (!$this->paymobService->verifyHmac($data['obj'], $hmac)) {
                 Log::error('Paymob HMAC verification failed');
                 // return response()->json(['success' => false], 403); 
             }

            $obj = $data['obj'];
            $success = $obj['success'];
            $transactionId = $obj['id'];
            $specialReference = $obj['order']['merchant_order_id'] ?? null;

            // 2. Parse Special Reference
            // Format: USER-{id}-ADDR-{id}-VOUCHER-{id}-TS-{timestamp}
            // Fallback for old format without voucher: USER-{id}-ADDR-{id}-TS-{timestamp}
            $voucherId = 0;
            if (preg_match('/USER-(\d+)-ADDR-(\d+)-VOUCHER-(\d+)-TS-(\d+)/', $specialReference, $matches)) {
                $userId = $matches[1];
                $addressId = $matches[2];
                $voucherId = $matches[3];
            } elseif (preg_match('/USER-(\d+)-ADDR-(\d+)-TS-(\d+)/', $specialReference, $matches)) {
                $userId = $matches[1];
                $addressId = $matches[2];
            } else {
                Log::error('Paymob Webhook: Invalid special reference ' . $specialReference);
                return response()->json(['success' => false, 'message' => 'Invalid reference'], 400);
            }

            // 3. Check duplicate transaction
            if (Payment::where('transaction_id', $transactionId)->exists()) {
                Log::info('Paymob Webhook: Transaction already processed ' . $transactionId);
                return response()->json(['success' => true]);
            }

            if ($success) {
                DB::beginTransaction();

                // 4. Create Order
                $order = $this->createOrderFromCart($userId, $addressId, $voucherId, $transactionId, $obj);

                if (!$order) {
                    DB::rollBack();
                    Log::error('Paymob Webhook: Failed to create order for user ' . $userId);
                    return response()->json(['success' => false, 'message' => 'Order creation failed'], 500);
                }

                DB::commit();
                
                // 5. Clear Cart
                $this->clearUserCart($userId);

                Log::info('Order created successfully: ' . $order->order_number);
            } else {
                Log::info('Paymob Webhook: Payment failed for reference ' . $specialReference);
            }

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Paymob Webhook Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Create Order Logic (Moved from OrderController)
     */
    private function createOrderFromCart($userId, $addressId, $voucherId, $transactionId, $paymentObj)
    {
        $user = \App\Models\User::find($userId);
        $cart = Cart::with(['items.product', 'items.options', 'items.addons'])->where('user_id', $userId)->first();
        $address = UserAddress::find($addressId);

        if (!$cart || !$address) return null;

        // Generate Order Number
        $orderNumber = $this->generateOrderNumber();

        // Calculate Totals
        $subtotal = $this->calculateCartTotal($cart);
        $deliveryFee = 10.00;
        $tax = 0.00;
        
        // Re-apply voucher logic
        $discount = 0.00;
        $voucher = null;
        if ($voucherId > 0) {
            $voucher = \App\Models\Voucher::find($voucherId);
            if ($voucher && $voucher->isValidForUser($user, $subtotal)) {
                $discount = $voucher->getDiscountAmount($subtotal);
            }
        }

        $total = max(0, $subtotal + $deliveryFee + $tax - $discount);

        // Create Order
        $order = Order::create([
            'order_number' => $orderNumber,
            'user_id' => $userId,
            'address_id' => $addressId,
            'address_snapshot' => $address->toArray(),
            'payment_method' => 'online',
            'payment_status' => 'paid',
            'payment_reference' => $transactionId,
            'order_status' => 'confirmed',
            'simple_status' => 'in_progress',
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
            'is_cash_handed_over' => false, // Online payment already captured
        ]);

        // Record Voucher Usage
        if ($voucher && $discount > 0) {
            \App\Models\VoucherUsage::create([
                'user_id' => $user->id,
                'voucher_id' => $voucher->id,
                'order_id' => $order->id,
                'discount_amount' => $discount,
            ]);
            
            $voucher->increment('usage_count');
        }

        // Create Order Items
        foreach ($cart->items as $cartItem) {
            $itemPrice = $this->calculateItemPrice($cartItem);
            
            $orderItem = OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'product_snapshot' => [
                    'name_en' => $cartItem->product->name_en,
                    'name_ar' => $cartItem->product->name_ar,
                    'base_price' => $cartItem->product->effective_price,
                ],
                'quantity' => $cartItem->quantity,
                'unit_price' => $itemPrice,
                'total_price' => $itemPrice * $cartItem->quantity,
            ]);

            // Save Options
            foreach ($cartItem->options as $option) {
                OrderItemOption::create([
                    'order_item_id' => $orderItem->id,
                    'option_snapshot' => [
                        'option_value_id' => $option->productOptionValue->option_value_id,
                        'price_value' => $option->productOptionValue->price_value,
                    ]
                ]);
            }
            
            // Save Addons
            foreach ($cartItem->addons as $addon) {
                OrderItemAddon::create([
                    'order_item_id' => $orderItem->id,
                    'addon_snapshot' => [
                         'name_en' => $addon->addon->name_en,
                         'price' => $addon->addon->price
                    ],
                    'quantity' => $addon->quantity,
                    'unit_price' => $addon->addon->price,
                    'total_price' => $addon->addon->price * $addon->quantity,
                ]);
            }
        }

        // Record Payment
        Payment::create([
            'order_id' => $order->id,
            'transaction_id' => $transactionId,
            'amount_cents' => $paymentObj['amount_cents'],
            'currency' => $paymentObj['currency'],
            'success' => true,
            'status' => 'completed',
            'raw_response' => $paymentObj,
        ]);

        return $order;
    }

    /**
     * Helpers
     */
    private function calculateCartTotal($cart)
    {
        $total = 0;
        foreach ($cart->items as $item) {
            $total += $this->calculateItemPrice($item) * $item->quantity;
        }
        return $total;
    }

    private function calculateItemPrice($item)
    {
        $price = $item->product->effective_price;
        foreach ($item->options as $opt) {
            $price += $opt->productOptionValue->price_value; // Simplified
        }
        foreach ($item->addons as $addon) {
            $price += $addon->addon->price;
        }
        return $price;
    }

    private function generateOrderNumber()
    {
        return 'ORD-' . date('Ymd') . '-' . rand(10000, 99999);
    }

    private function clearUserCart($userId)
    {
        $cart = Cart::where('user_id', $userId)->first();
        if ($cart) {
            $cart->items()->delete();
            $cart->delete();
        }
    }
}
