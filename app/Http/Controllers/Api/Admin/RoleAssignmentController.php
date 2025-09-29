<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use App\Services\ValidationService;
use App\Models\Admin;
use App\Models\StoreUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleAssignmentController extends Controller
{
    use ApiResponse;

    /**
     * Get user's roles.
     */
    public function getUserRoles(Request $request, int $userId): JsonResponse
    {
        try {
            $guard = $request->get('guard', 'admins');
            $user = $this->getUser($userId, $guard);

            if (!$user) {
                return $this->notFoundResponse('errors.user_not_found');
            }

            $roles = $user->roles;
            $permissions = $user->getAllPermissions();

            return $this->successResponse([
                'user' => $user,
                'roles' => $roles,
                'permissions' => $permissions
            ], 'success.user_roles_retrieved');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Assign role to user.
     */
    public function assignRole(Request $request, int $userId): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'guard' => 'required|string|in:admins,store_users',
                'role' => 'required|string|exists:roles,name'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $data = $validator->validated();
            $user = $this->getUser($userId, $data['guard']);

            if (!$user) {
                return $this->notFoundResponse('errors.user_not_found');
            }

            // Check if role exists for the guard
            $role = Role::where('name', $data['role'])
                       ->where('guard_name', $data['guard'])
                       ->first();

            if (!$role) {
                return $this->errorResponse('errors.role_not_found', [], 404);
            }

            $user->assignRole($role);

            return $this->successResponse([
                'user' => $user,
                'roles' => $user->roles,
                'permissions' => $user->getAllPermissions()
            ], 'success.role_assigned');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Remove role from user.
     */
    public function removeRole(Request $request, int $userId): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'guard' => 'required|string|in:admins,store_users',
                'role' => 'required|string'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $data = $validator->validated();
            $user = $this->getUser($userId, $data['guard']);

            if (!$user) {
                return $this->notFoundResponse('errors.user_not_found');
            }

            $user->removeRole($data['role']);

            return $this->successResponse([
                'user' => $user,
                'roles' => $user->roles,
                'permissions' => $user->getAllPermissions()
            ], 'success.role_removed');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Sync user roles (replace all roles with new ones).
     */
    public function syncRoles(Request $request, int $userId): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'guard' => 'required|string|in:admins,store_users',
                'roles' => 'array',
                'roles.*' => 'string|exists:roles,name'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $data = $validator->validated();
            $user = $this->getUser($userId, $data['guard']);

            if (!$user) {
                return $this->notFoundResponse('errors.user_not_found');
            }

            // Get roles for the specific guard
            $roles = Role::whereIn('name', $data['roles'] ?? [])
                        ->where('guard_name', $data['guard'])
                        ->get();

            $user->syncRoles($roles);

            return $this->successResponse([
                'user' => $user,
                'roles' => $user->roles,
                'permissions' => $user->getAllPermissions()
            ], 'success.roles_synced');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get user by ID and guard.
     */
    private function getUser(int $userId, string $guard)
    {
        switch ($guard) {
            case 'admins':
                return Admin::find($userId);
            case 'store_users':
                return StoreUser::find($userId);
            default:
                return null;
        }
    }
}
