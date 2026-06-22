<?php

namespace App\Http\Controllers\Api\V2\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReportExport;
use App\Services\SmsMisrService;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * List users with pagination, search.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            $search = $request->get('search');

            $query = User::orderBy('id', 'desc');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            // Filter by date range
            if ($request->filled('from_date')) {
                $query->whereDate('created_at', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->whereDate('created_at', '<=', $request->to_date);
            }

            $users = $query->paginate($perPage);

            // Statistics
            $now = \Carbon\Carbon::now();
            $statistics = [
                'total_users' => [
                    'value' => User::count(),
                    'label' => 'All registered customers',
                ],
                'new_this_month' => [
                    'value' => User::where('created_at', '>=', $now->copy()->startOfMonth())->count(),
                    'label' => 'All registered customers',
                ],
                'verified_accounts' => [
                    'value' => User::whereNotNull('email_verified_at')->count(),
                    'label' => 'Email verified',
                ],
            ];

            return response()->json([
                'success' => true,
                'message' => \App\Services\LocalizationService::getMessage('success.users_retrieved'),
                'data' => $users,
                'statistics' => $statistics,
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Create a new user.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'name' => 'required|string|between:2,100',
                'email' => 'required|string|email|max:100|unique:users,email',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $data = $validator->validated();
            $data['password'] = Hash::make($data['password']);

            $user = User::create($data);

            // Log the creation activity
            $currentAdmin = auth('admins')->user();
            activity('user')
                ->causedBy($currentAdmin)
                ->performedOn($user)
                ->withProperties([
                    'action' => 'created',
                    'created_by' => $currentAdmin->name,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                ])
                ->log('User created by admin');

            return $this->successResponse($user, 'success.user_created', [], 201);
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Show a single user.
     */
    public function show(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            return $this->successResponse($user, 'success.user_retrieved');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Update a user.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $validator = ValidationService::make($request->all(), [
                'name' => 'nullable|string|between:2,100',
                'email' => 'nullable|string|email|max:100|unique:users,email,' . $id,
                'phone' => 'nullable|string|max:20|unique:users,phone,' . $id,
                'password' => 'nullable|string|min:6',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $data = $validator->validated();

            if (!empty($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                unset($data['password']);
            }

            $user->update($data);

            // Log the update activity
            $currentAdmin = auth('admins')->user();
            activity('user')
                ->causedBy($currentAdmin)
                ->performedOn($user)
                ->withProperties([
                    'action' => 'updated',
                    'updated_by' => $currentAdmin->name,
                    'user_name' => $user->name,
                ])
                ->log('User updated by admin');

            return $this->successResponse($user->fresh(), 'success.user_updated');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Delete a user.
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            
            // Log the deletion activity before deleting
            $currentAdmin = auth('admins')->user();
            activity('user')
                ->causedBy($currentAdmin)
                ->performedOn($user)
                ->withProperties([
                    'action' => 'deleted',
                    'deleted_by' => $currentAdmin->name,
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                ])
                ->log('User deleted by admin');
            
            $user->delete();
            return $this->successResponse(null, 'success.user_deleted');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Toggle user status (active/inactive).
     */
    public function toggleStatus(int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);

            $user->status = !$user->status;
            $user->save();

            // Log the status change activity
            $currentAdmin = auth('admins')->user();
            activity('user')
                ->causedBy($currentAdmin)
                ->performedOn($user)
                ->withProperties([
                    'action' => 'status_updated',
                    'updated_by' => $currentAdmin->name,
                    'user_name' => $user->name,
                    'new_status' => $user->status,
                ])
                ->log('User status updated by admin');

            return $this->successResponse($user->fresh(), 'success.user_status_updated');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        } catch (\Exception $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Export users with 0 or 1 orders (name, phone, order count, created_at).
     */
    public function exportLowOrderUsers(Request $request)
    {
        try {
            $users = User::withCount('orders')
                ->with(['orders' => fn($q) => $q->latest()->limit(1)])
                ->having('orders_count', '<=', 1)
                ->orderBy('orders_count', 'asc')
                ->orderBy('id', 'desc')
                ->get();

            $headings = [
                'Name', 'Phone', 'Order Count', 'Created At',
                'Last Order #', 'Last Order Date', 'Last Order Total', 'Last Order Status',
            ];

            $mapper = function ($user) {
                $order = $user->orders->first();
                return [
                    $user->name,
                    $user->phone,
                    $user->orders_count,
                    \Carbon\Carbon::parse($user->created_at)->format('d-m-Y'),
                    $order?->order_number ?? '',
                    $order ? \Carbon\Carbon::parse($order->created_at)->format('d-m-Y') : '',
                    $order?->total ?? '',
                    $order?->simple_status ?? '',
                ];
            };

            return Excel::download(
                new ReportExport($users, $headings, $mapper),
                "users_low_orders_" . now()->format('YmdHis') . ".xlsx"
            );
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Export all users with name, phone, orders count and created_at.
     */
    public function export(Request $request)
    {
        try {
            $users = User::withCount('orders')->orderBy('id', 'desc')->get();
            
            $headings = ['Name', 'Phone', 'Total Orders', 'Created At'];
            
            $mapper = fn($row) => [
                $row['name'],
                $row['phone'],
                $row['orders_count'],
                \Carbon\Carbon::parse($row['created_at'])->format('Y-m-d H:i:s')
            ];

            return Excel::download(
                new ReportExport(collect($users), $headings, $mapper),
                "users_export_" . now()->format('YmdHis') . ".xlsx"
            );
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * List soft-deleted users with pagination, search.
     */
    public function deletedIndex(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            $search = $request->get('search');

            $query = User::onlyTrashed()->orderBy('deleted_at', 'desc');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            if ($request->filled('from_date')) {
                $query->whereDate('deleted_at', '>=', $request->from_date);
            }

            if ($request->filled('to_date')) {
                $query->whereDate('deleted_at', '<=', $request->to_date);
            }

            $users = $query->paginate($perPage);

            $statistics = [
                'total_deleted' => [
                    'value' => User::onlyTrashed()->count(),
                    'label' => 'All deleted customers',
                ],
            ];

            return response()->json([
                'success'    => true,
                'message'    => \App\Services\LocalizationService::getMessage('success.deleted_users_retrieved'),
                'data'       => $users,
                'statistics' => $statistics,
            ]);
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Restore a soft-deleted user.
     */
    public function restore(int $id): JsonResponse
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);

            $user->restore();

            $currentAdmin = auth('admins')->user();
            activity('user')
                ->causedBy($currentAdmin)
                ->performedOn($user)
                ->withProperties([
                    'action'       => 'restored',
                    'restored_by'  => $currentAdmin->name,
                    'user_name'    => $user->name,
                    'user_email'   => $user->email,
                ])
                ->log('User restored by admin');

            return $this->successResponse($user->fresh(), 'success.user_restored');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Get paginated orders for a user.
     */
    public function userOrders(Request $request, int $id): JsonResponse
    {
        try {
            $user = User::findOrFail($id);
            $perPage = (int) $request->get('limit', 15);

            $query = Order::where('user_id', $user->id)->orderBy('created_at', 'desc');

            if ($request->filled('status')) {
                $query->where('simple_status', $request->get('status'));
            }

            $orders = $query->paginate($perPage);

            return $this->successResponse($orders, 'success.data_retrieved');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return $this->notFoundResponse('errors.resource_not_found');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    // ─── SMS Campaign Endpoints ───────────────────────────────────────────

    private const CAMPAIGN_MESSAGE = "⚽ هدية من مطعم مكوك!\nاستخدم برومو كود: WORLDCUP\nعلى طلبك القادم من مطعم مكوك\nواستمتع بالخصم 🔥\nhttp://bit.ly/4aqZ7KQ\nحمله الان";

    /**
     * Day 0 (test): send only to 201207048631.
     */
    public function smsDay0()
    {
        $result = SmsMisrService::sendCampaign(['201207048631'], self::CAMPAIGN_MESSAGE);

        return response()->json(['success' => true, 'data' => [...$result, 'total_numbers' => 1]]);
    }

    /**
     * Day 1: first 800 users with 0 orders + first 280 users with 1 order.
     */
    public function smsDay1()
    {
        $phones = $this->collectPhones([
            $this->smsUsers(0, 0, 800),
            $this->smsUsers(1, 0, 280),
        ]);

        $result = SmsMisrService::sendCampaign($phones, self::CAMPAIGN_MESSAGE);

        return response()->json(['success' => true, 'data' => [...$result, 'total_numbers' => \count($phones)]]);
    }

    /**
     * Day 2: next 800 users with 0 orders + next 280 users with 1 order.
     */
    public function smsDay2()
    {
        $phones = $this->collectPhones([
            $this->smsUsers(0, 800, 800),
            $this->smsUsers(1, 280, 280),
        ]);

        $result = SmsMisrService::sendCampaign($phones, self::CAMPAIGN_MESSAGE);

        return response()->json(['success' => true, 'data' => [...$result, 'total_numbers' => \count($phones)]]);
    }

    /**
     * Day 3: next 400 users with 0 orders only.
     */
    public function smsDay3()
    {
        $phones = $this->collectPhones([
            $this->smsUsers(0, 1600, 400),
        ]);

        $result = SmsMisrService::sendCampaign($phones, self::CAMPAIGN_MESSAGE);

        return response()->json(['success' => true, 'data' => [...$result, 'total_numbers' => \count($phones)]]);
    }

    /** Merge multiple smsUsers() result sets into a flat array of phone strings. */
    private function collectPhones(array $groups): array
    {
        $phones = ['201207048631']; // always included
        foreach ($groups as $group) {
            foreach ($group as $user) {
                $phones[] = $user['phone'];
            }
        }
        return array_values(array_unique($phones));
    }

    /**
     * Fetch users with exactly $orderCount orders, apply phone cleaning,
     * skip invalid numbers, and return [name, phone] pairs.
     */
    private function smsUsers(int $orderCount, int $skip, int $take): array
    {
        $users = User::withCount('orders')
            ->having('orders_count', $orderCount)
            ->orderBy('id', 'desc')
            ->skip($skip)
            ->take($take)
            ->get(['id', 'name', 'phone']);

        $result = [];
        foreach ($users as $user) {
            $phone = $this->cleanPhone($user->phone);
            if ($phone !== null) {
                $result[] = ['name' => $user->name, 'phone' => $phone];
            }
        }
        return $result;
    }

    /**
     * Prepend '2' if not present, then reject if length < 10 or > 13.
     */
    private function cleanPhone(?string $phone): ?string
    {
        if (empty($phone)) return null;
        if (!str_starts_with($phone, '2')) {
            $phone = '2' . $phone;
        }
        $len = strlen($phone);
        if ($len < 10 || $len > 13) return null;
        return $phone;
    }
}
