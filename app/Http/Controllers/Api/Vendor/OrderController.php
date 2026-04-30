<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        try {
            $vendor = Auth::guard('vendors')->user();

            if (!$vendor || !$vendor->store_id) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $query = Order::with(['user', 'items'])
                ->where('store_id', $vendor->store_id)
                ->orderBy('created_at', 'desc');

            if ($request->filled('status')) {
                $query->where('simple_status', $request->status);
            }

            // if ($request->filled('order_status')) {
            //     $query->where('order_status', $request->order_status);
            // }

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
            $vendor = Auth::guard('vendors')->user();

            if (!$vendor || !$vendor->store_id) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $order = Order::with(['user', 'store', 'items.options', 'items.addons'])
                ->where('store_id', $vendor->store_id)
                ->find($id);

            if (!$order) {
                return $this->errorResponse('errors.order_not_found', [], 404);
            }

            return $this->successResponse($order, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    public function markReadyToPick($id): JsonResponse
    {
        try {
            $vendor = Auth::guard('vendors')->user();

            if (!$vendor || !$vendor->store_id) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $order = Order::where('store_id', $vendor->store_id)->find($id);

            if (!$order) {
                return $this->errorResponse('errors.order_not_found', [], 404);
            }

            $order->simple_status = 'ready_to_pick';
            $order->save();

            $order->load(['user', 'store', 'items.options', 'items.addons']);

            return $this->successResponse($order, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    public function cancel($id): JsonResponse
    {
        try {
            $vendor = Auth::guard('vendors')->user();

            if (!$vendor || !$vendor->store_id) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $order = Order::where('store_id', $vendor->store_id)
                ->whereNull('delivery_id')
                ->find($id);

            if (!$order) {
                return $this->errorResponse('errors.order_not_found', [], 404);
            }

            if (!in_array($order->simple_status, ['in_progress', 'ready_to_pick'])) {
                return $this->errorResponse('errors.order_cannot_cancel', [], 400);
            }

            $order->simple_status = 'cancelled';
            $order->save();

            $order->restoreStock();

            $order->load(['user', 'store', 'items.options', 'items.addons']);

            return $this->successResponse($order, 'order.cancelled_successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
