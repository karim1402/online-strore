<?php

namespace App\Http\Controllers\Api\V2\Admin;

use App\Http\Controllers\Controller;
use App\Models\ModuleAd;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModuleAdController extends Controller
{
    use ApiResponse;

    /**
     * List module ads with pagination and filters.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            $moduleId = $request->get('module_id');
            $status = $request->get('status');

            $query = ModuleAd::with(['module', 'product'])->orderBy('sort_order', 'asc');

            if ($moduleId) {
                $query->where('module_id', $moduleId);
            }

            if (!is_null($status)) {
                $query->where('status', filter_var($status, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE));
            }

            $ads = $query->paginate($perPage);

            return $this->successResponse($ads, 'success.module_ads_retrieved');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Create a new module ad.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'module_id' => 'required|exists:modules,id',
                'product_id' => 'required|exists:products,id',
                'image' => 'required|image|max:2048',
                'status' => 'boolean',
                'sort_order' => 'nullable|integer',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $data = $validator->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                $data['image'] = $request->file('image')->store('module_ads', 'r2');
            }

            $data['status'] = $data['status'] ?? true;
            $data['sort_order'] = $data['sort_order'] ?? 0;

            $ad = ModuleAd::create($data);

            // Log the creation activity
            $currentAdmin = auth('admins')->user();
            activity('module_ad_management')
                ->causedBy($currentAdmin)
                ->performedOn($ad)
                ->withProperties([
                    'action' => 'created',
                    'created_by' => $currentAdmin->name,
                    'module_id' => $ad->module_id,
                    'product_id' => $ad->product_id,
                ])
                ->log('Module ad created');

            return $this->successResponse($ad->load(['module', 'product']), 'success.module_ad_created', [], 201);
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Show a single module ad.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $ad = ModuleAd::with(['module', 'product'])->findOrFail($id);
            return $this->successResponse($ad, 'success.module_ad_retrieved');
        } catch (\Throwable $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        }
    }

    /**
     * Update a module ad.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $ad = ModuleAd::findOrFail($id);

            $validator = ValidationService::make($request->all(), [
                'module_id' => 'nullable|exists:modules,id',
                'product_id' => 'nullable|exists:products,id',
                'image' => 'nullable|image|max:2048',
                'status' => 'boolean',
                'sort_order' => 'nullable|integer',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date|after_or_equal:start_date',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $data = $validator->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                // Delete old image
                if ($ad->image) {
                    if (Storage::disk('public')->exists($ad->image)) {
                        Storage::disk('public')->delete($ad->image);
                    }
                    if (Storage::disk('r2')->exists($ad->image)) {
                        Storage::disk('r2')->delete($ad->image);
                    }
                }
                $data['image'] = $request->file('image')->store('module_ads', 'r2');
            }

            $ad->update($data);

            // Log the update activity
            $currentAdmin = auth('admins')->user();
            activity('module_ad_management')
                ->causedBy($currentAdmin)
                ->performedOn($ad)
                ->withProperties([
                    'action' => 'updated',
                    'updated_by' => $currentAdmin->name,
                    'module_id' => $ad->module_id,
                    'product_id' => $ad->product_id,
                ])
                ->log('Module ad updated');

            return $this->successResponse($ad->fresh()->load(['module', 'product']), 'success.module_ad_updated');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Delete a module ad.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $ad = ModuleAd::findOrFail($id);

            // Log the deletion activity before deleting
            $currentAdmin = auth('admins')->user();
            activity('module_ad_management')
                ->causedBy($currentAdmin)
                ->performedOn($ad)
                ->withProperties([
                    'action' => 'deleted',
                    'deleted_by' => $currentAdmin->name,
                    'module_id' => $ad->module_id,
                    'product_id' => $ad->product_id,
                ])
                ->log('Module ad deleted');

            // Delete image
            if ($ad->image) {
                if (Storage::disk('public')->exists($ad->image)) {
                    Storage::disk('public')->delete($ad->image);
                }
                if (Storage::disk('r2')->exists($ad->image)) {
                    Storage::disk('r2')->delete($ad->image);
                }
            }

            $ad->delete();
            return $this->successResponse(null, 'success.module_ad_deleted');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Toggle module ad status.
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $ad = ModuleAd::findOrFail($id);
            $oldStatus = $ad->status;
            $newStatus = !$ad->status;
            $ad->update(['status' => $newStatus]);

            // Log the status change activity
            $currentAdmin = auth('admins')->user();
            activity('module_ad_management')
                ->causedBy($currentAdmin)
                ->performedOn($ad)
                ->withProperties([
                    'action' => 'status_changed',
                    'changed_by' => $currentAdmin->name,
                    'old_status' => $oldStatus ? 'active' : 'inactive',
                    'new_status' => $newStatus ? 'active' : 'inactive',
                ])
                ->log('Module ad status toggled');

            return $this->successResponse($ad->fresh()->load(['module', 'product']), 'success.module_ad_status_updated');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
