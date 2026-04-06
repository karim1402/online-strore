<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\FcmToken;
use App\Models\Order;
use App\Models\User;
use App\Models\UserNotification;
use App\Services\FcmService;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    use ApiResponse;

    protected $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    /**
     * List notification history
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $perPage = (int) $request->get('per_page', 15);
            
            $notifications = DB::table('push_notifications')
                ->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return $this->successResponse($notifications, 'success.notifications_retrieved');
        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }

    /**
     * Send push notification
     */
    public function send(Request $request): JsonResponse
    {
        try {
            $validator = ValidationService::make($request->all(), [
                'title'                  => 'required|string|max:255',
                'body'                   => 'required|string',
                'image'                  => 'nullable|image|max:2048',
                'target_type'            => 'nullable|string|in:all_users,specific_users,one_time_orderers,multiple_orderers,never_ordered,never_logged_in,inactive_users,high_spenders,cancelled_order_users,new_registrants,cash_on_delivery_users,online_payment_users',
                'user_ids'               => 'required_if:target_type,specific_users|array',
                'user_ids.*'             => 'exists:users,id',
                'min_order_count'        => 'nullable|integer|min:2',
                'inactive_days'          => 'nullable|integer|min:1',
                'min_total_spent'        => 'nullable|numeric|min:0',
                'registered_within_days' => 'nullable|integer|min:1',
            ]);

            if ($validator->fails()) {
                return $this->validationErrorWithFirstMessage($validator);
            }

            $data = $validator->validated();
            $imageUrl = null;

            // Handle Image Upload
            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('notifications', 'public');
                $imageUrl = asset('storage/' . $path);
            }

            // --- Token Resolution ---
            $targetType    = $data['target_type'] ?? 'all_users';
            $targetCriteria = [];
            $userIds       = [];
            $tokens        = [];

            switch ($targetType) {

                case 'specific_users':
                    $userIds = $data['user_ids'];
                    $tokens  = FcmToken::getAllUserTokens($userIds);
                    break;

                case 'one_time_orderers':
                    $userIds = Order::select('user_id')
                        ->groupBy('user_id')
                        ->havingRaw('COUNT(*) = 1')
                        ->pluck('user_id')->toArray();
                    $tokens = FcmToken::getAllUserTokens($userIds);
                    break;

                case 'multiple_orderers':
                    $min = $data['min_order_count'] ?? 2;
                    $targetCriteria = ['min_order_count' => $min];
                    $userIds = Order::select('user_id')
                        ->groupBy('user_id')
                        ->havingRaw('COUNT(*) >= ?', [$min])
                        ->pluck('user_id')->toArray();
                    $tokens = FcmToken::getAllUserTokens($userIds);
                    break;

                case 'never_ordered':
                    $orderedIds = Order::distinct()->pluck('user_id')->toArray();
                    $userIds    = User::whereNotIn('id', $orderedIds)->pluck('id')->toArray();
                    $tokens     = FcmToken::getAllUserTokens($userIds);
                    break;

                case 'never_logged_in':
                    $loggedInIds = FcmToken::where('tokenable_type', User::class)
                        ->distinct()->pluck('tokenable_id')->toArray();
                    $userIds = User::whereNotIn('id', $loggedInIds)->pluck('id')->toArray();
                    $tokens  = []; // No device token — in-app record only
                    break;

                case 'inactive_users':
                    $days = $data['inactive_days'] ?? 30;
                    $targetCriteria = ['inactive_days' => $days];
                    $activeIds = Order::where('created_at', '>=', now()->subDays($days))
                        ->distinct()->pluck('user_id')->toArray();
                    $userIds = User::whereNotIn('id', $activeIds)->pluck('id')->toArray();
                    $tokens  = FcmToken::getAllUserTokens($userIds);
                    break;

                case 'high_spenders':
                    $minSpent = $data['min_total_spent'] ?? 0;
                    $targetCriteria = ['min_total_spent' => $minSpent];
                    $userIds = Order::select('user_id')
                        ->groupBy('user_id')
                        ->havingRaw('SUM(total) >= ?', [$minSpent])
                        ->pluck('user_id')->toArray();
                    $tokens = FcmToken::getAllUserTokens($userIds);
                    break;

                case 'cancelled_order_users':
                    $userIds = Order::where('simple_status', 'cancelled')
                        ->distinct()->pluck('user_id')->toArray();
                    $tokens  = FcmToken::getAllUserTokens($userIds);
                    break;

                case 'new_registrants':
                    $withinDays = $data['registered_within_days'] ?? 7;
                    $targetCriteria = ['registered_within_days' => $withinDays];
                    $userIds = User::where('created_at', '>=', now()->subDays($withinDays))
                        ->pluck('id')->toArray();
                    $tokens  = FcmToken::getAllUserTokens($userIds);
                    break;

                case 'cash_on_delivery_users':
                    $userIds = Order::where('payment_method', 'cash')
                        ->distinct()->pluck('user_id')->toArray();
                    $tokens  = FcmToken::getAllUserTokens($userIds);
                    break;

                case 'online_payment_users':
                    $userIds = Order::where('payment_method', '!=', 'cash')
                        ->distinct()->pluck('user_id')->toArray();
                    $tokens  = FcmToken::getAllUserTokens($userIds);
                    break;

                default: // all_users
                    $targetType = 'all_users';
                    $tokens     = FcmToken::getAllUserTokens();
                    $userIds    = FcmToken::where('tokenable_type', User::class)
                        ->distinct()->pluck('tokenable_id')->toArray();
                    break;
            }

            // Send Notification (only if there are tokens)
            $result = ['success' => 0, 'failure' => 0];
            if (!empty($tokens)) {
                $result = $this->fcmService->sendToMultiple(
                    $tokens,
                    $data['title'],
                    $data['body'],
                    ['click_action' => 'FLUTTER_NOTIFICATION_CLICK'],
                    $imageUrl
                );
            }

            // Save History
            DB::table('push_notifications')->insert([
                'title'         => $data['title'],
                'body'          => $data['body'],
                'image_url'     => $imageUrl,
                'target_type'   => $targetType,
                'success_count' => $result['success'],
                'failure_count' => $result['failure'],
                'sender_id'     => auth('admins')->id(),
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);

            // Save individual UserNotification records for in-app display
            if (!empty($userIds)) {
                $now = now();
                $chunks = array_chunk($userIds, 500);
                foreach ($chunks as $chunk) {
                    $rows = array_map(fn($uid) => [
                        'user_id'    => $uid,
                        'title'      => $data['title'],
                        'body'       => $data['body'],
                        'image_url'  => $imageUrl,
                        'is_read'    => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ], $chunk);
                    UserNotification::insert($rows);
                }
            }

            return $this->successResponse(array_merge($result, [
                'target_type'    => $targetType,
                'targeted_users' => count($userIds),
            ]), 'success.notification_sent');

        } catch (\Throwable $e) {
            Log::error('NotificationController send error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
