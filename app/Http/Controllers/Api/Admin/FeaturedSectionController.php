<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeaturedSection;
use App\Models\Module;
use App\Models\Category;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeaturedSectionController extends Controller
{
    use ApiResponse;

    public function index(Request $request): JsonResponse
    {
        $query = FeaturedSection::query()->ordered();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $sections = $query->paginate($request->get('per_page', 15));

        return $this->successResponse($sections);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|in:module,category,subcategory',
            'item_id' => 'required|integer',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $data = $validator->validated();

        $exists = $this->validateItemExists($data['type'], $data['item_id']);
        if (!$exists) {
            return $this->errorResponse('errors.resource_not_found', [], 404);
        }

        $duplicate = FeaturedSection::where('type', $data['type'])
            ->where('item_id', $data['item_id'])
            ->exists();

        if ($duplicate) {
            return $this->errorResponse('errors.validation_failed', [], 422);
        }

        $section = FeaturedSection::create($data);

        return $this->successResponse($section, 'success.created', [], 201);
    }

    public function show($id): JsonResponse
    {
        $section = FeaturedSection::find($id);

        if (!$section) {
            return $this->notFoundResponse();
        }

        return $this->successResponse($section);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $section = FeaturedSection::find($id);

        if (!$section) {
            return $this->notFoundResponse();
        }

        $validator = Validator::make($request->all(), [
            'type' => 'nullable|in:module,category,subcategory',
            'item_id' => 'nullable|integer',
            'sort_order' => 'nullable|integer',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        $data = $validator->validated();

        $type = $data['type'] ?? $section->type;
        $itemId = $data['item_id'] ?? $section->item_id;

        if (isset($data['type']) || isset($data['item_id'])) {
            $exists = $this->validateItemExists($type, $itemId);
            if (!$exists) {
                return $this->errorResponse('errors.resource_not_found', [], 404);
            }
        }

        $section->update($data);

        return $this->successResponse($section->fresh(), 'success.updated');
    }

    public function destroy($id): JsonResponse
    {
        $section = FeaturedSection::find($id);

        if (!$section) {
            return $this->notFoundResponse();
        }

        $section->delete();

        return $this->successResponse(null, 'success.deleted');
    }

    public function toggleStatus($id): JsonResponse
    {
        $section = FeaturedSection::find($id);

        if (!$section) {
            return $this->notFoundResponse();
        }

        $section->update(['is_active' => !$section->is_active]);

        return $this->successResponse($section->fresh(), 'success.updated');
    }

    public function updateSortOrder(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'items' => 'required|array',
            'items.*.id' => 'required|exists:featured_sections,id',
            'items.*.sort_order' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorResponse($validator);
        }

        foreach ($request->items as $item) {
            FeaturedSection::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
        }

        return $this->successResponse(null, 'success.updated');
    }

    public function getAvailableItems(Request $request): JsonResponse
    {
        $type = $request->query('type');

        $items = match ($type) {
            'module' => Module::active()->ordered()->get(['id', 'name_en', 'name_ar', 'image']),
            'category' => Category::whereNull('parent_id')->active()->orderBy('sort_order')->get(['id', 'name_en', 'name_ar', 'image']),
            'subcategory' => Category::whereNotNull('parent_id')->active()->orderBy('sort_order')->get(['id', 'name_en', 'name_ar', 'image']),
            default => collect(),
        };

        return $this->successResponse($items);
    }

    private function validateItemExists(string $type, int $itemId): bool
    {
        return match ($type) {
            'module' => Module::where('id', $itemId)->exists(),
            'category' => Category::where('id', $itemId)->whereNull('parent_id')->exists(),
            'subcategory' => Category::where('id', $itemId)->whereNotNull('parent_id')->exists(),
            default => false,
        };
    }
}
