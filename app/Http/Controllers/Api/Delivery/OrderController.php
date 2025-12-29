<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponse;

    /**
     * Get all orders assigned to the delivery person with pagination and filters
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $delivery = auth('deliveries')->user();

            $query = Order::with(['store', 'branch', 'user', 'items'])
                ->where('delivery_id', $delivery->id)
                ->orderBy('created_at', 'desc');

            // Filter by simple status
            if ($request->filled('status')) {
                $query->where('simple_status', $request->status);
            }

            // Filter by payment status
            // if ($request->filled('payment_status')) {
            //     $query->where('payment_status', $request->payment_status);
            // }

            // Search by order number
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', "%{$search}%");
                });
            }

            // Pagination
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

    /**
     * Get all unassigned orders that are ready to pick
     * (delivery_id IS NULL and simple_status = 'ready_to_pick').
     */
    public function available(Request $request): JsonResponse
    {
        try {
            $query = Order::with(['store', 'branch', 'user', 'items'])
                ->whereNull('delivery_id')
                ->where('simple_status', 'ready_to_pick')
                ->orderBy('created_at', 'desc');

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
            $order = Order::with(['store', 'branch', 'user', 'items.options', 'items.addons'])
                ->whereNull('delivery_id')
                ->where('simple_status', 'ready_to_pick')
                ->find($id);

            if (!$order) {
                return $this->errorResponse('errors.order_not_found', [], 404);
            }

            return $this->successResponse($order, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    public function pick($id): JsonResponse
    {
        try {
            $delivery = auth('deliveries')->user();

            // Check if delivery person already has an active order
            $activeOrder = Order::where('delivery_id', $delivery->id)
                ->whereIn('simple_status', ['ready_to_pick', 'in_delivery'])
                ->first();

            if ($activeOrder) {
                return $this->errorResponse('errors.delivery_has_active_order', [], 400);
            }

            $order = Order::whereNull('delivery_id')
                ->where('simple_status', 'ready_to_pick')
                ->find($id);

            if (!$order) {
                return $this->errorResponse('errors.order_not_found', [], 404);
            }

            $order->delivery_id = $delivery->id;
            // $order->simple_status = 'in_delivery';
            $order->save();

            $order->load(['store', 'branch', 'user', 'items.options', 'items.addons', 'delivery']);

            return $this->successResponse($order, 'order.picked_successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    public function startDelivery($id, Request $request): JsonResponse
    {
        try {
            $delivery = auth('deliveries')->user();

            $order = Order::where('delivery_id', $delivery->id)
                ->where('simple_status', 'ready_to_pick')
                ->find($id);

            if (!$order) {
                return $this->errorResponse('errors.order_not_found', [], 404);
            }

            // Validate image if provided
            if ($request->hasFile('order_pickup_image')) {
                $request->validate([
                    'order_pickup_image' => 'required|image|mimes:jpeg,png,jpg|max:5120', // 5MB max
                ]);

                // Store the image
                $image = $request->file('order_pickup_image');
                $imageName = 'pickup_' . $order->order_number . '_' . time() . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('orders/pickup', $imageName, 'public');

                // Save image path to order
                $order->order_pickup_image = $imagePath;
            }

            $order->simple_status = 'in_delivery';
            $order->save();

            $order->load(['store', 'branch', 'user', 'items.options', 'items.addons', 'delivery']);

            return $this->successResponse($order, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    public function deliver($id): JsonResponse
    {
        try {
            $delivery = auth('deliveries')->user();

            $order = Order::where('delivery_id', $delivery->id)
                ->where('simple_status', 'in_delivery')
                ->find($id);

            if (!$order) {
                return $this->errorResponse('errors.order_not_found', [], 404);
            }

            $order->simple_status = 'delivered';
            $order->save();

            $order->load(['store', 'branch', 'user', 'items.options', 'items.addons', 'delivery']);

            return $this->successResponse($order, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get the total amount of cash that has not been handed over by the delivery person
     */
    public function pendingCashHandover(Request $request): JsonResponse
    {
        try {
            $delivery = auth('deliveries')->user();

            $totalAmount = Order::where('delivery_id', $delivery->id)
                ->where('is_cash_handed_over', false)
                ->sum('total');

            return $this->successResponse([
                'total_amount' => (float) $totalAmount
            ], 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
