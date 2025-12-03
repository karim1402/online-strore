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
     * Get all unassigned orders that are ready to pick
     * (delivery_id IS NULL and simple_status = 'ready_to_pick').
     */
    public function available(Request $request): JsonResponse
    {
        try {
            $query = Order::with(['store', 'user', 'items'])
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
            $order = Order::with(['store', 'user', 'items.options', 'items.addons'])
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

            $order = Order::whereNull('delivery_id')
                ->where('simple_status', 'ready_to_pick')
                ->find($id);

            if (!$order) {
                return $this->errorResponse('errors.order_not_found', [], 404);
            }

            $order->delivery_id = $delivery->id;
            $order->simple_status = 'in_delivery';
            $order->save();

            $order->load(['store', 'user', 'items.options', 'items.addons', 'delivery']);

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

            $order->load(['store', 'user', 'items.options', 'items.addons', 'delivery']);

            return $this->successResponse($order, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    
}
