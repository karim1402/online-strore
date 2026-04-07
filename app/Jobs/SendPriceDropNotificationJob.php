<?php

namespace App\Jobs;

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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendPriceDropNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $productId;
    protected $newPrice;

    public function __construct($productId, $newPrice)
    {
        $this->productId = $productId;
        $this->newPrice  = $newPrice;
    }

    public function handle(FcmService $fcmService): void
    {
        try {
            $product = Product::find($this->productId);
            if (!$product) return;

            // Find users who viewed this product in the last 48 hours
            $userIds = DB::table('product_views')
                ->where('product_id', $this->productId)
                ->where('viewed_at', '>=', now()->subHours(48))
                ->distinct()
                ->pluck('user_id')
                ->toArray();

            if (empty($userIds)) return;

            // Skip users already notified about a price drop for THIS product in the last 24h
            $notifiedUserIds = SmartNotificationLog::whereIn('user_id', $userIds)
                ->where('type', 'price_drop')
                ->where('product_id', $this->productId)
                ->where('sent_at', '>=', now()->subHours(24))
                ->pluck('user_id')
                ->toArray();

            $targetUserIds = array_diff($userIds, $notifiedUserIds);
            
            if (empty($targetUserIds)) return;

            $tokens = FcmToken::getAllUserTokens($targetUserIds);
            if (empty($tokens)) return;

            $productName = $product->name_ar ?? $product->name_en ?? 'أحد المنتجات التي شاهدتها';
            $title = "📉 تنبيه بانخفاض السعر!";
            $body  = "أخبار رائعة! {$productName} متاح الآن بسعر {$this->newPrice} جنيه فقط. احصل عليه قبل نفاذ الكمية.";
            
            $fcmService->sendToMultiple($tokens, $title, $body, ['click_action' => 'FLUTTER_NOTIFICATION_CLICK']);

            $now = now();
            // Log the notification
            $logs = array_map(fn($uid) => [
                'user_id'    => $uid,
                'product_id' => $this->productId,
                'type'       => 'price_drop',
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
            Log::error('SendPriceDropNotificationJob error: ' . $e->getMessage());
        }
    }
}
