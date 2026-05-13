<?php

namespace App\Http\Controllers\Api\V2\Admin;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of permissions.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 50);
            $guard = $request->get('guard', 'admins');
            $search = $request->get('search');

            $query = Permission::where('guard_name', $guard);

            if ($search) {
                $query->where('name', 'like', "%{$search}%");
            }

            $permissions = $query->paginate($perPage);

            return $this->successResponse($permissions, 'success.permissions_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get permissions grouped by category.
     */
    public function getByCategory(Request $request): JsonResponse
    {
        try {
            $guard = $request->get('guard', 'admins');
            
            $permissions = Permission::where('guard_name', $guard)
                                   ->get()
                                   ->groupBy(function ($permission) {
                                       // Extract category from permission name (e.g., 'admin-users.view' -> 'admin-users')
                                       return explode('.', $permission->name)[0];
                                   });

            return $this->successResponse($permissions, 'success.permissions_by_category_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Display the specified permission.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $permission = Permission::with('roles')->findOrFail($id);
            return $this->successResponse($permission, 'success.permission_retrieved');
        } catch (\Exception $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        }
    }
}
