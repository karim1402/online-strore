<?php

namespace App\Http\Controllers\Api\V2\User;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Module;
use App\Models\Store;
use App\Models\UserAddress;
use App\Services\LocalizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class StoreController extends Controller
{
    /**
     * Get stores by module ID, ordered by distance from user location.
     *
     * @param int $moduleId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStoresByModule($moduleId, Request $request)
    {
       //log the request and moduleId
       Log::info('getStoresByModule', ['moduleId' => $moduleId, 'request' => $request->all()]);
        // Validate module exists
        $module = Module::find($moduleId);
        if (!$module) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Module']),
            ], 404);
        }

        // Get user location
        $locationData = $this->getUserLocation($request);
        
        if (!$locationData['success']) {
            return response()->json([
                'success' => false,
                'message' => $locationData['message'],
                'errors' => $locationData['errors'] ?? null,
            ], $locationData['status']);
        }

        $latitude = $locationData['latitude'];
        $longitude = $locationData['longitude'];
        $locationSource = $locationData['source'];

        // Validate other request parameters
        $validator = Validator::make($request->all(), [
            'search' => 'nullable|string|max:255',
            'per_page' => 'nullable|integer|min:1|max:100',
            'radius' => 'nullable|numeric|min:0',
            'address_id' => 'nullable|integer|exists:user_addresses,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('validation.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        // Query parameters
        $search = $request->input('search');
        $perPage = $request->input('per_page', 15);
        $radius = $request->input('radius'); // Optional radius filter in km

        // Query stores by module with active branches
        $query = Store::whereHas('modules', function ($q) use ($moduleId) {
            $q->where('modules.id', $moduleId);
        })
        ->with(['modules' => function ($q) {
            $q->select('modules.id', 'name_en', 'name_ar', 'image');
        }])
        ->approved(); // Only approved stores

        // Apply search filter if provided
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name_en', 'like', "%{$search}%")
                  ->orWhere('name_ar', 'like', "%{$search}%");
            });
        }

        // Get stores
        $stores = $query->get();

        // Calculate distances and filter stores
        $storesWithDistance = [];

        foreach ($stores as $store) {
            // Get active branches with coordinates
            $branches = $store->activeBranches()
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->get();

            if ($branches->isEmpty()) {
                continue; // Skip stores with no valid branches
            }

            // Calculate distance for each branch and find the closest one
            $closestBranch = null;
            $minDistance = PHP_FLOAT_MAX;

            foreach ($branches as $branch) {
                $distance = $branch->getDistanceFrom($latitude, $longitude);
                
                if ($distance < $minDistance) {
                    $minDistance = $distance;
                    $closestBranch = $branch;
                }
            }

            // Apply radius filter if provided
            if ($radius !== null && $minDistance > $radius) {
                continue; // Skip stores outside the radius
            }

            // Add distance to branch data
            $closestBranchData = $closestBranch->toArray();
            $closestBranchData['distance_km'] = round($minDistance, 2);

            // Add store with closest branch and distance
            $storeData = $store->toArray();
            $storeData['closest_branch'] = $closestBranchData;
            $storeData['distance_km'] = round($minDistance, 2);

            $storesWithDistance[] = $storeData;
        }

        // Sort stores by distance (ascending)
        usort($storesWithDistance, function ($a, $b) {
            return $a['distance_km'] <=> $b['distance_km'];
        });

        // Manual pagination
        $total = count($storesWithDistance);
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $paginatedStores = array_slice($storesWithDistance, $offset, $perPage);

        $lastPage = ceil($total / $perPage);

        // Localize store and branch data based on app locale
        $localizedStores = LocalizationService::localizeCollection($paginatedStores, ['name', 'description', 'address']);

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => [
                'stores' => $localizedStores,
                'pagination' => [
                    'current_page' => (int) $currentPage,
                    'per_page' => (int) $perPage,
                    'total' => $total,
                    'last_page' => $lastPage,
                    'from' => $total > 0 ? $offset + 1 : 0,
                    'to' => min($offset + $perPage, $total),
                ],
                'user_location' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                    'source' => $locationSource,
                ],
            ],
        ], 200);
    }

    /**
     * Get user location from request parameters, specific address, or default address.
     *
     * @param Request $request
     * @return array
     */
    private function getUserLocation(Request $request)
    {
        // Check if latitude and longitude are provided
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');

        if ($latitude !== null && $longitude !== null) {
            // Validate provided coordinates
            $validator = Validator::make($request->all(), [
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ]);

            if ($validator->fails()) {
                return [
                    'success' => false,
                    'message' => LocalizationService::getMessage('validation.validation_failed'),
                    'errors' => $validator->errors(),
                    'status' => 422,
                ];
            }

            return [
                'success' => true,
                'latitude' => (float) $latitude,
                'longitude' => (float) $longitude,
                'source' => 'provided',
            ];
        }

        // Check if user is authenticated
        $user = auth('api')->user();

        if (!$user) {
            return [
                'success' => false,
                'message' => LocalizationService::getMessage('validation.location_required'),
                'status' => 400,
            ];
        }

        // Check if address_id is provided
        $addressId = $request->input('address_id');

        if (!$addressId) {
            return [
                'success' => false,
                'message' => LocalizationService::getMessage('validation.address_selection_required'),
                'status' => 400,
            ];
        }

        // Validate and get specific address
        $address = UserAddress::where('id', $addressId)
            ->where('user_id', $user->id)
            ->first();

        if (!$address) {
            return [
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Address']),
                'status' => 404,
            ];
        }

        if (!$address->latitude || !$address->longitude) {
            return [
                'success' => false,
                'message' => LocalizationService::getMessage('validation.address_coordinates_required'),
                'status' => 400,
            ];
        }

        return [
            'success' => true,
            'latitude' => (float) $address->latitude,
            'longitude' => (float) $address->longitude,
            'source' => 'selected_address',
        ];
    }

    /**
     * Get all categories with their products for a specific store.
     *
     * @param int $storeId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStoreCategoriesWithProducts($storeId, Request $request)
    {
        // Validate store exists and is approved
        $store = Store::find($storeId);
        
        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.store_not_found'),
            ], 404);
        }

        if (!$store->isApproved()) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.store_not_found'),
            ], 404);
        }

        // Get module IDs associated with this store
        $moduleIds = $store->modules()->pluck('modules.id');

        // Query only active categories from the store's modules with active products
        $categories = Category::whereIn('module_id', $moduleIds)
            ->active()
            ->with(['children' => function ($query) {
                $query->active()->with(['products' => function ($q) {
                    $q->active()
                        ->whereColumn('subcategory_id', 'categories.id') // Products assigned to this subcategory
                        ->with(['primaryImage', 'images'])
                        ->orderBy('sort_order', 'asc');
                }]);
            }, 'products' => function ($query) {
                $query->active()
                    ->whereNull('subcategory_id') // Only products directly in the main category
                    ->with(['primaryImage', 'images'])
                    ->orderBy('sort_order', 'asc')
                    ->orderBy('name_en', 'asc');
            }])
            ->whereNull('parent_id') // Get only top-level categories
            ->orderBy('sort_order', 'asc')
            ->orderBy('name_en', 'asc')
            ->get();

        // Recursive function to transform category data
        $transformCategory = function ($category) use (&$transformCategory) {
            $productsData = $category->products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name_en' => $product->name_en,
                    'name_ar' => $product->name_ar,
                    'description_en' => $product->description_en,
                    'description_ar' => $product->description_ar,
                    'base_price' => $product->base_price,
                    'is_active' => $product->is_active,
                    'view_count' => $product->view_count,
                    'sales_count' => $product->sales_count,
                    'sort_order' => $product->sort_order,
                    'primary_image' => $product->primaryImage ? [
                        'id' => $product->primaryImage->id,
                        'image_url' => $product->primaryImage->image_url,
                    ] : null,
                    'images' => $product->images->map(function ($image) {
                        return [
                            'id' => $image->id,
                            'image_url' => $image->image_url,
                            'is_primary' => $image->is_primary,
                            'sort_order' => $image->sort_order,
                        ];
                    }),
                ];
            });

            return [
                'id' => $category->id,
                'name_en' => $category->name_en,
                'name_ar' => $category->name_ar,
                'description_en' => $category->description_en,
                'description_ar' => $category->description_ar,
                'image_url' => $category->image_url,
                'is_active' => $category->is_active,
                'sort_order' => $category->sort_order,
                'products_count' => $productsData->count(),
                'products' => $productsData,
                'subcategories' => $category->children->map($transformCategory),
            ];
        };

        $categoriesData = $categories->map($transformCategory);

        // Localize the data
        $localizedCategories = LocalizationService::localizeCollection($categoriesData->toArray(), ['name', 'description']);

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => [
                'store' => [
                    'id' => $store->id,
                    'name_en' => $store->name_en,
                    'name_ar' => $store->name_ar,
                    'description_en' => $store->description_en,
                    'description_ar' => $store->description_ar,
                    'logo_url' => $store->logo_url,
                    'status' => $store->status,
                ],
                'categories' => $localizedCategories,
                'total_categories' => $categoriesData->count(),
            ],
        ], 200);
    }
}
