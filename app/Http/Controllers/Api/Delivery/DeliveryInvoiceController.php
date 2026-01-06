<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Http\Controllers\Controller;
use App\Models\DeliveryInvoice;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryInvoiceController extends Controller
{
    use ApiResponse;

    /**
     * Get all invoices for the authenticated delivery driver
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $delivery = Auth::guard('deliveries')->user();

            if (!$delivery) {
                return $this->errorResponse('errors.unauthenticated', [], 401);
            }

            $query = DeliveryInvoice::where('delivery_id', $delivery->id)
                ->withCount('orders')
                ->orderBy('created_at', 'desc');

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Pagination
            $perPage = (int) $request->get('per_page', 15);
            if ($perPage <= 0) {
                $perPage = 15;
            }
            if ($perPage > 100) {
                $perPage = 100;
            }

            $invoices = $query->paginate($perPage);

            return $this->successResponse($invoices, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get a specific invoice with its related orders
     */
    public function show($id): JsonResponse
    {
        try {
            $delivery = Auth::guard('deliveries')->user();

            if (!$delivery) {
                return $this->errorResponse('errors.unauthenticated', [], 401);
            }

            $invoice = DeliveryInvoice::where('delivery_id', $delivery->id)
                ->with(['orders' => function ($query) {
                    $query->with(['user', 'store', 'items'])
                        ->orderBy('created_at', 'desc');
                }])
                ->find($id);

            if (!$invoice) {
                return $this->errorResponse('errors.invoice_not_found', [], 404);
            }

            return $this->successResponse($invoice, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
