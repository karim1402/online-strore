<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;

    /**
     * Get order statistics for dashboard
     */
    public function statistics(): JsonResponse
    {
        try {
            $totalOrders = Order::count();
            $pendingOrders = Order::where('simple_status', 'in_progress')->count();
            $processingOrders = Order::where('simple_status', 'ready_to_pick')->count();
            $inDeliveryOrders = Order::where('simple_status', 'in_delivery')->count();
            $deliveredOrders = Order::where('simple_status', 'delivered')->count();
            $cancelledOrders = Order::where('simple_status', 'cancelled')->count();

            // Revenue per status
            $pendingRevenue = Order::where('simple_status', 'in_progress')->sum('total');
            $processingRevenue = Order::where('simple_status', 'ready_to_pick')->sum('total');
            $inDeliveryRevenue = Order::where('simple_status', 'in_delivery')->sum('total');
            $deliveredRevenue = Order::where('simple_status', 'delivered')->sum('total');
            $cancelledRevenue = Order::where('simple_status', 'cancelled')->sum('total');
            $totalRevenue = Order::sum('total');

            return $this->successResponse([
                'total_orders' => $totalOrders,
                'pending' => $pendingOrders,
                'processing' => $processingOrders,
                'in_delivery' => $inDeliveryOrders,
                'delivered' => $deliveredOrders,
                'cancelled' => $cancelledOrders,
                'revenue' => [
                    'total' => round($totalRevenue, 2),
                    'pending' => round($pendingRevenue, 2),
                    'processing' => round($processingRevenue, 2),
                    'in_delivery' => round($inDeliveryRevenue, 2),
                    'delivered' => round($deliveredRevenue, 2),
                    'cancelled' => round($cancelledRevenue, 2),
                ],
            ], 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $query = Order::with(['user', 'items.product.category', 'store'])
                ->orderBy('created_at', 'desc');

            if ($request->filled('store_id')) {
                $query->where('store_id', $request->store_id);
            }

            if ($request->filled('status')) {
                $query->where('simple_status', $request->status);
            }

            if ($request->filled('payment_status')) {
                $query->where('payment_status', $request->payment_status);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%")
                      ->orWhereHas('user', function ($userQuery) use ($search) {
                          $userQuery->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%")
                                    ->orWhere('phone', 'like', "%{$search}%")
                                    ->orWhere('id', "{$search}");
                      });
                });
            }

            // Filter by date range
            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            $perPage = (int) $request->get('per_page', 15) ?? 15;
            // if ($perPage <= 0) {
            //     $perPage = 15;
            // }
            // if ($perPage > 100) {
            //     $perPage = 100;
            // }

            $orders = $query->paginate($perPage);

            return $this->successResponse($orders, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $order = Order::with(['user', 'store', 'delivery', 'items.product.images', 'items.product.category', 'items.options', 'items.addons', 'branch', 'vendorInvoice'])
                ->find($id);

            if (!$order) {
                return $this->errorResponse('errors.order_not_found', [], 404);
            }

            return $this->successResponse($order, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    public function store(Request $request): JsonResponse
    {
        try {
            // Validate input
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'user_id' => 'nullable|exists:users,id',
                'address_id' => 'nullable|exists:user_addresses,id',
                'address' => 'nullable|array',
                'payment_method' => 'required|in:cash,online',
                'payment_status' => 'required|in:pending,paid,failed,refunded',
                'order_status' => 'nullable|in:pending,pending_payment,confirmed,preparing,ready,out_for_delivery,delivered,cancelled',
                'simple_status' => 'nullable|in:in_progress,ready_to_pick,in_delivery,delivered,cancelled',
                'notes' => 'nullable|string|max:500',
                'discount' => 'nullable|numeric|min:0',
                'delivery_fee' => 'nullable|numeric|min:0',
                'tax' => 'nullable|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.product_id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.special_instructions' => 'nullable|string|max:200',
                'items.*.options' => 'nullable|array',
                'items.*.options.*.product_option_value_id' => 'required|exists:product_option_values,id',
                'items.*.addons' => 'nullable|array',
                'items.*.addons.*.addon_id' => 'required|exists:addons,id',
                'items.*.addons.*.quantity' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return $this->errorResponse('errors.validation_failed', $validator->errors(), 422);
            }

            // Verify user exists if provided
            $user = null;
            if ($request->filled('user_id')) {
                $user = \App\Models\User::find($request->user_id);
                if (!$user) {
                    return $this->errorResponse('errors.user_not_found', [], 404);
                }
            }

            // Get or create address snapshot (optional)
            $addressSnapshot = null;
            if ($request->filled('address_id')) {
                $query = \App\Models\UserAddress::where('id', $request->address_id);
                
                // If user is specified, ensure address belongs to them
                if ($user) {
                    $query->where('user_id', $user->id);
                }
                
                $address = $query->first();

                if (!$address) {
                    return $this->errorResponse('errors.address_not_found', [], 404);
                }

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
            } elseif ($request->filled('address')) {
                // Use provided address
                $addressSnapshot = $request->address;
            }

            // Validate and calculate totals
            $subtotal = 0;
            $validatedItems = [];

            foreach ($request->items as $itemData) {
                $product = \App\Models\Product::find($itemData['product_id']);
                
                if (!$product || !$product->is_active) {
                    return $this->errorResponse('errors.product_not_available', [
                        'product_id' => $itemData['product_id']
                    ], 422);
                }

                $itemPrice = $product->base_price;
                $itemSubtotal = 0;

                // Validate and calculate option prices
                $validatedOptions = [];
                if (!empty($itemData['options'])) {
                    foreach ($itemData['options'] as $optionData) {
                        $optionValue = \App\Models\ProductOptionValue::with('optionValue')
                            ->find($optionData['product_option_value_id']);

                        if (!$optionValue || !$optionValue->is_available) {
                            return $this->errorResponse('errors.option_not_available', [
                                'product_option_value_id' => $optionData['product_option_value_id']
                            ], 422);
                        }

                        $optionPrice = $optionValue->calculatePrice($product->base_price);
                        $itemPrice = $optionPrice;

                        $validatedOptions[] = [
                            'product_option_value_id' => $optionValue->id,
                            'price' => $optionPrice,
                        ];
                    }
                }

                $itemSubtotal = $itemPrice * $itemData['quantity'];

                // Validate and calculate addon prices
                $validatedAddons = [];
                $addonsSubtotal = 0;
                if (!empty($itemData['addons'])) {
                    foreach ($itemData['addons'] as $addonData) {
                        $addon = \App\Models\Addon::find($addonData['addon_id']);

                        if (!$addon || !$addon->is_active) {
                            return $this->errorResponse('errors.addon_not_available', [
                                'addon_id' => $addonData['addon_id']
                            ], 422);
                        }

                        $addonSubtotal = $addon->price * $addonData['quantity'];
                        $addonsSubtotal += $addonSubtotal;

                        $validatedAddons[] = [
                            'addon_id' => $addon->id,
                            'quantity' => $addonData['quantity'],
                            'price' => $addon->price,
                        ];
                    }
                }

                $subtotal += $itemSubtotal + $addonsSubtotal;

                $validatedItems[] = [
                    'product' => $product,
                    'quantity' => $itemData['quantity'],
                    'price' => $itemPrice,
                    'special_instructions' => $itemData['special_instructions'] ?? null,
                    'options' => $validatedOptions,
                    'addons' => $validatedAddons,
                ];
            }

            // Calculate final total
            $deliveryFee = $request->delivery_fee ?? 0;
            $tax = $request->tax ?? 0;
            $discount = $request->discount ?? 0;
            $total = max(0, $subtotal + $deliveryFee + $tax - $discount);

            // Create order in transaction
            \Illuminate\Support\Facades\DB::beginTransaction();

            try {
                // Generate order number
                $orderNumber = $this->generateOrderNumber();

                // Determine order status
                $orderStatus = $request->order_status ?? ($request->payment_method === 'cash' ? 'pending' : 'pending_payment');
                $simpleStatus = $request->simple_status ?? 'in_progress';

                // Create order
                $order = Order::create([
                    'order_number' => $orderNumber,
                    'user_id' => $user ? $user->id : null,
                    'store_id' => null, // Admin orders don't require store
                    'address_id' => $request->address_id ?? null,
                    'address_snapshot' => $addressSnapshot,
                    'payment_method' => $request->payment_method,
                    'payment_status' => $request->payment_status,
                    'payment_reference' => null,
                    'order_status' => $orderStatus,
                    'simple_status' => $simpleStatus,
                    'subtotal' => $subtotal,
                    'delivery_fee' => $deliveryFee,
                    'tax' => $tax,
                    'discount' => $discount,
                    'total' => $total,
                    'notes' => $request->notes,
                ]);

                // Create order items
                foreach ($validatedItems as $itemData) {
                    $itemSubtotal = $itemData['price'] * $itemData['quantity'];
                    
                    // Calculate addons total for this item
                    $addonsTotal = 0;
                    foreach ($itemData['addons'] as $addonData) {
                        $addonsTotal += $addonData['price'] * $addonData['quantity'];
                    }
                    
                    $totalItemPrice = $itemSubtotal + $addonsTotal;
                    
                    $orderItem = \App\Models\OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $itemData['product']->id,
                        'product_snapshot' => [
                            'id' => $itemData['product']->id,
                            'name_en' => $itemData['product']->name_en,
                            'name_ar' => $itemData['product']->name_ar,
                            'base_price' => $itemData['product']->base_price,
                        ],
                        'quantity' => $itemData['quantity'],
                        'unit_price' => $itemData['price'],
                        'total_price' => $totalItemPrice,
                    ]);

                    // Create order item options
                    foreach ($itemData['options'] as $optionData) {
                        $productOptionValue = \App\Models\ProductOptionValue::with('optionValue.optionGroup')->find($optionData['product_option_value_id']);
                        
                            \App\Models\OrderItemOption::create([
                                'order_item_id' => $orderItem->id,
                                'option_snapshot' => [
                                    'option_group_name_en' => $productOptionValue->optionValue->optionGroup->name_en ?? null,
                                    'option_group_name_ar' => $productOptionValue->optionValue->optionGroup->name_ar ?? null,
                                    'option_value_name_en' => $productOptionValue->optionValue->name_en ?? null,
                                    'option_value_name_ar' => $productOptionValue->optionValue->name_ar ?? null,
                                    'calculated_price' => $optionData['price'],
                                ],
                            ]);
                    }

                    // Create order item addons
                    foreach ($itemData['addons'] as $addonData) {
                        $addon = \App\Models\Addon::find($addonData['addon_id']);
                        
                        \App\Models\OrderItemAddon::create([
                            'order_item_id' => $orderItem->id,
                            'addon_snapshot' => [
                                'name_en' => $addon->name_en,
                                'name_ar' => $addon->name_ar,
                                'description_en' => $addon->description_en,
                                'description_ar' => $addon->description_ar,
                            ],
                            'quantity' => $addonData['quantity'],
                            'unit_price' => $addonData['price'],
                            'total_price' => $addonData['price'] * $addonData['quantity'],
                        ]);
                    }
                }

                \Illuminate\Support\Facades\DB::commit();

                // Load relationships for response
                $order->load(['user', 'items.options', 'items.addons']);

                return $this->successResponse($order, 'success.order_created', [], 201);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Admin Order Creation Error: ' . $e->getMessage(), [
                'exception' => $e,
                'request' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return $this->errorResponse('errors.server_error', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-' . strtoupper(uniqid());
        } while (Order::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }
    public function markReadyToPick($id): JsonResponse
    {
        try {
            $order = Order::find($id);

            if (!$order) {
                return $this->errorResponse('errors.order_not_found', [], 404);
            }

            $order->simple_status = 'ready_to_pick';
            $order->save();

            $order->load(['user', 'store', 'items.options', 'items.addons']);

            // Send FCM notification to all available delivery users
            $this->notifyDeliveryUsers($order);

            // Send FCM notification to the client (order user)
            $this->notifyClient($order);

            return $this->successResponse($order, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Send FCM notifications to all available delivery users
     */
    private function notifyDeliveryUsers(Order $order): void
    {
        try {
            // Get all available delivery users with FCM tokens
            $deliveryUsers = \App\Models\Delivery::where('status', true)
                ->where('availability', true)
                ->whereNotNull('fcm_token')
                ->get();

            if ($deliveryUsers->isEmpty()) {
                \Illuminate\Support\Facades\Log::info('No available delivery users to notify for order: ' . $order->order_number);
                return;
            }

            $tokens = $deliveryUsers->pluck('fcm_token')->toArray();

            $fcmService = app(\App\Services\FcmService::class);

            $title = 'New Order Ready for Pickup!';
            $body = "Order #{$order->order_number} is ready to pick from " . ($order->store->name_en ?? 'store');

            $data = [
                'type' => 'order_ready_to_pick',
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
                'store_id' => (string) $order->store_id,
            ];

            $result = $fcmService->sendToMultiple($tokens, $title, $body, $data);

            \Illuminate\Support\Facades\Log::info('Delivery notification sent for order: ' . $order->order_number, [
                'tokens_count' => count($tokens),
                'success' => $result['success'] ?? 0,
                'failure' => $result['failure'] ?? 0,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send delivery notification: ' . $e->getMessage());
        }
    }

    /**
     * Send FCM notification to the client (order user)
     */
    private function notifyClient(Order $order): void
    {
        try {
            // Check if the order has a user with FCM token
            if (!$order->user || !$order->user->fcm_token) {
                \Illuminate\Support\Facades\Log::info('No FCM token for client of order: ' . $order->order_number);
                return;
            }

            $fcmService = app(\App\Services\FcmService::class);

            $title = 'Your Order is Ready!';
            $body = "Order #{$order->order_number} is ready and will be picked up soon.";

            $data = [
                'type' => 'order_ready',
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
            ];

            $success = $fcmService->sendNotification($order->user->fcm_token, $title, $body, $data);

            \Illuminate\Support\Facades\Log::info('Client notification sent for order: ' . $order->order_number, [
                'user_id' => $order->user->id,
                'success' => $success,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send client notification: ' . $e->getMessage());
        }
    }

    public function cancel($id): JsonResponse
    {
        try {
            $order = Order::whereNull('delivery_id')
                ->find($id);

            if (!$order) {
                return $this->errorResponse('errors.order_not_found', [], 404);
            }

            if (!in_array($order->simple_status, ['in_progress', 'ready_to_pick'])) {
                return $this->errorResponse('errors.order_cannot_cancel', [], 400);
            }

            $order->simple_status = 'cancelled';
            $order->save();

            $order->load(['user', 'store', 'items.options', 'items.addons']);

            return $this->successResponse($order, 'order.cancelled_successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
