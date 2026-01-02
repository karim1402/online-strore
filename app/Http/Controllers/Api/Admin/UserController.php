<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * List users with pagination, search.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            $search = $request->get('search');

            $query = User::orderBy('id', 'desc');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                      
                });
            }

            // Filter by date range
            if ($request->has('start_date')) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->has('end_date')) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            $users = $query->paginate($perPage);

            return $this->successResponse($users, 'success.users_retrieved');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Create a new user.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'name' => 'required|string|between:2,100',
                'email' => 'required|string|email|max:100|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $data = $validator->validated();
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            // Log the creation activity
            $currentAdmin = auth('admins')->user();
            activity('user')
                ->causedBy($currentAdmin)
                ->performedOn($user)
                ->withProperties([
                    'action' => 'created',
                    'created_by' => $currentAdmin->name,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                ])
                ->log('User created by admin');

            return $this->successResponse($user, 'success.user_created', [], 201);
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Show a single user.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            return $this->successResponse($user, 'success.user_retrieved');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Update a user.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validator = ValidationService::make($request->all(), [
                'name' => 'nullable|string|between:2,100',
                'email' => 'nullable|string|email|max:100|unique:users,email,' . $id,
                'password' => 'nullable|string|min:6',
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

            $user->update($data);

            // Log the update activity
            $currentAdmin = auth('admins')->user();
            activity('user')
                ->causedBy($currentAdmin)
                ->performedOn($user)
                ->withProperties([
                    'action' => 'updated',
                    'updated_by' => $currentAdmin->name,
                    'user_name' => $user->name,
                ])
                ->log('User updated by admin');

            return $this->successResponse($user->fresh(), 'success.user_updated');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Delete a user.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            
            // Log the deletion activity before deleting
            $currentAdmin = auth('admins')->user();
            activity('user')
                ->causedBy($currentAdmin)
                ->performedOn($user)
                ->withProperties([
                    'action' => 'deleted',
                    'deleted_by' => $currentAdmin->name,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                ])
                ->log('User deleted by admin');
            
            $user->delete();
            return $this->successResponse(null, 'success.user_deleted');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
