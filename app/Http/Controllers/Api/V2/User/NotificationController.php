<?php

namespace App\Http\Controllers\Api\V2\User;

use App\Http\Controllers\Controller;
use App\Models\UserNotification;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    /**
     * Get paginated notifications for the authenticated user.
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $perPage = (int) $request->get('limit', 15);

        $notifications = UserNotification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return $this->successResponse($notifications, 'success.data_retrieved');
    }

    /**
     * Get the count of unread notifications.
     */
    public function getUnreadCount(): JsonResponse
    {
        $user = auth('api')->user();

        $count = UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        return $this->successResponse(['unread_count' => $count], 'success.data_retrieved');
    }

    /**
     * Mark a specific notification as read.
     */
    public function markAsRead($id): JsonResponse
    {
        $user = auth('api')->user();

        $notification = UserNotification::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$notification) {
            return $this->notFoundResponse();
        }

        if (!$notification->is_read) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return $this->successResponse($notification, 'success.updated');
    }

    /**
     * Mark all notifications as read for the user.
     */
    public function markAllAsRead(): JsonResponse
    {
        $user = auth('api')->user();

        UserNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return $this->successResponse(null, 'success.updated');
    }
}
