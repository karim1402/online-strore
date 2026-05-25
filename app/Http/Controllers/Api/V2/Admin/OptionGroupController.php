<?php

namespace App\Http\Controllers\Api\V2\Admin;

use App\Http\Controllers\Controller;
use App\Models\OptionGroup;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OptionGroupController extends Controller
{
    use ApiResponse;

    /**
     * Get all option groups
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = OptionGroup::with('values');
            $query->where('makook_sandwitch', 0);

            // Filter by type
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            // Filter by active status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            // Search by name
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%");
                });
            }

            $optionGroups = $query->orderBy('created_at', 'desc')->paginate(15);

            return $this->successResponse($optionGroups, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    //makook makook_sandwitch list
    public function makookSandwitchList(Request $request): JsonResponse
    {
        try {
            $query = OptionGroup::with('values');
            $query->where('makook_sandwitch', 1);

            // Filter by type
            if ($request->filled('type')) {
                $query->where('type', $request->type);
            }

            // Filter by active status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            // Search by name
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%");
                });
            }

            $optionGroups = $query->orderBy('created_at', 'desc')->paginate(15);

            return $this->successResponse($optionGroups, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get a single option group by ID
     */
    public function show($id): JsonResponse
    {
        try {
            $optionGroup = OptionGroup::with('values')->find($id);

            if (!$optionGroup) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            return $this->successResponse($optionGroup, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Create a new option group
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'name_en' => 'required|string|max:255',
                'name_ar' => 'required|string|max:255',
                'type' => 'required|string|max:50',
                'is_active' => 'nullable|boolean',
                'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
                'makook_sandwitch' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('option_groups', 'public');
            }

            $optionGroup = OptionGroup::create([
                'name_en' => $request->name_en,
                'name_ar' => $request->name_ar,
                'type' => $request->type,
                'is_active' => $request->boolean('is_active', true),
                'image' => $imagePath,
                'makook_sandwitch' => $request->boolean('makook_sandwitch', false),
            ]);

            $optionGroup->load('values');

            DB::commit();

            return $this->successResponse($optionGroup, 'success.option_group_created', [], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            // Clean up uploaded image if creation failed
            if (isset($imagePath) && $imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }

            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Update an option group
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $optionGroup = OptionGroup::find($id);

            if (!$optionGroup) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'name_en' => 'nullable|string|max:255',
                'name_ar' => 'nullable|string|max:255',
                'type' => 'nullable|string|max:50',
                'is_active' => 'nullable|boolean',
                'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
                'makook_sandwitch' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            if ($request->filled('name_en')) {
                $optionGroup->name_en = $request->name_en;
            }
            if ($request->filled('name_ar')) {
                $optionGroup->name_ar = $request->name_ar;
            }
            if ($request->filled('type')) {
                $optionGroup->type = $request->type;
            }
            if ($request->has('is_active')) {
                $optionGroup->is_active = $request->boolean('is_active');
            }
            if ($request->has('makook_sandwitch')) {
                $optionGroup->makook_sandwitch = $request->boolean('makook_sandwitch');
            }

            // Handle image upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Delete old image
                if ($optionGroup->image && Storage::disk('public')->exists($optionGroup->image)) {
                    Storage::disk('public')->delete($optionGroup->image);
                }
                
                // Upload new image
                $optionGroup->image = $request->file('image')->store('option_groups', 'public');
            }

            $optionGroup->save();
            $optionGroup->load('values');

            DB::commit();

            return $this->successResponse($optionGroup, 'success.option_group_updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Delete an option group
     */
    public function destroy($id): JsonResponse
    {
        try {
            $optionGroup = OptionGroup::find($id);

            if (!$optionGroup) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            DB::beginTransaction();

            // Delete image
            if ($optionGroup->image && Storage::disk('public')->exists($optionGroup->image)) {
                Storage::disk('public')->delete($optionGroup->image);
            }

            $optionGroup->delete();

            DB::commit();

            return $this->successResponse(null, 'success.option_group_deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Toggle option group active status
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $optionGroup = OptionGroup::find($id);

            if (!$optionGroup) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $optionGroup->is_active = !$optionGroup->is_active;
            $optionGroup->save();
            $optionGroup->load('values');

            return $this->successResponse($optionGroup, 'success.status_updated');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get distinct option types
     */
    public function getTypes(): JsonResponse
    {
        try {
            $types = OptionGroup::select('type')
                ->distinct()
                ->pluck('type');

            return $this->successResponse($types, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
