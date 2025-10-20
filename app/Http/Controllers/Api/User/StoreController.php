<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\MainCategory;
use App\Models\Store;
use App\Models\UserAddress;
use App\Services\LocalizationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    /**
     * Get stores by main category ID, ordered by distance from user location.
     *
     * @param int $mainCategoryId
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStoresByMainCategory($mainCategoryId, Request $request)
    {
        // Validate main category exists
        $mainCategory = MainCategory::find($mainCategoryId);
        if (!$mainCategory) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.not_found', ['resource' => 'Main Category']),
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

        // Query stores by main category with active branches
        $query = Store::whereHas('mainCategories', function ($q) use ($mainCategoryId) {
            $q->where('main_categories.id', $mainCategoryId);
        })
        ->with(['mainCategories' => function ($q) {
            $q->select('main_categories.id', 'name_en', 'name_ar', 'image');
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

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('success.data_retrieved'),
            'data' => [
                'stores' => $paginatedStores,
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
     * Get user location from request parameters or default address.
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

        // Get user's default address
        $defaultAddress = UserAddress::where('user_id', $user->id)
            ->where('is_default', true)
            ->first();

        if (!$defaultAddress) {
            return [
                'success' => false,
                'message' => LocalizationService::getMessage('validation.default_address_required'),
                'status' => 400,
            ];
        }

        if (!$defaultAddress->latitude || !$defaultAddress->longitude) {
            return [
                'success' => false,
                'message' => LocalizationService::getMessage('validation.address_coordinates_required'),
                'status' => 400,
            ];
        }

        return [
            'success' => true,
            'latitude' => (float) $defaultAddress->latitude,
            'longitude' => (float) $defaultAddress->longitude,
            'source' => 'default_address',
        ];
    }
}
