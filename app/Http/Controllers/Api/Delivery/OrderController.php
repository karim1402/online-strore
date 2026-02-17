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

    public function available(Request $request): JsonResponse
    {
        try {
            $delivery = auth('deliveries')->user();

            // If delivery person already has an active order, they shouldn't see available orders
            $activeOrder = Order::where('delivery_id', $delivery->id)
                ->whereIn('simple_status', ['ready_to_pick', 'in_delivery'])
                ->first();

            if ($activeOrder) {
                return $this->successResponse(new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15), 'success.data_retrieved');
            }

            $query = Order::with(['store', 'branch', 'user', 'items'])
                ->whereNull('delivery_id')
                ->where('simple_status', 'ready_to_pick')
                ->where('is_delivery', true)
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

            // Check if delivery person is available
            if (!$delivery->availability) {
                return $this->errorResponse('errors.delivery_not_available', [], 400);
            }

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

            // Notify client that order has been picked up
            $this->notifyClientOrderPicked($order, $delivery);

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

    /**
     * Send FCM notification to the client when order is picked up
     */
    private function notifyClientOrderPicked(Order $order, $delivery): void
    {
        try {
            // Check if the order has a user with FCM token
            if (!$order->user || !$order->user->fcm_token) {
                \Illuminate\Support\Facades\Log::info('No FCM token for client of order: ' . $order->order_number);
                return;
            }

            $fcmService = app(\App\Services\FcmService::class);

            $title = 'Your Order is On The Way!';
            $body = "Order #{$order->order_number} has been picked up by {$delivery->name} and is on the way to you.";

            $data = [
                'type' => 'order_picked',
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
                'delivery_name' => $delivery->name,
            ];

            $success = $fcmService->sendNotification($order->user->fcm_token, $title, $body, $data);

            \Illuminate\Support\Facades\Log::info('Client notification sent for order pickup: ' . $order->order_number, [
                'user_id' => $order->user->id,
                'delivery_id' => $delivery->id,
                'success' => $success,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send client pickup notification: ' . $e->getMessage());
        }
    }
}
