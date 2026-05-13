<?php

namespace App\Http\Controllers\Api\V2\Admin;

use App\Http\Controllers\Controller;
use App\Models\OptionValue;
use App\Models\OptionGroup;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OptionValueController extends Controller
{
    use ApiResponse;

    /**
     * Get all option values for a specific group
     */
    public function index(Request $request, $groupId): JsonResponse
    {
        try {
            $optionGroup = OptionGroup::find($groupId);

            if (!$optionGroup) {
                return $this->errorResponse('errors.option_group_not_found', [], 404);
            }

            $query = OptionValue::where('option_group_id', $groupId)->with('optionGroup');

            // Filter by active status
            if ($request->has('is_active')) {
                $query->where('is_active', $request->boolean('is_active'));
            }

            $optionValues = $query->orderBy('sort_order', 'asc')
                ->orderBy('created_at', 'asc')
                ->get();

            return $this->successResponse($optionValues, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get a single option value by ID
     */
    public function show($id): JsonResponse
    {
        try {
            $optionValue = OptionValue::with('optionGroup')->find($id);

            if (!$optionValue) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            return $this->successResponse($optionValue, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Create a new option value
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'option_group_id' => 'required|integer|exists:option_groups,id',
                'value_en' => 'required|string|max:255',
                'value_ar' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('option-values', 'public');
            }

            $optionValue = OptionValue::create([
                'option_group_id' => $request->option_group_id,
                'value_en' => $request->value_en,
                'value_ar' => $request->value_ar,
                'image' => $imagePath,
                'sort_order' => $request->get('sort_order', 0),
                'is_active' => $request->boolean('is_active', true),
            ]);

            $optionValue->load('optionGroup');

            DB::commit();

            return $this->successResponse($optionValue, 'success.option_value_created', [], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Update an option value
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $optionValue = OptionValue::find($id);

            if (!$optionValue) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'value_en' => 'nullable|string|max:255',
                'value_ar' => 'nullable|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'sort_order' => 'nullable|integer|min:0',
                'is_active' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            if ($request->filled('value_en')) {
                $optionValue->value_en = $request->value_en;
            }
            if ($request->filled('value_ar')) {
                $optionValue->value_ar = $request->value_ar;
            }
            
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($optionValue->image) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($optionValue->image);
                }
                $optionValue->image = $request->file('image')->store('option-values', 'public');
            }

            if ($request->has('sort_order')) {
                $optionValue->sort_order = $request->sort_order;
            }
            if ($request->has('is_active')) {
                $optionValue->is_active = $request->boolean('is_active');
            }

            $optionValue->save();
            $optionValue->load('optionGroup');

            DB::commit();

            return $this->successResponse($optionValue, 'success.option_value_updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Delete an option value
     */
    public function destroy($id): JsonResponse
    {
        try {
            $optionValue = OptionValue::find($id);

            if (!$optionValue) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            DB::beginTransaction();

            // Delete image if exists
            if ($optionValue->image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($optionValue->image);
            }

            $optionValue->delete();

            DB::commit();

            return $this->successResponse(null, 'success.option_value_deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Reorder option values
     */
    public function reorder(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'values' => 'required|array',
                'values.*.id' => 'required|integer|exists:option_values,id',
                'values.*.sort_order' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            foreach ($request->values as $valueData) {
                OptionValue::where('id', $valueData['id'])
                    ->update(['sort_order' => $valueData['sort_order']]);
            }

            DB::commit();

            return $this->successResponse(null, 'success.values_reordered');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
