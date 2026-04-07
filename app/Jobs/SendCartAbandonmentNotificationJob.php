<?php

namespace App\Jobs;

use App\Models\Cart;
use App\Models\FcmToken;
use App\Models\Order;
use App\Models\SmartNotificationLog;
use App\Models\UserNotification;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCartAbandonmentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle(FcmService $fcmService): void
    {
        try {
            // Carts updated between 24h and 48h ago that have items
            $abandonedCarts = Cart::where('updated_at', '<=', now()->subHours(24))
                                  ->where('updated_at', '>=', now()->subHours(48))
                                  ->has('items')
                                  ->get();

            if ($abandonedCarts->isEmpty()) return;

            $candidateUserIds = [];
            foreach ($abandonedCarts as $cart) {
                // Skip if user made an order after this cart was updated
                $orderedSince = Order::where('user_id', $cart->user_id)
                                     ->where('created_at', '>', $cart->updated_at)
                                     ->exists();
                
                if (!$orderedSince) {
                    $candidateUserIds[] = $cart->user_id;
                }
            }

            if (empty($candidateUserIds)) return;

            // Make unique
            $candidateUserIds = array_unique($candidateUserIds);

            // Skip users already notified about abandonment in the last 24h
            $notifiedUserIds = SmartNotificationLog::whereIn('user_id', $candidateUserIds)
                ->where('type', 'cart_abandonment')
                ->where('sent_at', '>=', now()->subHours(24))
                ->pluck('user_id')
                ->toArray();

            $targetUserIds = array_diff($candidateUserIds, $notifiedUserIds);
            
            if (empty($targetUserIds)) return;

            $tokens = FcmToken::getAllUserTokens($targetUserIds);
            if (empty($tokens)) return;

            $title = "🛒 تركت شيئاً خلفك!";
            $body  = "عربة تسوقك في انتظارك. أكمل طلبك الآن قبل نفاذ الكمية.";
            
            $fcmService->sendToMultiple($tokens, $title, $body, ['click_action' => 'FLUTTER_NOTIFICATION_CLICK']);

            $now = now();
            // Log the notification
            $logs = array_map(fn($uid) => [
                'user_id'    => $uid,
                'product_id' => null,
                'type'       => 'cart_abandonment',
                'sent_at'    => $now,
            ], $targetUserIds);
            SmartNotificationLog::insert($logs);

            // Save in-app notification
            $userNotifications = array_map(fn($uid) => [
                'user_id'    => $uid,
                'title'      => $title,
                'body'       => $body,
                'image_url'  => null,
                'is_read'    => false,
                'created_at' => $now,
                'updated_at' => $now,
            ], $targetUserIds);
            UserNotification::insert($userNotifications);

        } catch (\Exception $e) {
            Log::error('SendCartAbandonmentNotificationJob error: ' . $e->getMessage());
        }
    }
}
