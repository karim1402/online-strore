<?php

namespace App\Http\Controllers\Api\V2\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use App\Models\OrderItemAddon;
use App\Models\Cart;
use App\Models\UserAddress;
use App\Models\Payment;
use App\Services\LocalizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Checkout cart and create order
     */
    public function checkout(Request $request)
    {

        $user = auth('api')->user();

        // Validation
        $validator = Validator::make($request->all(), [
            'address_id' => 'required|exists:user_addresses,id',
            // 'payment_method' => 'required|in:cash,online',
            'is_delivery' => 'nullable|boolean',
            'notes' => 'nullable|string|max:500',
            'scheduled_time' => 'nullable|date_format:H:i',
            // Payment details (flat fields, required if payment_method is online)
            // 'transaction_id' => 'required_if:payment_method,online|string',
            // 'gateway_order_id' => 'required_if:payment_method,online|string',
            // 'amount_cents' => 'required_if:payment_method,online|integer',
            // 'currency' => 'nullable|string',
            // 'success' => 'required_if:payment_method,online|in:0,1,true,false',
            // 'is_3d_secure' => 'nullable|in:0,1,true,false',
            // 'card_type' => 'nullable|string',
            // 'card_pan' => 'nullable|string',
            // 'gateway_response' => 'nullable|string',
            // 'txn_response_code' => 'nullable|string',
            // 'integration_id' => 'nullable|integer',
            // 'hmac' => 'nullable|string',
            // 'payment_created_at' => 'nullable|string',
            // 'merchant_commission' => 'nullable|numeric',
            // 'accept_fees' => 'nullable|numeric',
            'attribution' => 'nullable|array',
        ]);

        $request->payment_method = 'cash';

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Check working hours
        if (!\App\Models\AppSetting::isOpen()) {
            $hours = \App\Models\AppSetting::getWorkingHours();
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('working_hours.service_not_available', [
                    'opening_time' => \Carbon\Carbon::parse($hours['opening_time'])->format('g:i A'),
                    'closing_time' => \Carbon\Carbon::parse($hours['closing_time'])->format('g:i A'),
                ]),
                'data' => [
                    'is_open' => false,
                    'opening_time' => $hours['opening_time'],
                    'closing_time' => $hours['closing_time'],
                ],
            ], 403);
        }

        // Get cart with all items
        $cart = $user->cart()->with([
            // 'items.product.store',
            'items.product.primaryImage',
            'items.product.category',
            'items.options.productOptionValue.optionValue.optionGroup',
            'items.options.productOptionValue.optionValue.optionGroup',
            'items.addons.addon'
        ])->first();

        if (!$cart || $cart->items->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.cart_empty'),
            ], 404);
        }

        // Verify address belongs to user
        $address = UserAddress::where('id', $request->address_id)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.address_not_found'),
            ], 422);
        }

        // Validate cart items
        $validationErrors = $this->validateCartItems($cart);
        if (!empty($validationErrors)) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.cart_validation_failed'),
                'errors' => $validationErrors,
            ], 422);
        }

        // Check for duplicate order (idempotency)
        $calculatedTotal = $this->calculateCartTotal($cart);
        $recentOrder = Order::where('user_id', $user->id)
            ->where('total', $calculatedTotal)
            ->where('created_at', '>', now()->subMinutes(5))
            ->first();

        if ($recentOrder) {
            return response()->json([
                'success' => true,
                'message' => LocalizationService::getMessage('order.already_placed'),
                'data' => ['order' => $this->transformOrder($recentOrder)],
            ], 200);
        }

        // Create order in transaction
        try {
            DB::beginTransaction();

            // Generate order number
            $orderNumber = $this->generateOrderNumber();

            // Calculate totals
            $subtotal = $this->calculateSubtotal($cart);
            $deliveryFee = $request->boolean('is_delivery', true) ? $address->calculateDeliveryFee($subtotal, $cart->items) : 0.00;
            $tax = 0.00; // Placeholder
            
            // Apply Voucher
            $discount = 0.00;
            $voucher = null;
            if ($request->filled('voucher_code')) {
                $voucher = \App\Models\Voucher::with('module')->where('code', $request->voucher_code)->first();
                if ($voucher) {
                    $validationResult = $voucher->validateForUser($user, $subtotal, $cart->items);
                    if ($validationResult === true) {
                        $discount = $voucher->getDiscountAmount($subtotal);
                    } else {
                        DB::rollBack();
                        
                        $messageParams = [];
                        if ($validationResult === 'errors.voucher_module_restricted') {
                            $messageParams['module'] = $voucher->module->name ?? 'the required module';
                        }

                        return response()->json([
                            'success' => false,
                            'message' => LocalizationService::getMessage($validationResult, $messageParams),
                        ], 422);
                    }
                } else {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Voucher']),
                    ], 404);
                }
            }

            $total = max(0, $subtotal + $deliveryFee + $tax - $discount);

            // Determine order status based on payment method
            $orderStatus = $request->payment_method === 'cash' ? 'pending' : 'pending_payment';
            $paymentStatus = 'pending';
            $paymentReference = null;

            // Generate payment reference for online payment
            if ($request->payment_method === 'online') {
                $paymentReference = 'PAY-PLACEHOLDER-' . strtoupper(uniqid());
            }

            // Create address snapshot
            $addressSnapshot = [
                'id' => $address->id,
                'address_type' => $address->address_type,
                'building_name' => $address->building_name,
                'apartment_number' => $address->apartment_number,
                'floor_number' => $address->floor_number,
                'street_name' => $address->street_name,
                'landmark' => $address->landmark,
                'phone' => $address->phone,
                'latitude' => $address->latitude,
                'longitude' => $address->longitude,
            ];

            // Find nearest branch for the store based on delivery address
            // $nearestBranch = $this->getNearestBranchForAddress($cart->store_id, $address);
            // $branchId = $nearestBranch ? $nearestBranch->id : null;
            $branchId = null;

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                // 'store_id' => $cart->store_id,
                'branch_id' => $branchId,
                'address_id' => $address->id,
                'address_snapshot' => $addressSnapshot,
                'is_delivery' => $request->boolean('is_delivery', true),
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'payment_reference' => $paymentReference,
                'order_status' => $orderStatus,
                'simple_status' => 'in_progress',
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'notes' => $request->notes,
                'scheduled_time' => $request->scheduled_time ?: null,
                'is_cash_handed_over' => $request->payment_method === 'online',
                'is_paid_to_vendor' => $request->payment_method === 'cash',
                'campaign_attribution' => $request->attribution,
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

            // Copy cart items to order items
            foreach ($cart->items as $cartItem) {
                $product = $cartItem->product;

                if ($product->module_id == 31 && $product->stock !== null) {
                    $product->decrement('stock', $cartItem->quantity);
                }

                // Create product snapshot
                $productSnapshot = [
                    'id' => $product->id,
                    'name_en' => $product->name_en,
                    'name_ar' => $product->name_ar,
                    'description_en' => $product->description_en,
                    'description_ar' => $product->description_ar,
                    'base_price' => $product->effective_price,
                    'image_url' => $product->image_url ?? null,
                    'category_name_en' => $product->category->name_en ?? null,
                    'category_name_ar' => $product->category->name_ar ?? null,
                ];

                // Calculate item price
                $itemPrice = $this->calculateItemPrice($cartItem);

                // Create order item
                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_snapshot' => $productSnapshot,
                    'quantity' => $cartItem->quantity,
                    'unit_price' => $itemPrice,
                    'total_price' => $itemPrice * $cartItem->quantity,
                ]);

                // Copy options
                foreach ($cartItem->options as $cartOption) {
                    $optionValue = $cartOption->productOptionValue->optionValue;
                    $optionGroup = $optionValue->optionGroup;

                    $optionSnapshot = [
                        'option_group_id' => $optionGroup->id,
                        'option_group_name_en' => $optionGroup->name_en,
                        'option_group_name_ar' => $optionGroup->name_ar,
                        'option_value_id' => $optionValue->id,
                        'option_value_name_en' => $optionValue->name_en,
                        'option_value_name_ar' => $optionValue->name_ar,
                        'price_type' => $cartOption->productOptionValue->price_type,
                        'price_value' => $cartOption->productOptionValue->price_value,
                        'calculated_price' => $this->calculateOptionPrice($product->effective_price, $cartOption->productOptionValue),
                    ];

                    OrderItemOption::create([
                        'order_item_id' => $orderItem->id,
                        'option_snapshot' => $optionSnapshot,
                    ]);
                }

                // Copy addons
                foreach ($cartItem->addons as $cartAddon) {
                    $addon = $cartAddon->addon;

                    $addonSnapshot = [
                        'id' => $addon->id,
                        'name_en' => $addon->name_en,
                        'name_ar' => $addon->name_ar,
                        'description_en' => $addon->description_en ?? null,
                        'description_ar' => $addon->description_ar ?? null,
                        'price' => $addon->price,
                    ];

                    OrderItemAddon::create([
                        'order_item_id' => $orderItem->id,
                        'addon_snapshot' => $addonSnapshot,
                        'quantity' => $cartAddon->quantity,
                        'unit_price' => $addon->price,
                        'total_price' => $addon->price * $cartAddon->quantity,
                    ]);
                }
            }

            // Process online payment if payment details provided
            if ($request->payment_method === 'online' && $request->has('transaction_id')) {
                // Convert string boolean to actual boolean
                $success = filter_var($request->success, FILTER_VALIDATE_BOOLEAN);
                $is3dSecure = filter_var($request->is_3d_secure ?? false, FILTER_VALIDATE_BOOLEAN);
                
                // Create payment record
                $payment = Payment::create([
                    'order_id' => $order->id,
                    'transaction_id' => $request->transaction_id,
                    'gateway_order_id' => $request->gateway_order_id ?? null,
                    'amount_cents' => $request->amount_cents,
                    'currency' => $request->currency ?? 'EGP',
                    'success' => $success,
                    'status' => $success ? 'completed' : 'failed',
                    'is_3d_secure' => $is3dSecure,
                    'card_type' => $request->card_type ?? null,
                    'card_pan' => $request->card_pan ?? null,
                    'gateway_response' => $request->gateway_response ?? null,
                    'txn_response_code' => $request->txn_response_code ?? null,
                    'integration_id' => $request->integration_id ?? null,
                    'hmac' => $request->hmac ?? null,
                    'merchant_commission' => $request->merchant_commission ?? 0,
                    'accept_fees' => $request->accept_fees ?? 0,
                    'payment_created_at' => $request->payment_created_at ?? null,
                    'raw_response' => $request->only([
                        'transaction_id', 'gateway_order_id', 'amount_cents', 'currency',
                        'success', 'is_3d_secure', 'card_type', 'card_pan',
                        'gateway_response', 'txn_response_code', 'integration_id',
                        'hmac', 'payment_created_at', 'merchant_commission', 'accept_fees'
                    ]),
                ]);

                // Update order based on payment success
                if ($success) {
                    $order->payment_status = 'paid';
                    $order->order_status = 'confirmed';
                    $order->payment_reference = $request->transaction_id;
                    $order->save();
                } else {
                    $order->payment_status = 'failed';
                    $order->save();
                }

                // Log payment activity
                activity()
                    ->performedOn($payment)
                    ->causedBy($user)
                    ->withProperties([
                        'order_id' => $order->id,
                        'order_number' => $order->order_number,
                        'transaction_id' => $request->transaction_id,
                        'success' => $success,
                        'amount_cents' => $request->amount_cents,
                    ])
                    ->log('Payment processed during checkout');
            }

            // Clear cart for cash orders OR successful online payments
            if ($request->payment_method === 'cash' || 
                ($request->payment_method === 'online' && $request->has('transaction_id') && filter_var($request->success, FILTER_VALIDATE_BOOLEAN))) {
                $cart->items()->delete();
                $cart->delete();
            }

            // Log activity
            activity()
                ->performedOn($order)
                ->causedBy($user)
                ->withProperties([
                    'order_number' => $orderNumber,
                    'payment_method' => $request->payment_method,
                    'total' => $total,
                    // 'store_id' => $cart->store_id,
                ])
                ->log('Order created');

            DB::commit();

            // Send FCM notification to all admins
            // try {
            //     $admins = \App\Models\Admin::whereNotNull('fcm_token')->get();
            //     if ($admins->isNotEmpty()) {
            //         \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\NewOrderNotification($order));
            //     }
            // } catch (\Exception $e) {
            //     Log::error('Failed to send admin notification: ' . $e->getMessage());
            // }

            // Broadcast via Pusher to admin channel
            try {
                event(new \App\Events\NewOrderEvent($order));
            } catch (\Exception $e) {
                Log::error('Failed to broadcast new order event: ' . $e->getMessage());
            }

            // Prepare response
            $response = [
                'order' => $this->transformOrder($order->load(['items.options', 'items.addons'])),
            ];

            // Determine message based on payment method and status
            if ($request->payment_method === 'cash') {
                $message = LocalizationService::getMessage('order.placed_successfully');
            } elseif ($request->has('transaction_id') && filter_var($request->success, FILTER_VALIDATE_BOOLEAN)) {
                $message = LocalizationService::getMessage('order.payment_confirmed');
            } elseif ($request->has('transaction_id')) {
                $message = LocalizationService::getMessage('order.payment_failed');
            } else {
                $message = LocalizationService::getMessage('order.pending_payment');
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $response,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Checkout error', [
                'user_id' => $user->id ?? null,
                'request' => $request->except(['password']),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.server_error'),
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Get user's order history
     */
    public function index(Request $request)
    {
        $user = auth('api')->user();

        $query = Order::where('user_id', $user->id)
            ->with([/*'store',*/ 'items'])
            ->orderBy('created_at', 'desc');

        // Filter by simple status
        if ($request->has('status')) {
            $query->where('simple_status', $request->status);
        }

        // Pagination
        $perPage = min($request->get('per_page', 15), 100);
        $orders = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('order.list_retrieved'),
            'data' => [
                'orders' => $orders->map(function ($order) {
                    return $this->transformOrderListItem($order);
                }),
                'pagination' => [
                    'current_page' => $orders->currentPage(),
                    'per_page' => $orders->perPage(),
                    'total' => $orders->total(),
                    'last_page' => $orders->lastPage(),
                    'from' => $orders->firstItem(),
                    'to' => $orders->lastItem(),
                ],
            ],
        ], 200);
    }

    /**
     * Get single order details
     */
    public function show($orderId)
    {
        $user = auth('api')->user();

        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->with([/*'store',*/ 'items.options', 'items.addons', 'delivery'])
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.order_not_found'),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('order.retrieved_successfully'),
            'data' => [
                'order' => $this->transformOrderDetail($order),
            ],
        ], 200);
    }

    /**
     * Cancel order
     */
    public function cancel(Request $request, $orderId)
    {
        $user = auth('api')->user();

        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.order_not_found'),
            ], 404);
        }

        if (!$order->canBeCancelled()) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.order_cannot_cancel'),
            ], 400);
        }

        // Check if order was created more than 15 minutes ago
        if ($order->created_at->diffInMinutes(now()) > 15) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.order_cancel_time_exceeded'),
            ], 400);
        }

        $order->simple_status = 'cancelled';
        $order->reason = $request->input('reason');
        $order->save();

        $order->restoreStock();

        // Log activity
        activity()
            ->performedOn($order)
            ->causedBy($user)
            ->withProperties([
                'reason' => $request->input('reason', 'User cancelled'),
            ])
            ->log('Order cancelled');

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('order.cancelled_successfully'),
        ], 200);
    }

    /**
     * Confirm payment for online orders
     */
    public function confirmPayment(Request $request, $orderId)
    {
        $user = auth('api')->user();

        // Validation
        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string',
            'order_id' => 'required|string',
            'amount_cents' => 'required|integer',
            'currency' => 'required|string',
            'success' => 'required|boolean',
            'is_3d_secure' => 'nullable|boolean',
            'card_type' => 'nullable|string',
            'card_pan' => 'nullable|string',
            'gateway_response' => 'nullable|string',
            'txn_response_code' => 'nullable|string',
            'integration_id' => 'nullable|integer',
            'hmac' => 'nullable|string',
            'created_at' => 'nullable|string',
            'merchant_commission' => 'nullable|numeric',
            'accept_fees' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Find order
        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.order_not_found'),
            ], 404);
        }

        // Verify order is pending payment
        if ($order->payment_method !== 'online') {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('order.payment_method_not_online'),
            ], 400);
        }

        if ($order->payment_status === 'paid') {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('order.already_paid'),
            ], 400);
        }

        try {
            DB::beginTransaction();

            // Create payment record
            $payment = Payment::create([
                'order_id' => $order->id,
                'transaction_id' => $request->transaction_id,
                'gateway_order_id' => $request->order_id,
                'amount_cents' => $request->amount_cents,
                'currency' => $request->currency ?? 'EGP',
                'success' => $request->success,
                'status' => $request->success ? 'completed' : 'failed',
                'is_3d_secure' => $request->is_3d_secure ?? false,
                'card_type' => $request->card_type,
                'card_pan' => $request->card_pan,
                'gateway_response' => $request->gateway_response,
                'txn_response_code' => $request->txn_response_code,
                'integration_id' => $request->integration_id,
                'hmac' => $request->hmac,
                'merchant_commission' => $request->merchant_commission ?? 0,
                'accept_fees' => $request->accept_fees ?? 0,
                'payment_created_at' => $request->created_at,
                'raw_response' => $request->all(),
            ]);
            
            // Update order payment status based on success flag
            if ($request->success) {
                $order->payment_status = 'paid';
                $order->order_status = 'confirmed';
                $order->payment_reference = $request->transaction_id;
            } else {
                $order->payment_status = 'failed';
            }

            $order->save();

          
        
                $cart = $user->cart()->first();
                if ($cart) {
                    $cart->items()->delete();
                    $cart->delete();
                }
            

            // Log activity
            activity()
                ->performedOn($payment)
                ->causedBy($user)
                ->withProperties([
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'transaction_id' => $request->transaction_id,
                    'success' => $request->success,
                    'amount_cents' => $request->amount_cents,
                ])
                ->log('Payment confirmation received');

            DB::commit();

            $message = $request->success 
                ? LocalizationService::getMessage('order.payment_confirmed')
                : LocalizationService::getMessage('order.payment_failed');

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => [
                    'order' => $this->transformOrderDetail($order->load([/*'store',*/ 'items.options', 'items.addons'])),
                ],
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.server_error'),
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Helper: Validate cart items
     */
    private function validateCartItems($cart)
    {
        $errors = [];

        foreach ($cart->items as $item) {
            $product = $item->product;

            // Check product exists and is active
            if (!$product || !$product->is_active) {
                $errors[] = [
                    'product_id' => $product->id ?? $item->product_id,
                    'error' => LocalizationService::getMessage('cart.product_unavailable'),
                ];
                continue;
            }

            // Check stock for SME products (module 31)
            if ($product->module_id == 31 && $product->stock !== null) {
                if ($product->stock < $item->quantity) {
                    $locale = app()->getLocale();
                    $productName = ($locale === 'ar' ? $product->name_ar : $product->name_en)
                        ?: ($product->name_en ?: $product->name_ar);
                    $errors[] = [
                        'product_id' => $product->id,
                        'error' => LocalizationService::getMessage('cart.out_of_stock', ['name' => $productName]),
                    ];
                }
            }

            // Check store is approved
            // if ($product->store->status !== 'approved') {
            //     $errors[] = [
            //         'product_id' => $product->id,
            //         'error' => LocalizationService::getMessage('cart.store_unavailable'),
            //     ];
            // }
        }

        return $errors;
    }

    /**
     * Helper: Calculate cart total
     */
    private function calculateCartTotal($cart)
    {
        $subtotal = $this->calculateSubtotal($cart);
        // Note: Delivery fee is not predictably known here without address, 
        // the actual final logic adds it during checkout/intention.
        $tax = 0.00;
        return $subtotal + $tax;
    }

    /**
     * Helper: Calculate subtotal
     */
    private function calculateSubtotal($cart)
    {
        $subtotal = 0;

        foreach ($cart->items as $item) {
            $itemPrice = $this->calculateItemPrice($item);
            $subtotal += $itemPrice * $item->quantity;
        }

        return $subtotal;
    }

    /**
     * Helper: Calculate item price
     */
    private function calculateItemPrice($cartItem)
    {
        // Use effective_price which returns offer_price if available, otherwise base_price
        $basePrice = $cartItem->product->effective_price;

        // Add option prices
        foreach ($cartItem->options as $option) {
            $basePrice += $this->calculateOptionPrice(
                $cartItem->product->effective_price,
                $option->productOptionValue
            );
        }

        // Add addon prices
        foreach ($cartItem->addons as $addon) {
            $basePrice += $addon->addon->price * $addon->quantity;
        }

        return $basePrice;
    }

    /**
     * Helper: Calculate option price
     */
    private function calculateOptionPrice($productPrice, $productOptionValue)
    {
        if ($productOptionValue->price_type === 'percentage') {
            return ($productPrice * $productOptionValue->price_value) / 100;
        }

        return $productOptionValue->price_value;
    }

    /**
     * Helper: Generate order number
     */
    private function generateOrderNumber()
    {
        $date = now()->format('Ymd');
        
        // Use lockForUpdate to prevent race conditions
        $lastOrder = Order::whereDate('created_at', now())
            ->orderBy('id', 'desc')
            ->lockForUpdate()
            ->first();

        $sequence = $lastOrder ? ((int)substr($lastOrder->order_number, -5)) + 1 : 1;
        $orderNumber = sprintf('ORD-%s-%05d', $date, $sequence);

        // Safety check ensures uniqueness even if lock fails or in edge cases
        while (Order::where('order_number', $orderNumber)->exists()) {
            $sequence++;
            $orderNumber = sprintf('ORD-%s-%05d', $date, $sequence);
        }

        return $orderNumber;
    }

    /**
     * Helper: Get nearest branch for a given address
     */
    private function getNearestBranchForAddress($storeId, $address)
    {
        // Validate address has coordinates
        if (!$address->latitude || !$address->longitude) {
            return null;
        }

        // Get all active branches for the store with valid coordinates
        /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Branch[] $branches */
        $branches = \App\Models\Branch::where('store_id', $storeId)
            ->where('is_active', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        if ($branches->isEmpty()) {
            return null;
        }

        // Find the closest branch
        $closestBranch = null;
        $minDistance = PHP_FLOAT_MAX;

        foreach ($branches as $branch) {
            $distance = $branch->getDistanceFrom($address->latitude, $address->longitude);
            
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $closestBranch = $branch;
            }
        }

        return $closestBranch;
    }

    /**
     * Helper: Transform order for response
     */
    private function transformOrder($order)
    {
        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            'status' => $order->order_status,
            'simple_status' => $order->simple_status,
            'simple_status_label' => $order->simple_status_label,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'payment_reference' => $order->payment_reference,
            'subtotal' => number_format($order->subtotal, 2),
            'delivery_fee' => number_format($order->delivery_fee, 2),
            'tax' => number_format($order->tax, 2),
            'discount' => number_format($order->discount, 2),
            'total' => number_format($order->total, 2),
            'items_count' => $order->items->count(),
            'scheduled_time' => $order->scheduled_time,
            'created_at' => $order->created_at->toISOString(),
        ];
    }

    /**
     * Helper: Transform order list item
     */
    private function transformOrderListItem($order)
    {
        $locale = LocalizationService::getCurrentLocale();

        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            // 'store' => [
            //     'id' => $order->store->id,
            //     'name' => $order->store->{"name_{$locale}"} ?? $order->store->name_en,
            //     'logo_url' => $order->store->logo_url ?? null,
            // ],
            'status' => $order->order_status,
            'status_label' => $order->status_label,
            'simple_status' => $order->simple_status,
            'simple_status_label' => $order->simple_status_label,
            'payment_method' => $order->payment_method,
            'total' => number_format($order->total, 2),
            'items_count' => $order->items->count(),
            'scheduled_time' => $order->scheduled_time,
            'created_at' => $order->created_at->toISOString(),
        ];
    }

    /**
     * Helper: Transform order detail
     */
    private function transformOrderDetail($order)
    {
        $locale = LocalizationService::getCurrentLocale();

        return [
            'id' => $order->id,
            'order_number' => $order->order_number,
            // 'store' => [
            //     'id' => $order->store->id,
            //     'name' => $order->store->{"name_{$locale}"} ?? $order->store->name_en,
            //     'description' => $order->store->{"description_{$locale}"} ?? $order->store->description_en ?? '',
            //     'logo_url' => $order->store->logo_url ?? null,
            //     'phone' => $order->store->phone ?? null,
            // ],
            'delivery' => $order->delivery ? [
                'id' => $order->delivery->id,
                'name' => $order->delivery->name,
                'phone' => $order->delivery->phone,
                'vehicle_type' => $order->delivery->vehicle_type,
                'vehicle_number' => $order->delivery->vehicle_number,
            ] : null,
            'address' => $order->address_snapshot,
            'items' => $order->items->map(function ($item) use ($locale) {
                return [
                    'id' => $item->id,
                    'product' => [
                        'name' => $item->product_snapshot["name_{$locale}"] ?? $item->product_snapshot['name_en'],
                        'description' => $item->product_snapshot["description_{$locale}"] ?? $item->product_snapshot['description_en'] ?? '',
                        'image_url' => $item->product_snapshot['image_url'] ?? null,
                    ],
                    'quantity' => $item->quantity,
                    'selected_options' => $item->options->map(function ($option) use ($locale) {
                        return [
                            'option_group_name' => $option->option_snapshot["option_group_name_{$locale}"] ?? $option->option_snapshot['option_group_name_en'],
                            'option_value_name' => $option->option_snapshot["option_value_name_{$locale}"] ?? $option->option_snapshot['option_value_name_en'],
                            'calculated_price' => number_format($option->option_snapshot['calculated_price'] ?? 0, 2),
                        ];
                    }),
                    'selected_addons' => $item->addons->map(function ($addon) use ($locale) {
                        return [
                            'addon_name' => $addon->addon_snapshot["name_{$locale}"] ?? $addon->addon_snapshot['name_en'],
                            'quantity' => $addon->quantity,
                            'total_price' => number_format($addon->total_price, 2),
                        ];
                    }),
                    'unit_price' => number_format($item->unit_price, 2),
                    'total_price' => number_format($item->total_price, 2),
                ];
            }),
            'status' => $order->order_status,
            'status_label' => $order->status_label,
            'simple_status' => $order->simple_status,
            'simple_status_label' => $order->simple_status_label,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'payment_reference' => $order->payment_reference,
            'subtotal' => number_format($order->subtotal, 2),
            'delivery_fee' => number_format($order->delivery_fee, 2),
            'tax' => number_format($order->tax, 2),
            'total' => number_format($order->total, 2),
            'discount' => number_format($order->discount, 2),
            'notes' => $order->notes,
            'scheduled_time' => $order->scheduled_time,
            'created_at' => $order->created_at->toISOString(),
            'updated_at' => $order->updated_at->toISOString(),
        ];
    }
}
