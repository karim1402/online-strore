<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderItemOption;
use App\Models\OrderItemAddon;
use App\Models\Cart;
use App\Models\UserAddress;
use App\Services\LocalizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Checkout cart and create order
     */
    public function checkout(Request $request)
    {
        $user = auth('users')->user();

        // Validation
        $validator = Validator::make($request->all(), [
            'address_id' => 'required|exists:user_addresses,id',
            'payment_method' => 'required|in:cash,online',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Get cart with all items
        $cart = $user->cart()->with([
            'items.product.store',
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
            $deliveryFee = 10.00; // Placeholder
            $tax = 0.00; // Placeholder
            $total = $subtotal + $deliveryFee + $tax;

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

            // Create order
            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => $user->id,
                'store_id' => $cart->store_id,
                'address_id' => $address->id,
                'address_snapshot' => $addressSnapshot,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'payment_reference' => $paymentReference,
                'order_status' => $orderStatus,
                'subtotal' => $subtotal,
                'delivery_fee' => $deliveryFee,
                'tax' => $tax,
                'total' => $total,
                'notes' => $request->notes,
            ]);

            // Copy cart items to order items
            foreach ($cart->items as $cartItem) {
                $product = $cartItem->product;

                // Create product snapshot
                $productSnapshot = [
                    'id' => $product->id,
                    'name_en' => $product->name_en,
                    'name_ar' => $product->name_ar,
                    'description_en' => $product->description_en,
                    'description_ar' => $product->description_ar,
                    'base_price' => $product->price,
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
                        'calculated_price' => $this->calculateOptionPrice($product->price, $cartOption->productOptionValue),
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

            // Clear cart for cash orders only
            if ($request->payment_method === 'cash') {
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
                    'store_id' => $cart->store_id,
                ])
                ->log('Order created');

            DB::commit();

            // Prepare response
            $response = [
                'order' => $this->transformOrder($order->load(['items.options', 'items.addons'])),
            ];

            // Add payment URL for online payment
            if ($request->payment_method === 'online') {
                $response['order']['payment_url'] = "https://payment.example.com/pay/{$paymentReference}";
            }

            $message = $request->payment_method === 'cash' 
                ? LocalizationService::getMessage('order.placed_successfully')
                : LocalizationService::getMessage('order.pending_payment');

            return response()->json([
                'success' => true,
                'message' => $message,
                'data' => $response,
            ], 201);

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
     * Get user's order history
     */
    public function index(Request $request)
    {
        $user = auth('users')->user();

        $query = Order::where('user_id', $user->id)
            ->with(['store', 'items'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status')) {
            $query->where('order_status', $request->status);
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
        $user = auth('users')->user();

        $order = Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->with(['store', 'items.options', 'items.addons'])
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
        $user = auth('users')->user();

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

        $order->order_status = 'cancelled';
        $order->save();

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
                    'error' => 'Product is no longer available',
                ];
                continue;
            }

            // Check store is approved
            if ($product->store->status !== 'approved') {
                $errors[] = [
                    'product_id' => $product->id,
                    'error' => 'Store is not available',
                ];
            }
        }

        return $errors;
    }

    /**
     * Helper: Calculate cart total
     */
    private function calculateCartTotal($cart)
    {
        $subtotal = $this->calculateSubtotal($cart);
        $deliveryFee = 10.00;
        $tax = 0.00;
        return $subtotal + $deliveryFee + $tax;
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
        $basePrice = $cartItem->product->price;

        // Add option prices
        foreach ($cartItem->options as $option) {
            $basePrice += $this->calculateOptionPrice(
                $cartItem->product->price,
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
        
        $lastOrder = Order::whereDate('created_at', now())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastOrder ? ((int)substr($lastOrder->order_number, -5)) + 1 : 1;

        return sprintf('ORD-%s-%05d', $date, $sequence);
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
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'payment_reference' => $order->payment_reference,
            'subtotal' => number_format($order->subtotal, 2),
            'delivery_fee' => number_format($order->delivery_fee, 2),
            'tax' => number_format($order->tax, 2),
            'total' => number_format($order->total, 2),
            'items_count' => $order->items->count(),
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
            'store' => [
                'id' => $order->store->id,
                'name' => $order->store->{"name_{$locale}"} ?? $order->store->name_en,
                'logo_url' => $order->store->logo_url ?? null,
            ],
            'status' => $order->order_status,
            'status_label' => $order->status_label,
            'payment_method' => $order->payment_method,
            'total' => number_format($order->total, 2),
            'items_count' => $order->items->count(),
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
            'store' => [
                'id' => $order->store->id,
                'name' => $order->store->{"name_{$locale}"} ?? $order->store->name_en,
                'description' => $order->store->{"description_{$locale}"} ?? $order->store->description_en ?? '',
                'logo_url' => $order->store->logo_url ?? null,
                'phone' => $order->store->phone ?? null,
            ],
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
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'payment_reference' => $order->payment_reference,
            'subtotal' => number_format($order->subtotal, 2),
            'delivery_fee' => number_format($order->delivery_fee, 2),
            'tax' => number_format($order->tax, 2),
            'total' => number_format($order->total, 2),
            'notes' => $order->notes,
            'created_at' => $order->created_at->toISOString(),
            'updated_at' => $order->updated_at->toISOString(),
        ];
    }
}
