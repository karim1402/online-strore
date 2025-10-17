<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Facades\Activity;

class AddressController extends Controller
{
    use ApiResponse;

    /**
     * Get all addresses for the authenticated user
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('api')->user();
            
            $query = UserAddress::where('user_id', $user->id);

            // Filter by address type
            if ($request->has('address_type')) {
                $query->where('address_type', $request->address_type);
            }

            // Filter by default address
            if ($request->has('is_default')) {
                $query->where('is_default', $request->boolean('is_default'));
            }

            // Search by address name, building name, street name, or landmark
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('address_name', 'like', "%{$search}%")
                        ->orWhere('building_name', 'like', "%{$search}%")
                        ->orWhere('street_name', 'like', "%{$search}%")
                        ->orWhere('landmark', 'like', "%{$search}%");
                });
            }

            $addresses = $query->orderBy('is_default', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse($addresses, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get a single address by ID
     */
    public function show($id): JsonResponse
    {
        try {
            $user = Auth::guard('api')->user();
            
            $address = UserAddress::where('user_id', $user->id)
                ->find($id);

            if (!$address) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            return $this->successResponse($address, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Create a new address for the user
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = Auth::guard('api')->user();

            $validator = ValidationService::make($request->all(), [
                'address_name' => 'nullable|string|max:255',
                'address_type' => 'required|in:villa,apartment,office',
                'building_name' => 'required|string|max:255',
                'apartment_number' => 'nullable|string|max:50',
                'floor_number' => 'nullable|string|max:50',
                'street_name' => 'required|string|max:255',
                'landmark' => 'nullable|string|max:255',
                'phone' => 'required|string|max:20',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'is_default' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            // If setting as default, unset other default addresses for this user
            if ($request->boolean('is_default', false)) {
                UserAddress::where('user_id', $user->id)
                    ->update(['is_default' => false]);
            }

            // If no default address exists for this user, make this one default
            $hasDefaultAddress = UserAddress::where('user_id', $user->id)
                ->where('is_default', true)
                ->exists();
            
            $isDefault = $request->boolean('is_default', false);
            if (!$hasDefaultAddress) {
                $isDefault = true;
            }

            $address = UserAddress::create([
                'user_id' => $user->id,
                'address_name' => $request->address_name,
                'address_type' => $request->address_type,
                'building_name' => $request->building_name,
                'apartment_number' => $request->apartment_number,
                'floor_number' => $request->floor_number,
                'street_name' => $request->street_name,
                'landmark' => $request->landmark,
                'phone' => $request->phone,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'is_default' => $isDefault,
            ]);

            // Log activity
            activity('user_address')
                ->causedBy($user)
                ->performedOn($address)
                ->withProperties([
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'address_type' => $address->address_type,
                    'building_name' => $address->building_name,
                    'street_name' => $address->street_name,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ])
                ->log('User created new address');

            DB::commit();

            return $this->successResponse($address, 'success.data_created', 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Update an existing address
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $user = Auth::guard('api')->user();
            
            $address = UserAddress::where('user_id', $user->id)
                ->find($id);

            if (!$address) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'address_name' => 'nullable|string|max:255',
                'address_type' => 'sometimes|required|in:villa,apartment,office',
                'building_name' => 'sometimes|required|string|max:255',
                'apartment_number' => 'nullable|string|max:50',
                'floor_number' => 'nullable|string|max:50',
                'street_name' => 'sometimes|required|string|max:255',
                'landmark' => 'nullable|string|max:255',
                'phone' => 'sometimes|required|string|max:20',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'is_default' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            // Store old values for logging
            $oldValues = $address->toArray();

            // If setting as default, unset other default addresses for this user
            if ($request->has('is_default') && $request->boolean('is_default')) {
                UserAddress::where('user_id', $user->id)
                    ->where('id', '!=', $address->id)
                    ->update(['is_default' => false]);
            }

            $address->update($request->only([
                'address_name',
                'address_type',
                'building_name',
                'apartment_number',
                'floor_number',
                'street_name',
                'landmark',
                'phone',
                'latitude',
                'longitude',
                'is_default',
            ]));

            // Log activity
            activity('user_address')
                ->causedBy($user)
                ->performedOn($address)
                ->withProperties([
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'old_values' => $oldValues,
                    'new_values' => $address->fresh()->toArray(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ])
                ->log('User updated address');

            DB::commit();

            return $this->successResponse($address->fresh(), 'success.data_updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Delete an address
     */
    public function destroy($id): JsonResponse
    {
        try {
            $user = Auth::guard('api')->user();
            
            $address = UserAddress::where('user_id', $user->id)
                ->find($id);

            if (!$address) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            DB::beginTransaction();

            $wasDefault = $address->is_default;
            $addressData = $address->toArray();

            // Log activity before deletion
            activity('user_address')
                ->causedBy($user)
                ->performedOn($address)
                ->withProperties([
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'deleted_address' => $addressData,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ])
                ->log('User deleted address');

            $address->delete();

            // If deleted address was default, set another address as default
            if ($wasDefault) {
                $newDefaultAddress = UserAddress::where('user_id', $user->id)
                    ->where('is_active', true)
                    ->first();
                
                if ($newDefaultAddress) {
                    $newDefaultAddress->update(['is_default' => true]);
                }
            }

            DB::commit();

            return $this->successResponse(null, 'success.data_deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Set an address as default
     */
    public function setDefault($id): JsonResponse
    {
        try {
            $user = Auth::guard('api')->user();
            
            $address = UserAddress::where('user_id', $user->id)
                ->find($id);

            if (!$address) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            DB::beginTransaction();

            // Unset other default addresses
            UserAddress::where('user_id', $user->id)
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);

            // Set this address as default
            $address->update(['is_default' => true]);

            // Log activity
            activity('user_address')
                ->causedBy($user)
                ->performedOn($address)
                ->withProperties([
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'address_name' => $address->address_name,
                    'address_type' => $address->address_type,
                    'building_name' => $address->building_name,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ])
                ->log('User set address as default');

            DB::commit();

            return $this->successResponse($address->fresh(), 'success.data_updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get the default address for the user
     */
    public function getDefault(): JsonResponse
    {
        try {
            $user = Auth::guard('api')->user();
            
            $address = UserAddress::where('user_id', $user->id)
                ->where('is_default', true)
                ->first();

            if (!$address) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            return $this->successResponse($address, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
