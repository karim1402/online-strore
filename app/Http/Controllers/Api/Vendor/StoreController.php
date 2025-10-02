<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\MainCategory;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    use ApiResponse;

    /**
     * Get vendor's store information
     */
    public function show(): JsonResponse
    {
        $vendor = Auth::guard('vendors')->user();
        $store = $vendor->store;

        if (!$store) {
            return $this->errorResponse('errors.store_not_found', [], 404);
        }

        // Load relationships
        $store->load(['mainCategories']);

        return $this->successResponse([
            'store' => [
                'id' => $store->id,
                'name_en' => $store->name_en,
                'name_ar' => $store->name_ar,
                'description_en' => $store->description_en,
                'description_ar' => $store->description_ar,
                'address' => $store->address,
                'latitude' => $store->latitude,
                'longitude' => $store->longitude,
                'logo' => $store->logo ? asset('storage/' . $store->logo) : null,
                'document' => $store->document ? asset('storage/' . $store->document) : null,
                'status' => $store->status,
                'main_categories' => $store->mainCategories,
                'created_at' => $store->created_at,
                'updated_at' => $store->updated_at,
            ]
        ], 'success.store_fetched');
    }

    /**
     * Update vendor's store information
     */
    public function update(Request $request): JsonResponse
    {
        $vendor = Auth::guard('vendors')->user();
        $store = $vendor->store;

        if (!$store) {
            return $this->errorResponse('errors.store_not_found', [], 404);
        }

        $validator = ValidationService::make($request->all(), [
            'main_category_ids' => 'sometimes|array|min:1',
            'main_category_ids.*' => 'sometimes|integer|exists:main_categories,id',
            'name_en' => 'sometimes|string|max:255',
            'name_ar' => 'sometimes|string|max:255',
            'description_en' => 'sometimes|string',
            'description_ar' => 'sometimes|string',
            'store_address' => 'sometimes|string',
            'latitude' => 'sometimes|numeric|between:-90,90',
            'longitude' => 'sometimes|numeric|between:-180,180',
            'logo' => 'sometimes|image|mimes:jpeg,jpg,png,webp|max:2048',
            'document' => 'sometimes|file|mimes:pdf,jpeg,jpg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return $this->validationErrorWithFirstMessage($validator);
        }

        // Verify all main categories exist and are active if provided
        if ($request->has('main_category_ids')) {
            $activeCategories = MainCategory::whereIn('id', $request->main_category_ids)
                ->where('status', true)
                ->pluck('id')
                ->toArray();
                
            if (count($activeCategories) !== count($request->main_category_ids)) {
                return $this->errorResponse('errors.category_not_found', [], 404);
            }
        }

        DB::beginTransaction();
        try {
            $oldLogoPath = $store->logo;
            $oldDocumentPath = $store->document;

            // Handle logo upload
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('stores/logos', 'public');
                $store->logo = $logoPath;
                
                // Delete old logo
                if ($oldLogoPath && Storage::disk('public')->exists($oldLogoPath)) {
                    Storage::disk('public')->delete($oldLogoPath);
                }
            }

            // Handle document upload
            if ($request->hasFile('document')) {
                $documentPath = $request->file('document')->store('stores/documents', 'public');
                $store->document = $documentPath;
                
                // Delete old document
                if ($oldDocumentPath && Storage::disk('public')->exists($oldDocumentPath)) {
                    Storage::disk('public')->delete($oldDocumentPath);
                }
            }

            // Update store fields
            if ($request->has('name_en')) {
                $store->name_en = $request->name_en;
            }
            if ($request->has('name_ar')) {
                $store->name_ar = $request->name_ar;
            }
            if ($request->has('description_en')) {
                $store->description_en = $request->description_en;
            }
            if ($request->has('description_ar')) {
                $store->description_ar = $request->description_ar;
            }
            if ($request->has('store_address')) {
                $store->address = $request->store_address;
            }
            if ($request->has('latitude')) {
                $store->latitude = $request->latitude;
            }
            if ($request->has('longitude')) {
                $store->longitude = $request->longitude;
            }

            $store->save();

            // Update main categories if provided
            if ($request->has('main_category_ids')) {
                $store->mainCategories()->sync($request->main_category_ids);
            }

            DB::commit();

            // Load relationships for response
            $store->load(['mainCategories']);

            return $this->successResponse([
                'store' => [
                    'id' => $store->id,
                    'name_en' => $store->name_en,
                    'name_ar' => $store->name_ar,
                    'description_en' => $store->description_en,
                    'description_ar' => $store->description_ar,
                    'address' => $store->address,
                    'latitude' => $store->latitude,
                    'longitude' => $store->longitude,
                    'logo' => $store->logo ? asset('storage/' . $store->logo) : null,
                    'document' => $store->document ? asset('storage/' . $store->document) : null,
                    'status' => $store->status,
                    'main_categories' => $store->mainCategories,
                    'created_at' => $store->created_at,
                    'updated_at' => $store->updated_at,
                ]
            ], 'success.store_updated');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded files if update failed
            if (isset($logoPath) && $logoPath !== $oldLogoPath && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            if (isset($documentPath) && $documentPath !== $oldDocumentPath && Storage::disk('public')->exists($documentPath)) {
                Storage::disk('public')->delete($documentPath);
            }
            
            return $this->errorResponse('errors.store_update_failed', [], 500);
        }
    }
}
