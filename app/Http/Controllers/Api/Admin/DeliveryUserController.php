<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DeliveryUserController extends Controller
{
    use ApiResponse;

    /**
     * List delivery users with pagination, search and filters.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            $search = $request->get('search');
            $status = $request->get('status'); // true/false/null
            $availability = $request->get('availability'); // true/false/null
            $vehicleType = $request->get('vehicle_type');

            $query = Delivery::orderBy('id', 'desc');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            if (!is_null($status)) {
                $query->where('status', filter_var($status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
            }

            if (!is_null($availability)) {
                $query->where('availability', filter_var($availability, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
            }

            if ($vehicleType) {
                $query->where('vehicle_type', $vehicleType);
            }

            $deliveryUsers = $query->paginate($perPage);

            return $this->successResponse($deliveryUsers, 'success.delivery_users_retrieved');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Create a new delivery user.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'name' => 'required|string|between:2,100',
                'email' => 'required|string|email|max:100|unique:deliveries,email',
                'password' => 'required|string|min:6',
                'phone' => 'required|string|max:20',
                'vehicle_type' => 'nullable|string|max:50',
                'vehicle_number' => 'nullable|string|max:50',
                'license_number' => 'nullable|string|max:50',
                'address' => 'nullable|string',
                'status' => 'boolean',
                'availability' => 'boolean',
                'shift_start_time' => 'nullable|date_format:H:i:s,H:i',
                'shift_end_time' => 'nullable|date_format:H:i:s,H:i',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $data = $validator->validated();
            $data['password'] = Hash::make($data['password']);
            $data['status'] = $data['status'] ?? true;
            $data['availability'] = $data['availability'] ?? true;

            $deliveryUser = Delivery::create($data);

            // Log the creation activity
            $currentAdmin = auth('admins')->user();
            activity('delivery_management')
                ->causedBy($currentAdmin)
                ->performedOn($deliveryUser)
                ->withProperties([
                    'action' => 'created',
                    'created_by' => $currentAdmin->name,
                    'delivery_user_name' => $deliveryUser->name,
                    'delivery_user_email' => $deliveryUser->email,
                ])
                ->log('Delivery user created');

            return $this->successResponse($deliveryUser, 'success.delivery_user_created', [], 201);
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Show a single delivery user.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $deliveryUser = Delivery::findOrFail($id);
            return $this->successResponse($deliveryUser, 'success.delivery_user_retrieved');
        } catch (\Throwable $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        }
    }

    /**
     * Update a delivery user.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $deliveryUser = Delivery::findOrFail($id);

            $validator = ValidationService::make($request->all(), [
                'name' => 'nullable|string|between:2,100',
                'email' => 'nullable|string|email|max:100|unique:deliveries,email,' . $id,
                'password' => 'nullable|string|min:6',
                'phone' => 'nullable|string|max:20',
                'vehicle_type' => 'nullable|string|max:50',
                'vehicle_number' => 'nullable|string|max:50',
                'license_number' => 'nullable|string|max:50',
                'address' => 'nullable|string',
                'status' => 'boolean',
                'availability' => 'boolean',
                'shift_start_time' => 'nullable|date_format:H:i:s,H:i',
                'shift_end_time' => 'nullable|date_format:H:i:s,H:i',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $data = $validator->validated();

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $deliveryUser->update($data);

            // Log the update activity
            $currentAdmin = auth('admins')->user();
            activity('delivery_management')
                ->causedBy($currentAdmin)
                ->performedOn($deliveryUser)
                ->withProperties([
                    'action' => 'updated',
                    'updated_by' => $currentAdmin->name,
                    'delivery_user_name' => $deliveryUser->name,
                    'delivery_user_email' => $deliveryUser->email,
                ])
                ->log('Delivery user updated');

            return $this->successResponse($deliveryUser->fresh(), 'success.delivery_user_updated');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Delete a delivery user.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $deliveryUser = Delivery::findOrFail($id);
            
            // Log the deletion activity before deleting
            $currentAdmin = auth('admins')->user();
            activity('delivery_management')
                ->causedBy($currentAdmin)
                ->performedOn($deliveryUser)
                ->withProperties([
                    'action' => 'deleted',
                    'deleted_by' => $currentAdmin->name,
                    'delivery_user_name' => $deliveryUser->name,
                    'delivery_user_email' => $deliveryUser->email,
                ])
                ->log('Delivery user deleted');
            
            $deliveryUser->delete();
            return $this->successResponse(null, 'success.delivery_user_deleted');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Toggle delivery user status.
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $deliveryUser = Delivery::findOrFail($id);
            $oldStatus = $deliveryUser->status;
            $newStatus = !$deliveryUser->status;
            $deliveryUser->update(['status' => $newStatus]);
            
            // Log the status change activity
            $currentAdmin = auth('admins')->user();
            activity('delivery_management')
                ->causedBy($currentAdmin)
                ->performedOn($deliveryUser)
                ->withProperties([
                    'action' => 'status_changed',
                    'changed_by' => $currentAdmin->name,
                    'delivery_user_name' => $deliveryUser->name,
                    'old_status' => $oldStatus ? 'active' : 'inactive',
                    'new_status' => $newStatus ? 'active' : 'inactive',
                ])
                ->log('Delivery user status toggled');
            
            return $this->successResponse($deliveryUser->fresh(), 'success.delivery_user_status_updated');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
