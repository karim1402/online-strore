<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\VendorInvoice;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VendorInvoiceController extends Controller
{
    use ApiResponse;

    /**
     * Get all invoices for the vendor's store
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $vendor = Auth::guard('vendors')->user();

            if (!$vendor || !$vendor->store_id) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $query = VendorInvoice::where('store_id', $vendor->store_id)
                ->withCount('orders')
                ->orderBy('created_at', 'desc');

            // Filter by status
            if ($request->filled('status')) {
                $query->where('simple_status', $request->status);
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
            $vendor = Auth::guard('vendors')->user();

            if (!$vendor || !$vendor->store_id) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $invoice = VendorInvoice::where('store_id', $vendor->store_id)
                ->with(['orders' => function ($query) {
                    $query->with(['user', 'items'])
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
