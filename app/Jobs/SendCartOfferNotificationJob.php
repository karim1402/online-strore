<?php

namespace App\Jobs;

use App\Models\CartItem;
use App\Models\FcmToken;
use App\Models\Product;
use App\Models\SmartNotificationLog;
use App\Models\UserNotification;
use App\Services\FcmService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendCartOfferNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $productId;

    public function __construct($productId)
    {
        $this->productId = $productId;
    }

    public function handle(FcmService $fcmService): void
    {
        try {
            $product = Product::find($this->productId);
            if (!$product || !$product->offer_price) return;

            // Find all unique users who have this product in their cart
            $userIds = CartItem::where('product_id', $this->productId)
                ->join('carts', 'carts.id', '=', 'cart_items.cart_id')
                ->distinct()
                ->pluck('carts.user_id')
                ->toArray();

            if (empty($userIds)) return;

            // Filter out users who already received this notification in the last 24h
            $notifiedUserIds = SmartNotificationLog::whereIn('user_id', $userIds)
                ->where('type', 'cart_offer')
                ->where('product_id', $this->productId)
                ->where('sent_at', '>=', now()->subHours(24))
                ->pluck('user_id')
                ->toArray();

            $targetUserIds = array_diff($userIds, $notifiedUserIds);
            
            if (empty($targetUserIds)) return;

            $tokens = FcmToken::getAllUserTokens($targetUserIds);
            if (empty($tokens)) return;

            $productName = $product->name_ar ?? $product->name_en ?? 'أحد المنتجات';
            $title = "🔥 أخبار سارة!";
            $body  = "المنتج {$productName} الموجود في عربة تسوقك معروض الآن بخصم — احصل عليه قبل نفاذ الكمية!";
            
            $fcmService->sendToMultiple($tokens, $title, $body, ['click_action' => 'FLUTTER_NOTIFICATION_CLICK']);

            $now = now();
            // Log the notification
            $logs = array_map(fn($uid) => [
                'user_id'    => $uid,
                'product_id' => $this->productId,
                'type'       => 'cart_offer',
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
            Log::error('SendCartOfferNotificationJob error: ' . $e->getMessage());
        }
    }
}
