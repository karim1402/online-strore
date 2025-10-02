<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\Store;
use App\Models\MainCategory;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    use ApiResponse;
    /**
     * Create a new AuthController instance.
     */
    public function __construct()
    {
        // Middleware will be handled in routes
    }

    /**
     * Register a new vendor with store
     */
    public function register(Request $request): JsonResponse
    {
        // Validate vendor and store data
        $validator = ValidationService::make($request->all(), [
            // Vendor fields
            'name' => 'required|string|between:2,100',
            'email' => 'required|string|email|max:100|unique:vendors',
            'password' => 'required|string|min:6',
            'phone' => 'required|string|max:20',
            
            // Store fields
            'main_category_ids' => 'required|array|min:1',
            'main_category_ids.*' => 'required|integer|exists:main_categories,id',
            'name_en' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'description_en' => 'required|string',
            'description_ar' => 'required|string',
            'store_address' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'logo' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
            'document' => 'required|file|mimes:pdf,jpeg,jpg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        // Verify all main categories exist and are active
        $activeCategories = MainCategory::whereIn('id', $request->main_category_ids)
            ->where('status', true)
            ->pluck('id')
            ->toArray();
            
        if (count($activeCategories) !== count($request->main_category_ids)) {
            return $this->errorResponse('errors.category_not_found', [], 404);
        }

        DB::beginTransaction();
        try {
            // Handle logo upload
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('stores/logos', 'public');
            }

            // Handle document upload
            $documentPath = null;
            if ($request->hasFile('document')) {
                $documentPath = $request->file('document')->store('stores/documents', 'public');
            }

            // Create store first
            $store = Store::create([
                'name_en' => $request->name_en,
                'name_ar' => $request->name_ar,
                'description_en' => $request->description_en,
                'description_ar' => $request->description_ar,
                'address' => $request->store_address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'logo' => $logoPath,
                'document' => $documentPath,
                'status' => false, // Pending approval
            ]);

            // Attach main categories to store
            $store->mainCategories()->attach($request->main_category_ids);

            // Create vendor and assign to store
            $vendor = Vendor::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'store_id' => $store->id,
                'status' => true,
            ]);

            DB::commit();

            // Login vendor
            $token = Auth::guard('vendors')->login($vendor);

            return $this->successResponse([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => Auth::guard('vendors')->factory()->getTTL() * 60,
                'user' => [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'email' => $vendor->email,
                    'phone' => $vendor->phone,
                    'status' => $vendor->status,
                    'store_logo' => $store->logo ? asset('storage/' . $store->logo) : null,
                    'created_at' => $vendor->created_at,
                    'updated_at' => $vendor->updated_at,
                ]
            ], 'success.vendor_registered', [], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded files if store creation failed
            if (isset($logoPath) && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            if (isset($documentPath) && Storage::disk('public')->exists($documentPath)) {
                Storage::disk('public')->delete($documentPath);
            }
            
            return $this->errorResponse('errors.store_creation_failed', [], 500);
        }
    }

    /**
     * Login vendor
     */
    public function login(Request $request): JsonResponse
    {
        $validator = ValidationService::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        $credentials = $request->only('email', 'password');

        if (!$token = Auth::guard('vendors')->attempt($credentials)) {
            return $this->errorResponse('errors.invalid_credentials', [], 401);
        }

        $vendor = Auth::guard('vendors')->user();
        
        // Check if vendor is active
        if (!$vendor->status) {
            Auth::guard('vendors')->logout();
            return $this->errorResponse('errors.account_disabled', [], 403);
        }

        // Get store logo only
        $store = $vendor->store;

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('vendors')->factory()->getTTL() * 60,
            'user' => [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'email' => $vendor->email,
                'phone' => $vendor->phone,
                'status' => $vendor->status,
                'store_logo' => $store?->logo ? asset('storage/' . $store->logo) : null,
                'created_at' => $vendor->created_at,
                'updated_at' => $vendor->updated_at,
            ]
        ], 'success.vendor_logged_in');
    }

    /**
     * Get vendor profile
     */
    public function profile(): JsonResponse
    {
        $vendor = Auth::guard('vendors')->user();
        $store = $vendor->store;

        return $this->successResponse([
            'user' => [
                'id' => $vendor->id,
                'name' => $vendor->name,
                'email' => $vendor->email,
                'phone' => $vendor->phone,
                'status' => $vendor->status,
                // 'store_logo' => $store?->logo ? asset('storage/' . $store->logo) : null,
                'created_at' => $vendor->created_at,
                'updated_at' => $vendor->updated_at,
            ]
        ], 'success.profile_fetched');
    }

    /**
     * Update vendor profile
     */
    public function updateProfile(Request $request): JsonResponse
    {
        $vendor = Auth::guard('vendors')->user();

        $validator = ValidationService::make($request->all(), [
            'name' => 'sometimes|string|between:2,100',
            'email' => 'sometimes|string|email|max:100|unique:vendors,email,' . $vendor->id,
            'phone' => 'sometimes|string|max:20',
            'password' => 'sometimes|string|min:6',
            'current_password' => 'required_with:password|string',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        // If updating password, verify current password
        if ($request->has('password')) {
            if (!Hash::check($request->current_password, $vendor->password)) {
                return $this->errorResponse('errors.current_password_incorrect', [], 400);
            }
        }

        DB::beginTransaction();
        try {
            // Update vendor fields
            if ($request->has('name')) {
                $vendor->name = $request->name;
            }
            if ($request->has('email')) {
                $vendor->email = $request->email;
            }
            if ($request->has('phone')) {
                $vendor->phone = $request->phone;
            }
            if ($request->has('password')) {
                $vendor->password = Hash::make($request->password);
            }

            $vendor->save();

            DB::commit();

            // Get store logo
            $store = $vendor->store;

            return $this->successResponse([
                'user' => [
                    'id' => $vendor->id,
                    'name' => $vendor->name,
                    'email' => $vendor->email,
                    'phone' => $vendor->phone,
                    'status' => $vendor->status,
                    'store_logo' => $store?->logo ? asset('storage/' . $store->logo) : null,
                    'created_at' => $vendor->created_at,
                    'updated_at' => $vendor->updated_at,
                ]
            ], 'success.profile_updated');

        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.profile_update_failed', [], 500);
        }
    }

    /**
     * Logout vendor
     */
    public function logout(): JsonResponse
    {
        Auth::guard('vendors')->logout();

        return $this->successResponse(null, 'success.vendor_logged_out');
    }

    /**
     * Refresh token
     */
    public function refresh(): JsonResponse
    {
        try {
            $token = Auth::guard('vendors')->refresh();
        } catch (\Exception $e) {
            return $this->errorResponse('errors.token_refresh_failed', [], 401);
        }

        return $this->successResponse([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::guard('vendors')->factory()->getTTL() * 60
        ], 'success.token_refreshed');
    }
}
