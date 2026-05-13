<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use App\Services\LocalizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    /**
     * Display a listing of the vouchers.
     */
    public function index(Request $request)
    {
        $query = Voucher::with('module');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('code', 'like', "%{$search}%");
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->filled('module_id')) {
            $query->where('module_id', $request->input('module_id'));
        }

        $vouchers = $query->orderBy('created_at', 'desc')->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => $vouchers,
        ]);
    }

    /**
     * Store a newly created voucher in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:vouchers,code',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'module_id' => 'nullable|exists:modules,id',
            'min_user_orders' => 'nullable|integer|min:1',
            'min_user_spend' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $voucher = Voucher::create($request->all());
        $voucher->load('module');

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.created'),
            'data' => $voucher,
        ], 201);
    }

    /**
     * Display the specified voucher.
     */
    public function show($id)
    {
        $voucher = Voucher::with('module')->find($id);

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Voucher']),
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => $voucher,
        ]);
    }

    /**
     * Update the specified voucher in storage.
     */
    public function update(Request $request, $id)
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Voucher']),
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'code' => 'sometimes|required|string|unique:vouchers,code,' . $id,
            'type' => 'sometimes|required|in:fixed,percentage',
            'value' => 'sometimes|required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_limit_per_user' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
            'module_id' => 'nullable|exists:modules,id',
            'min_user_orders' => 'nullable|integer|min:1',
            'min_user_spend' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $voucher->update($request->all());
        $voucher->load('module');

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.updated'),
            'data' => $voucher,
        ]);
    }

    /**
     * Remove the specified voucher from storage.
     */
    public function destroy($id)
    {
        $voucher = Voucher::find($id);

        if (!$voucher) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Voucher']),
            ], 404);
        }

        $voucher->delete();

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.deleted'),
        ]);
    }
}
