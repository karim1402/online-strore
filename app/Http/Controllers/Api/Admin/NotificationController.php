<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FcmService;
use App\Services\ValidationService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
                'title' => 'required|string|max:255',
                'body' => 'required|string',
                'image' => 'nullable|image|max:2048', // Max 2MB
                'user_ids' => 'nullable|array',
                'user_ids.*' => 'exists:users,id',
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

            // Fetch Tokens from fcm_tokens table
            if (!empty($data['user_ids'])) {
                $tokens = \App\Models\FcmToken::getAllUserTokens($data['user_ids']);
                $targetType = 'specific_users';
            } else {
                $tokens = \App\Models\FcmToken::getAllTokens();
                $targetType = 'all_devices';
            }

            if (empty($tokens)) {
                return $this->errorResponse('errors.no_users_with_tokens', [], 404);
            }

            // Send Notification
            $result = $this->fcmService->sendToMultiple(
                $tokens,
                $data['title'],
                $data['body'],
                ['click_action' => 'FLUTTER_NOTIFICATION_CLICK'],
                $imageUrl
            );

            // Save History
            DB::table('push_notifications')->insert([
                'title' => $data['title'],
                'body' => $data['body'],
                'image_url' => $imageUrl,
                'target_type' => $targetType,
                'success_count' => $result['success'],
                'failure_count' => $result['failure'],
                'sender_id' => auth('admins')->id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Save individual user notifications
            if ($targetType === 'specific_users' && !empty($data['user_ids'])) {
                $userNotifications = [];
                $now = now();
                foreach ($data['user_ids'] as $userId) {
                    $userNotifications[] = [
                        'user_id' => $userId,
                        'title' => $data['title'],
                        'body' => $data['body'],
                        'image_url' => $imageUrl,
                        'is_read' => false,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }
                
                foreach (array_chunk($userNotifications, 500) as $chunk) {
                    \App\Models\UserNotification::insert($chunk);
                }
            }

            return $this->successResponse($result, 'success.notification_sent');

        } catch (\Throwable $e) {
            return $this->errorResponse('errors.server_error', [], 500);
        }
    }
}
