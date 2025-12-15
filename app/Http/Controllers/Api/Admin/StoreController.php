<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Vendor;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StoreController extends Controller
{
    use ApiResponse;
    /**
     * Get all stores with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Store::with(['vendors', 'mainCategories', 'branches']);

            // Search functionality
            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name_en', 'like', "%{$search}%")
                      ->orWhere('name_ar', 'like', "%{$search}%")
                      ->orWhereHas('vendors', function ($vq) use ($search) {
                          $vq->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                      })
                      ->orWhereHas('branches', function ($bq) use ($search) {
                          $bq->where('name_en', 'like', "%{$search}%")
                            ->orWhere('name_ar', 'like', "%{$search}%")
                            ->orWhere('address', 'like', "%{$search}%");
                      });
                });
            }

            // Filter by status
            if ($request->has('status')) {
                $validStatuses = ['pending', 'approved', 'rejected', 'suspended'];
                if (in_array($request->status, $validStatuses)) {
                    $query->where('status', $request->status);
                }
            }

            // Filter by date range
            if ($request->has('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Filter by specific date
            if ($request->has('date')) {
                $query->whereDate('created_at', $request->date);
            }

            // Filter by main category
            if ($request->has('category_id')) {
                $query->whereHas('mainCategories', function ($q) use ($request) {
                    $q->where('main_categories.id', $request->category_id);
                });
            }

            // Sort by created date (newest first by default)
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            
            $allowedSortFields = ['created_at', 'name_en', 'name_ar', 'status', 'approved_at'];
            if (in_array($sortBy, $allowedSortFields)) {
                $query->orderBy($sortBy, $sortOrder);
            } else {
                $query->orderBy('created_at', 'desc');
            }

            // Pagination - configurable from frontend (default 15)
            $perPage = $request->get('per_page', 10);
            $perPage = min(max((int)$perPage, 1), 100); // Min 1, Max 100

            $stores = $query->paginate($perPage);

            return $this->successResponse($stores, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get all stores without pagination or filters
     */
    public function getAll(): JsonResponse
    {
        try {
            $stores = Store::with(['vendors', 'mainCategories', 'branches'])
                ->orderBy('created_at', 'desc')
                ->get();
            return $this->successResponse($stores, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Create a new store with optional vendor
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                // Store fields
                'main_category_ids' => 'required|array|min:1',
                'main_category_ids.*' => 'required|integer|exists:main_categories,id',
                'name_en' => 'required|string|max:255',
                'name_ar' => 'required|string|max:255',
                'description_en' => 'required|string',
                'description_ar' => 'required|string',
                'logo' => 'required|image|mimes:jpeg,jpg,png,webp|max:2048',
                'document' => 'required|file|mimes:pdf,jpeg,jpg,png|max:5120',
                'status' => 'nullable|in:pending,approved,rejected,suspended',
                
                // Branches array (required for store creation)
                'branches' => 'required|array|min:1',
                'branches.*.name_en' => 'required|string|max:255',
                'branches.*.name_ar' => 'required|string|max:255',
                'branches.*.address' => 'required|string',
                'branches.*.latitude' => 'required|numeric|between:-90,90',
                'branches.*.longitude' => 'required|numeric|between:-180,180',
                'branches.*.phone' => 'nullable|string|max:20',
                'branches.*.description_en' => 'nullable|string',
                'branches.*.description_ar' => 'nullable|string',
                'branches.*.is_main' => 'nullable|boolean',
                
                // Optional vendor fields
                'vendor_name' => 'nullable|string|between:2,100',
                'vendor_email' => 'nullable|email|max:100|unique:vendors,email',
                'vendor_password' => 'nullable|string|min:6',
                'vendor_phone' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            // Handle logo upload
            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('stores/logos', 'public');
            }

            // Handle document upload
            $documentPath = null;
            if ($request->hasFile('document')) {
                $documentPath = $request->file('document')->store('stores/documents', 'public');
            }

            // Get admin
            $admin = auth('admins')->user();

            // Create store
            $storeData = [
                'name_en' => $request->name_en,
                'name_ar' => $request->name_ar,
                'description_en' => $request->description_en,
                'description_ar' => $request->description_ar,
                'logo' => $logoPath,
                'document' => $documentPath,
                'status' => $request->get('status', 'pending'),
            ];

            // If admin creates as approved, set approval info
            if ($storeData['status'] === 'approved') {
                $storeData['approved_at'] = now();
                $storeData['approved_by'] = $admin->id;
            }

            $store = Store::create($storeData);

            // Attach main categories
            $store->mainCategories()->attach($request->main_category_ids);

            // Create branches for the store
            $hasMainBranch = false;
            $branches = [];
            
            foreach ($request->branches as $index => $branchData) {
                $isMain = isset($branchData['is_main']) && $branchData['is_main'];
                
                // If this is marked as main, unset any previous main branch
                if ($isMain) {
                    $hasMainBranch = true;
                }
                
                $branch = $store->branches()->create([
                    'name_en' => $branchData['name_en'],
                    'name_ar' => $branchData['name_ar'],
                    'address' => $branchData['address'],
                    'latitude' => $branchData['latitude'],
                    'longitude' => $branchData['longitude'],
                    'phone' => $branchData['phone'] ?? null,
                    'description_en' => $branchData['description_en'] ?? null,
                    'description_ar' => $branchData['description_ar'] ?? null,
                    'is_main' => $isMain,
                    'is_active' => true,
                ]);
                
                $branches[] = $branch;
            }
            
            // If no branch was marked as main, make the first one main
            if (!$hasMainBranch && count($branches) > 0) {
                $branches[0]->update(['is_main' => true]);
            }

            // Create vendor if vendor details provided
            $vendor = null;
            if ($request->filled('vendor_name') && $request->filled('vendor_email') && $request->filled('vendor_password')) {
                $vendor = Vendor::create([
                    'name' => $request->vendor_name,
                    'email' => $request->vendor_email,
                    'password' => Hash::make($request->vendor_password),
                    'phone' => $request->vendor_phone,
                    'store_id' => $store->id,
                    'status' => true,
                ]);
            }

            // Reload relationships
            $store->load(['mainCategories', 'vendors', 'branches']);

            DB::commit();

            $responseData = [
                'store' => $store,
            ];

            // if ($vendor) {
            //     $responseData['vendor'] = [
            //         'id' => $vendor->id,
            //         'name' => $vendor->name,
            //         'email' => $vendor->email,
            //         'phone' => $vendor->phone,
            //         'status' => $vendor->status,
            //     ];
            // }

            return $this->successResponse($responseData, 'success.store_created', [], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded files if store creation failed
            if (isset($logoPath) && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            if (isset($documentPath) && Storage::disk('public')->exists($documentPath)) {
                Storage::disk('public')->delete($documentPath);
            }
            
            return $this->errorResponse('errors.store_creation_failed', [], 500);
        }
    }

    /**
     * Get pending stores awaiting approval
     */
    public function getPendingStores(): JsonResponse
    {
        try {
            $stores = Store::with(['vendors', 'mainCategories', 'branches'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->get();

            return $this->successResponse([
                'count' => $stores->count(),
                'stores' => $stores
            ], 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get a single store by ID
     */
    public function show($id): JsonResponse
    {
        try {
            $store = Store::with(['vendors', 'mainCategories', 'branches','categories'])->find($id);

            if (!$store) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            return $this->successResponse($store, 'success.data_retrieved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Update an existing store
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $store = Store::find($id);

            if (!$store) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $validator = ValidationService::make($request->all(), [
                // Store fields
                'main_category_ids' => 'nullable|array|min:1',
                'main_category_ids.*' => 'nullable|integer|exists:main_categories,id',
                'name_en' => 'nullable|string|max:255',
                'name_ar' => 'nullable|string|max:255',
                'description_en' => 'nullable|string',
                'description_ar' => 'nullable|string',
                'logo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
                'document' => 'nullable|file|mimes:pdf,jpeg,jpg,png|max:5120',
                'status' => 'nullable|in:pending,approved,rejected,suspended',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            DB::beginTransaction();

            $oldLogoPath = $store->logo;
            $oldDocumentPath = $store->document;

            // Handle logo upload
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                $logoPath = $request->file('logo')->store('stores/logos', 'public');
                $store->logo = $logoPath;
                
                // Delete old logo
                if ($oldLogoPath && Storage::disk('public')->exists($oldLogoPath)) {
                    Storage::disk('public')->delete($oldLogoPath);
                }
            }

            // Handle document upload
            if ($request->hasFile('document') && $request->file('document')->isValid()) {
                $documentPath = $request->file('document')->store('stores/documents', 'public');
                $store->document = $documentPath;
                
                // Delete old document
                if ($oldDocumentPath && Storage::disk('public')->exists($oldDocumentPath)) {
                    Storage::disk('public')->delete($oldDocumentPath);
                }
            }

            // Update store fields
            if ($request->filled('name_en')) {
                $store->name_en = $request->name_en;
            }
            if ($request->filled('name_ar')) {
                $store->name_ar = $request->name_ar;
            }
            if ($request->filled('description_en')) {
                $store->description_en = $request->description_en;
            }
            if ($request->filled('description_ar')) {
                $store->description_ar = $request->description_ar;
            }

            // Handle status change
            if ($request->filled('status')) {
                $admin = auth('admins')->user();
                $newStatus = $request->status;
                
                // If status is being changed to approved
                if ($newStatus === 'approved' && $store->status !== 'approved') {
                    $store->status = 'approved';
                    $store->approved_at = now();
                    $store->approved_by = $admin->id;
                    $store->rejection_note = null;
                } 
                // If status is being changed from approved
                elseif ($store->status === 'approved' && $newStatus !== 'approved') {
                    $store->status = $newStatus;
                    if ($newStatus !== 'suspended') {
                        $store->approved_at = null;
                        $store->approved_by = null;
                    }
                } 
                // Any other status change
                else {
                    $store->status = $newStatus;
                }
            }

            $store->save();

            // Update main categories if provided
            if ($request->has('main_category_ids')) {
                $store->mainCategories()->sync($request->main_category_ids);
            }

            // Reload relationships
            $store->load(['mainCategories', 'vendors', 'branches']);

            DB::commit();

            return $this->successResponse($store, 'success.store_updated');

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Clean up uploaded files if update failed
            if (isset($logoPath) && Storage::disk('public')->exists($logoPath)) {
                Storage::disk('public')->delete($logoPath);
            }
            if (isset($documentPath) && Storage::disk('public')->exists($documentPath)) {
                Storage::disk('public')->delete($documentPath);
            }
            
            return $this->errorResponse('errors.store_update_failed', [], 500);
        }
    }

    /**
     * Approve a store
     */
    public function approve($id): JsonResponse
    {
        try {
            $store = Store::find($id);

            if (!$store) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            // Check if already approved
            if ($store->status === 'approved') {
                return $this->errorResponse('errors.store_already_approved', [], 400);
            }

            // Approve the store
            $admin = auth('admins')->user();
            
            $store->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => $admin->id,
                'rejection_note' => null
            ]);

            // Log the approval activity
            activity('store')
                ->causedBy($admin)
                ->performedOn($store)
                ->withProperties([
                    'action' => 'approved',
                    'admin_name' => $admin->name,
                    'store_name' => $store->name_en,
                ])
                ->log('Store approved by admin');

            // Reload relationships
            $store->load(['vendors', 'mainCategories', 'branches']);

            return $this->successResponse($store, 'success.store_approved');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Reject a store with a note
     */
    public function reject(Request $request, $id): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'note' => 'required|string|min:10|max:1000',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $store = Store::find($id);

            if (!$store) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            // Reject the store
            $admin = auth('admins')->user();
            
            $store->update([
                'status' => 'rejected',
                'rejection_note' => $request->note,
                'approved_at' => null,
                'approved_by' => null
            ]);

            // Log the rejection activity
            activity('store')
                ->causedBy($admin)
                ->performedOn($store)
                ->withProperties([
                    'action' => 'rejected',
                    'admin_name' => $admin->name,
                    'store_name' => $store->name_en,
                    'rejection_note' => $request->note,
                ])
                ->log('Store rejected by admin');

            // Reload relationships
            $store->load(['vendors', 'mainCategories', 'branches']);

            return $this->successResponse($store, 'success.store_rejected');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Toggle store status between approved and pending
     */
    public function toggleStatus($id): JsonResponse
    {
        try {
            $store = Store::find($id);

            if (!$store) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            $admin = auth('admins')->user();
            
            // Toggle between approved and pending (rejected stores must be explicitly re-approved)
            $newStatus = $store->status === 'approved' ? 'pending' : 'approved';
            
            $updateData = [
                'status' => $newStatus,
            ];

            // If approving, set approval info
            if ($newStatus === 'approved') {
                $updateData['approved_at'] = now();
                $updateData['approved_by'] = $admin->id;
                $updateData['rejection_note'] = null;
            }

            $store->update($updateData);

            return $this->successResponse([
                'id' => $store->id,
                'status' => $store->status
            ], 'success.store_status_updated');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Suspend a store temporarily
     */
    public function suspend(Request $request, $id): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'note' => 'required|string|min:10|max:1000',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $store = Store::find($id);

            if (!$store) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            // Can only suspend approved stores
            if ($store->status !== 'approved') {
                return $this->errorResponse('errors.only_approved_can_suspend', [], 400);
            }

            // Suspend the store
            $store->update([
                'status' => 'suspended',
                'rejection_note' => $request->note,
            ]);

            // Reload relationships
            $store->load(['vendors', 'mainCategories', 'branches']);

            return $this->successResponse($store, 'success.store_suspended');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Reactivate a suspended store
     */
    public function reactivate($id): JsonResponse
    {
        try {
            $store = Store::find($id);

            if (!$store) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            // Can only reactivate suspended stores
            if ($store->status !== 'suspended') {
                return $this->errorResponse('errors.only_suspended_can_reactivate', [], 400);
            }

            $admin = auth('admins')->user();

            // Reactivate the store
            $store->update([
                'status' => 'approved',
                'rejection_note' => null,
                'approved_at' => now(),
                'approved_by' => $admin->id,
            ]);

            // Reload relationships
            $store->load(['vendors', 'mainCategories', 'branches']);

            return $this->successResponse($store, 'success.store_reactivated');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Delete a store (only if pending or rejected)
     */
    public function destroy($id): JsonResponse
    {
        try {
            $store = Store::find($id);

            if (!$store) {
                return $this->errorResponse('errors.not_found', [], 404);
            }

            // Check if store has vendors
            if ($store->vendors()->count() > 0) {
                return $this->errorResponse('errors.cannot_delete_store_with_vendors', [], 400);
            }

            $store->delete();

            return $this->successResponse(null, 'success.store_deleted');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

}
