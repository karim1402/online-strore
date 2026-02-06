<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Module;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    use ApiResponse;

    /**
     * Get all categories with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Category::with([
                'module:id,name_en,name_ar',
                'parent:id,name_en,name_ar',
                'children',
                'products' => function ($query) {
                    $query->orderBy('sort_order', 'asc');
                }
            ]);

            // Filter by parent (null for top-level)
            if ($request->has('parent_id')) {
                $query->where('parent_id', $request->parent_id);
            } elseif ($request->boolean('top_level_only')) {
                $query->whereNull('parent_id');
            }

            // Filter by module
            if ($request->filled('module_id')) {
                $query->where('module_id', $request->module_id);
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

            $categories = $query->orderBy('sort_order', 'asc')
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            return $this->successResponse($categories, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get a single category by ID
     */
    public function show($id): JsonResponse
    {
        try {
            $category = Category::with([
                'module:id,name_en,name_ar',
                'parent:id,name_en,name_ar',
                'children',
                'products' => function ($query) {
                    $query->orderBy('sort_order', 'asc');
                }
            ])->find($id);

            if (!$category) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            return $this->successResponse($category, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Create a new category
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'module_id' => 'required|integer|exists:modules,id',
                'parent_id' => 'nullable|integer|exists:categories,id',
                'name_en' => 'required|string|max:255',
                'name_ar' => 'required|string|max:255',
                'description_en' => 'nullable|string',
                'description_ar' => 'nullable|string',
                'image' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            // Check if module exists
            $module = Module::find($request->module_id);
            if (!$module) {
                return $this->errorResponse('errors.module_not_found', [], 404);
            }

            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('categories', 'public');
            }

            $category = Category::create([
                'module_id' => $request->module_id,
                'parent_id' => $request->parent_id,
                'name_en' => $request->name_en,
                'name_ar' => $request->name_ar,
                'description_en' => $request->description_en,
                'description_ar' => $request->description_ar,
                'image' => $imagePath,
                'is_active' => $request->boolean('is_active', true),
                'sort_order' => $request->get('sort_order', 0),
            ]);

            $category->load(['module:id,name_en,name_ar', 'parent', 'children', 'products']);

            DB::commit();

            return $this->successResponse($category, 'success.category_created', [], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded image if category creation failed
            if (isset($imagePath) && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Update a category
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                'parent_id' => 'nullable|integer|exists:categories,id',
                'name_en' => 'nullable|string|max:255',
                'name_ar' => 'nullable|string|max:255',
                'description_en' => 'nullable|string',
                'description_ar' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
                'is_active' => 'nullable|boolean',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            // Handle image upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Delete old image
                if ($category->image && Storage::disk('public')->exists($category->image)) {
                    Storage::disk('public')->delete($category->image);
                }
                
                // Upload new image
                $category->image = $request->file('image')->store('categories', 'public');
            }

            // Handle parent_id update
            if ($request->has('parent_id')) {
                // Prevent self-parenting
                if ($request->parent_id == $id) {
                    return $this->errorResponse('errors.invalid_parent', [], 422);
                }
                $category->parent_id = $request->parent_id;
            }

            // Update category fields
            if ($request->filled('name_en')) {
                $category->name_en = $request->name_en;
            }
            if ($request->filled('name_ar')) {
                $category->name_ar = $request->name_ar;
            }
            if ($request->has('description_en')) {
                $category->description_en = $request->description_en;
            }
            if ($request->has('description_ar')) {
                $category->description_ar = $request->description_ar;
            }
            if ($request->has('is_active')) {
                $category->is_active = $request->boolean('is_active');
            }
            if ($request->has('sort_order')) {
                $category->sort_order = $request->sort_order;
            }

            $category->save();
            $category->load(['module:id,name_en,name_ar', 'parent', 'children', 'products']);

            DB::commit();

            return $this->successResponse($category, 'success.category_updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Delete a category
     */
    public function destroy($id): JsonResponse
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            DB::beginTransaction();

            // Delete category image
            if ($category->image && Storage::disk('public')->exists($category->image)) {
                Storage::disk('public')->delete($category->image);
            }

            $category->delete();

            DB::commit();

            return $this->successResponse(null, 'success.category_deleted');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Toggle category active status
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $category = Category::find($id);

            if (!$category) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $category->is_active = !$category->is_active;
            $category->save();
            $category->load(['module:id,name_en,name_ar', 'parent', 'children', 'products']);

            return $this->successResponse($category, 'success.status_updated');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get categories for a specific module
     */
    public function getModuleCategories(Request $request, $moduleId): JsonResponse
    {
        try {
            $module = Module::find($moduleId);

            if (!$module) {
                return $this->errorResponse('errors.module_not_found', [], 404);
            }

            $query = Category::with([
                'parent:id,name_en,name_ar',
                'children',
                'products' => function ($query) {
                    $query->orderBy('sort_order', 'asc');
                }
            ])->where('module_id', $moduleId);

            // Filter by parent (null for top-level)
            if ($request->has('parent_id')) {
                $query->where('parent_id', $request->parent_id);
            } elseif ($request->boolean('top_level_only')) {
                $query->whereNull('parent_id');
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

            $categories = $query->orderBy('sort_order', 'asc')
                ->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 1500));

            return $this->successResponse([
                'module' => $module,
                'categories' => $categories
            ], 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Update category sort order
     */
    public function updateSortOrder(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'categories' => 'required|array',
                'categories.*.id' => 'required|integer|exists:categories,id',
                'categories.*.sort_order' => 'required|integer|min:0',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            foreach ($request->categories as $categoryData) {
                Category::where('id', $categoryData['id'])
                    ->update(['sort_order' => $categoryData['sort_order']]);
            }

            DB::commit();

            return $this->successResponse(null, 'success.sort_order_updated');
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
