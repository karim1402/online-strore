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

    public function index(Request $request): JsonResponse
    {
        try {
            $query = Order::with(['user', 'items', 'store'])
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
                    $q->where('order_number', 'like', "%{$search}%");
                });
            }

            $perPage = (int) $request->get('per_page', 15);
            if ($perPage <= 0) {
                $perPage = 15;
            }
            if ($perPage > 100) {
                $perPage = 100;
            }

            $orders = $query->paginate($perPage);

            return $this->successResponse($orders, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    public function show($id): JsonResponse
    {
        try {
            $order = Order::with(['user', 'store', 'items.options', 'items.addons','branch', 'vendorInvoice'])
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
                'user_id' => 'required|exists:users,id',
                'address_id' => 'required_without:address|exists:user_addresses,id',
                'address' => 'required_without:address_id|array',
                'address.street_name' => 'required_with:address|string',
                'address.building_name' => 'nullable|string',
                'address.apartment_number' => 'nullable|string',
                'address.floor_number' => 'nullable|string',
                'address.landmark' => 'nullable|string',
                'address.phone' => 'required_with:address|string',
                'address.latitude' => 'required_with:address|numeric',
                'address.longitude' => 'required_with:address|numeric',
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

            // Verify user exists
            $user = \App\Models\User::find($request->user_id);
            if (!$user) {
                return $this->errorResponse('errors.user_not_found', [], 404);
            }

            // Get or create address snapshot
            $addressSnapshot = [];
            if ($request->filled('address_id')) {
                $address = \App\Models\UserAddress::where('id', $request->address_id)
                    ->where('user_id', $user->id)
                    ->first();

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
            } else {
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
                    'user_id' => $user->id,
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
                        'price' => $itemData['price'],
                        'special_instructions' => $itemData['special_instructions'],
                    ]);

                    // Create order item options
                    foreach ($itemData['options'] as $optionData) {
                        $productOptionValue = \App\Models\ProductOptionValue::with('optionValue.optionGroup')->find($optionData['product_option_value_id']);
                        
                        \App\Models\OrderItemOption::create([
                            'order_item_id' => $orderItem->id,
                            'product_option_value_id' => $productOptionValue->id,
                            'option_snapshot' => [
                                'option_group_name_en' => $productOptionValue->optionValue->optionGroup->name_en ?? null,
                                'option_group_name_ar' => $productOptionValue->optionValue->optionGroup->name_ar ?? null,
                                'option_value_name_en' => $productOptionValue->optionValue->name_en ?? null,
                                'option_value_name_ar' => $productOptionValue->optionValue->name_ar ?? null,
                            ],
                            'price' => $optionData['price'],
                        ]);
                    }

                    // Create order item addons
                    foreach ($itemData['addons'] as $addonData) {
                        $addon = \App\Models\Addon::find($addonData['addon_id']);
                        
                        \App\Models\OrderItemAddon::create([
                            'order_item_id' => $orderItem->id,
                            'addon_id' => $addon->id,
                            'addon_snapshot' => [
                                'name_en' => $addon->name_en,
                                'name_ar' => $addon->name_ar,
                            ],
                            'quantity' => $addonData['quantity'],
                            'price' => $addonData['price'],
                        ]);
                    }
                }

                \Illuminate\Support\Facades\DB::commit();

                // Load relationships for response
                $order->load(['user', 'items.options', 'items.addons']);

                return $this->successResponse($order, 'success.order_created', 201);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', ['error' => $e->getMessage()], 500);
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
}
