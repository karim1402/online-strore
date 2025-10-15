<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserController extends Controller
{
    use ApiResponse;

    /**
     * List admins with pagination, search and status filter.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            $search = $request->get('search');
            $status = $request->get('status'); // true/false/null
            $role = $request->get('role_id');

            $query = Admin::with('roles')->orderBy('id', 'desc');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            if (!is_null($status)) {
                $query->where('status', filter_var($status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
            }

            if ($role) {
                $query->whereHas('roles', function ($q) use ($role) {
                    $q->where('id', $role);
                });
            }

            $admins = $query->paginate($perPage);

            return $this->successResponse($admins, 'success.admin_users_retrieved');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Create a new admin user.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'name' => 'required|string|between:2,100',
                'email' => 'required|string|email|max:100|unique:admins,email',
                'password' => 'required|string|min:6',
                'role_id' => 'required|integer|exists:roles,id',
                'phone' => 'nullable|string|max:20',
                'status' => 'boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $data = $validator->validated();
            $roleId = $data['role_id'];
            unset($data['role_id']);
            
            $data['password'] = Hash::make($data['password']);
            $data['status'] = $data['status'] ?? true;

            $admin = Admin::create($data);
            
            // Assign single role to the admin
            $role = Role::where('id', $roleId)
                       ->where('guard_name', 'admins')
                       ->first();
            if ($role) {
                $admin->assignRole($role);
            }

            // Log the creation activity
            $currentAdmin = auth('admins')->user();
            activity('admin')
                ->causedBy($currentAdmin)
                ->performedOn($admin)
                ->withProperties([
                    'action' => 'created',
                    'created_by' => $currentAdmin->name,
                    'admin_name' => $admin->name,
                    'admin_email' => $admin->email,
                    'role' => $role->name ?? null,
                ])
                ->log('Admin user created');

            return $this->successResponse($admin->load('roles'), 'success.admin_user_created', [], 201);
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Show a single admin user.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $admin = Admin::with('roles')->findOrFail($id);
            return $this->successResponse($admin, 'success.admin_user_retrieved');
        } catch (\Throwable $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        }
    }

    /**
     * Update an admin user.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $admin = Admin::findOrFail($id);

            $validator = ValidationService::make($request->all(), [
                'name' => 'nullable|string|between:2,100',
                'email' => 'nullable|string|email|max:100|unique:admins,email,' . $id,
                'password' => 'nullable|string|min:6',
                'role_id' => 'nullable|integer|exists:roles,id',
                'phone' => 'nullable|string|max:20',
                'status' => 'boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $data = $validator->validated();
            $roleId = $data['role_id'] ?? null;
            unset($data['role_id']);

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $admin->update($data);
            
            // Update role if provided
            if ($roleId !== null) {
                $role = Role::where('id', $roleId)
                           ->where('guard_name', 'admins')
                           ->first();
                if ($role) {
                    $admin->syncRoles([$role]); // Sync with single role
                }
            }

            return $this->successResponse($admin->fresh()->load('roles'), 'success.admin_user_updated');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Delete an admin user.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $admin = Admin::findOrFail($id);
            
            // Log the deletion activity before deleting
            $currentAdmin = auth('admins')->user();
            activity('admin')
                ->causedBy($currentAdmin)
                ->performedOn($admin)
                ->withProperties([
                    'action' => 'deleted',
                    'deleted_by' => $currentAdmin->name,
                    'admin_name' => $admin->name,
                    'admin_email' => $admin->email,
                ])
                ->log('Admin user deleted');
            
            $admin->delete();
            return $this->successResponse(null, 'success.admin_user_deleted');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Toggle admin status.
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $admin = Admin::findOrFail($id);
            $oldStatus = $admin->status;
            $newStatus = !$admin->status;
            $admin->update(['status' => $newStatus]);
            
            // Log the status change activity
            $currentAdmin = auth('admins')->user();
            activity('admin')
                ->causedBy($currentAdmin)
                ->performedOn($admin)
                ->withProperties([
                    'action' => 'status_changed',
                    'changed_by' => $currentAdmin->name,
                    'admin_name' => $admin->name,
                    'old_status' => $oldStatus ? 'active' : 'inactive',
                    'new_status' => $newStatus ? 'active' : 'inactive',
                ])
                ->log('Admin user status toggled');
            
            return $this->successResponse($admin->fresh(), 'success.admin_user_status_updated');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
