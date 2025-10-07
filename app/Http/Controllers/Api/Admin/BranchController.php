<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Store;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BranchController extends Controller
{
    use ApiResponse;

    /**
     * Get all branches with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Branch::with('store');

            // Filter by store
            if ($request->filled('store_id')) {
                $query->where('store_id', $request->store_id);
            }

            // Filter by active status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            // Filter by main branch
            if ($request->has('is_main')) {
                $query->where('is_main', $request->boolean('is_main'));
            }

            // Search by name or address
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            }

            $branches = $query->orderBy('created_at', 'desc')->paginate(15);

            return $this->successResponse($branches, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get a single branch by ID
     */
    public function show($id): JsonResponse
    {
        try {
            $branch = Branch::with('store')->find($id);

            if (!$branch) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            return $this->successResponse($branch, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Create a new branch
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'store_id' => 'required|integer|exists:stores,id',
                'name_en' => 'required|string|max:255',
                'name_ar' => 'required|string|max:255',
                'address' => 'required|string',
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'phone' => 'nullable|string|max:20',
                'description_en' => 'nullable|string',
                'description_ar' => 'nullable|string',
                'is_main' => 'nullable|boolean',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            // Check if store exists
            $store = Store::find($request->store_id);
            if (!$store) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            // If setting as main, unset other main branches for this store
            if ($request->boolean('is_main', false)) {
                Branch::where('store_id', $request->store_id)
                    ->update(['is_main' => false]);
            }

            // If no main branch exists for this store, make this one main
            $hasMainBranch = Branch::where('store_id', $request->store_id)
                ->where('is_main', true)
                ->exists();

            $branch = Branch::create([
                'store_id' => $request->store_id,
                'name_en' => $request->name_en,
                'name_ar' => $request->name_ar,
                'address' => $request->address,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'phone' => $request->phone,
                'description_en' => $request->description_en,
                'description_ar' => $request->description_ar,
                'is_main' => $request->boolean('is_main', !$hasMainBranch),
                'is_active' => $request->boolean('is_active', true),
            ]);

            $branch->load('store');

            DB::commit();

            return $this->successResponse($branch, 'success.branch_created', [], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Update a branch
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $branch = Branch::find($id);

            if (!$branch) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'name_en' => 'nullable|string|max:255',
                'name_ar' => 'nullable|string|max:255',
                'address' => 'nullable|string',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'phone' => 'nullable|string|max:20',
                'description_en' => 'nullable|string',
                'description_ar' => 'nullable|string',
                'is_main' => 'nullable|boolean',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            // Update branch fields
            if ($request->filled('name_en')) {
                $branch->name_en = $request->name_en;
            }
            if ($request->filled('name_ar')) {
                $branch->name_ar = $request->name_ar;
            }
            if ($request->filled('address')) {
                $branch->address = $request->address;
            }
            if ($request->filled('latitude')) {
                $branch->latitude = $request->latitude;
            }
            if ($request->filled('longitude')) {
                $branch->longitude = $request->longitude;
            }
            if ($request->filled('phone')) {
                $branch->phone = $request->phone;
            }
            if ($request->filled('description_en')) {
                $branch->description_en = $request->description_en;
            }
            if ($request->filled('description_ar')) {
                $branch->description_ar = $request->description_ar;
            }
            if ($request->has('is_active')) {
                $branch->is_active = $request->boolean('is_active');
            }

            // Handle main branch logic
            if ($request->has('is_main') && $request->boolean('is_main')) {
                // If setting this branch as main, unset other main branches for this store
                Branch::where('store_id', $branch->store_id)
                    ->where('id', '!=', $branch->id)
                    ->update(['is_main' => false]);

                $branch->is_main = true;
            } elseif ($request->has('is_main') && !$request->boolean('is_main')) {
                // Only allow unsetting main if there's another main branch
                $otherMainBranches = Branch::where('store_id', $branch->store_id)
                    ->where('id', '!=', $branch->id)
                    ->where('is_main', true)
                    ->exists();

                if ($otherMainBranches) {
                    $branch->is_main = false;
                } else {
                    return $this->errorResponse('errors.store_must_have_main_branch', [], 400);
                }
            }

            $branch->save();
            $branch->load('store');

            DB::commit();

            return $this->successResponse($branch, 'success.branch_updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Delete a branch
     */
    public function destroy($id): JsonResponse
    {
        try {
            $branch = Branch::find($id);

            if (!$branch) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            DB::beginTransaction();

            // Check if this is the only branch for the store
            $branchCount = Branch::where('store_id', $branch->store_id)->count();
            if ($branchCount <= 1) {
                return $this->errorResponse('errors.cannot_delete_last_branch', [], 400);
            }

            // If deleting the main branch, assign another branch as main
            if ($branch->is_main) {
                $newMainBranch = Branch::where('store_id', $branch->store_id)
                    ->where('id', '!=', $branch->id)
                    ->first();

                if ($newMainBranch) {
                    $newMainBranch->update(['is_main' => true]);
                }
            }

            $branch->delete();

            DB::commit();

            return $this->successResponse(null, 'success.branch_deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Toggle branch active status
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $branch = Branch::find($id);

            if (!$branch) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $branch->is_active = !$branch->is_active;
            $branch->save();
            $branch->load('store');

            return $this->successResponse($branch, 'success.status_updated');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Set a branch as the main branch
     */
    public function setAsMain($id): JsonResponse
    {
        try {
            $branch = Branch::find($id);

            if (!$branch) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            DB::beginTransaction();

            // Unset other main branches for this store
            Branch::where('store_id', $branch->store_id)
                ->where('id', '!=', $branch->id)
                ->update(['is_main' => false]);

            // Set this branch as main
            $branch->is_main = true;
            $branch->save();
            $branch->load('store');

            DB::commit();

            return $this->successResponse($branch, 'success.main_branch_updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get branches for a specific store
     */
    public function getStoreBranches($storeId): JsonResponse
    {
        try {
            $store = Store::find($storeId);

            if (!$store) {
                return $this->errorResponse('errors.store_not_found', [], 404);
            }

            $branches = Branch::where('store_id', $storeId)
                ->orderBy('is_main', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse([
                'store' => $store,
                'branches' => $branches
            ], 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
