<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Services\FcmService;
use Illuminate\Support\Facades\Log;

class FcmChannel
{
    protected $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (!method_exists($notification, 'toFcm')) {
            Log::warning('Notification does not have toFcm method', ['notification' => get_class($notification)]);
            return;
        }

        $fcmData = $notification->toFcm($notifiable);
        
        // Get token from notifiable
        $token = $notifiable->routeNotificationForFcm($notification);

        if (!$token) {
            // Log::info('No FCM token for user', ['user_id' => $notifiable->id]);
            return;
        }

        $this->fcmService->sendNotification(
            $token,
            $fcmData['title'] ?? 'New Notification',
            $fcmData['body'] ?? '',
            $fcmData['data'] ?? [],
            $fcmData['image'] ?? null
        );
    }
}
