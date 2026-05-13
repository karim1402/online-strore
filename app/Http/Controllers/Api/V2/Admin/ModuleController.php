<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModuleController extends Controller
{
    use ApiResponse;

    /**
     * Display a listing of the modules.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = $request->get('per_page', 10);
            $search = $request->get('search');
            $status = $request->get('status');

            $query = Module::query();

            // Search functionality
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                      ->orWhere('name_ar', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($status !== null) {
                $query->where('status', $status);
            }

            $modules = $query->ordered()->paginate($perPage);

            $modules->through(fn($m) => $this->transformModule($m));

            return $this->successResponse($modules, 'success.modules_retrieved');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Store a newly created module.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'name_en' => 'required|string|max:255|unique:modules,name_en',
                'name_ar' => 'required|string|max:255|unique:modules,name_ar',
                'description_en' => 'nullable|string',
                'description_ar' => 'nullable|string',
                'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'image_ar' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $data = $validator->validated();

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('modules/v2', 'public');
                $data['image_v2'] = $imagePath;
                unset($data['image']);
            }

            // Handle Arabic image upload
            if ($request->hasFile('image_ar')) {
                $imageArPath = $request->file('image_ar')->store('modules/v2', 'public');
                $data['image_ar_v2'] = $imageArPath;
                unset($data['image_ar']);
            }

            $module = Module::create($data);

            return $this->successResponse($this->transformModule($module), 'success.module_created', [], 201);

        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Display the specified module.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        try {
            $module = Module::findOrFail($id);

            return $this->successResponse($this->transformModule($module), 'success.module_retrieved');

        } catch (\Exception $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        }
    }

    /**
     * Update the specified module.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $module = Module::findOrFail($id);

            $validator = ValidationService::make($request->all(), [
                'name_en' => 'required|string|max:255|unique:modules,name_en,' . $id,
                'name_ar' => 'required|string|max:255|unique:modules,name_ar,' . $id,
                'description_en' => 'nullable|string',
                'description_ar' => 'nullable|string',
                'image' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'image_ar' => 'nullable|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'status' => 'boolean',
                'sort_order' => 'integer|min:0'
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $data = $validator->validated();

            // Handle image upload
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Delete old V2 image if exists
                if ($module->image_v2 && Storage::disk('public')->exists($module->image_v2)) {
                    Storage::disk('public')->delete($module->image_v2);
                }

                $imagePath = $request->file('image')->store('modules/v2', 'public');
                $data['image_v2'] = $imagePath;
                unset($data['image']);
            }

            // Handle Arabic image upload
            if ($request->hasFile('image_ar') && $request->file('image_ar')->isValid()) {
                // Delete old V2 Arabic image if exists
                if ($module->image_ar_v2 && Storage::disk('public')->exists($module->image_ar_v2)) {
                    Storage::disk('public')->delete($module->image_ar_v2);
                }

                $imageArPath = $request->file('image_ar')->store('modules/v2', 'public');
                $data['image_ar_v2'] = $imageArPath;
                unset($data['image_ar']);
            }

            $module->update($data);

            return $this->successResponse($this->transformModule($module->fresh()), 'success.module_updated');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Remove the specified module.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $module = Module::findOrFail($id);

            // Delete associated V2 image if exists
            if ($module->image_v2 && Storage::disk('public')->exists($module->image_v2)) {
                Storage::disk('public')->delete($module->image_v2);
            }

            // Delete associated V2 Arabic image if exists
            if ($module->image_ar_v2 && Storage::disk('public')->exists($module->image_ar_v2)) {
                Storage::disk('public')->delete($module->image_ar_v2);
            }

            $module->delete();

            return $this->successResponse(null, 'success.module_deleted');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Toggle the status of the specified module.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $module = Module::findOrFail($id);
            $module->update(['status' => !$module->status]);

            return $this->successResponse($this->transformModule($module->fresh()), 'success.module_status_updated');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get all active modules for dropdown/select options.
     *
     * @return JsonResponse
     */
    public function getActiveModules(): JsonResponse
    {
        try {
            $modules = Module::active()->ordered()->get(['id', 'name_en', 'name_ar']);

            return $this->successResponse($modules, 'success.active_modules_retrieved');

        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    private function transformModule(Module $module): array
    {
        $data = $module->toArray();
        $data['image_url'] = $module->image_v2_url ?? $module->image_url;
        $data['image_ar_url'] = $module->image_ar_v2_url ?? $module->image_ar_url;
        return $data;
    }
}
