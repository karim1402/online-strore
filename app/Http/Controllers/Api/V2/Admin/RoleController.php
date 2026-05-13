<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Services\ValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of roles.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 15);
            $guard = $request->get('guard', 'admins');
            $search = $request->get('search');

            $query = Role::where('guard_name', $guard)->with('permissions');

            if ($search) {
                $query->where('name', 'like', "%{$search}%");
            }

            $roles = $query->paginate($perPage);

            return $this->successResponse($roles, 'success.roles_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'name' => 'required|string|max:255|unique:roles,name',
                'guard_name' => 'required|string|in:admins,vendors',
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,name'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $data = $validator->validated();
            $role = Role::create([
                'name' => $data['name'],
                'guard_name' => $data['guard_name']
            ]);

            if (isset($data['permissions'])) {
                $permissions = Permission::whereIn('name', $data['permissions'])
                                       ->where('guard_name', $data['guard_name'])
                                       ->get();
                $role->syncPermissions($permissions);
            }

            return $this->successResponse($role->load('permissions'), 'success.role_created', [], 201);
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Display the specified role.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $role = Role::with('permissions')->findOrFail($id);
            return $this->successResponse($role, 'success.role_retrieved');
        } catch (\Exception $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        }
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $role = Role::findOrFail($id);

            $validator = ValidationService::make($request->all(), [
                'name' => 'required|string|max:255|unique:roles,name,' . $id,
                'permissions' => 'array',
                'permissions.*' => 'exists:permissions,name'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $data = $validator->validated();
            $role->update(['name' => $data['name']]);

            if (isset($data['permissions'])) {
                $permissions = Permission::whereIn('name', $data['permissions'])
                                       ->where('guard_name', $role->guard_name)
                                       ->get();
                $role->syncPermissions($permissions);
            }

            return $this->successResponse($role->load('permissions'), 'success.role_updated');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $role = Role::findOrFail($id);
            
            // Check if role is assigned to any users
            if ($role->users()->count() > 0) {
                return $this->errorResponse('errors.role_has_users', [], 422);
            }

            $role->delete();
            return $this->successResponse(null, 'success.role_deleted');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get permissions for a specific guard.
     */
    public function getPermissions(Request $request): JsonResponse
    {
        try {
            $guard = $request->get('guard', 'admins');
            $permissions = Permission::where('guard_name', $guard)->get();

            return $this->successResponse($permissions, 'success.permissions_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get all roles with just id and name for dropdown/select purposes.
     */
    public function getRolesList(Request $request): JsonResponse
    {
        try {
            $guard = $request->get('guard', 'admins');
            $roles = Role::where('guard_name', $guard)
                        ->select('id', 'name')
                        ->orderBy('name')
                        ->get();

            return $this->successResponse($roles, 'success.roles_list_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
