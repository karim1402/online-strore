<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class NewOrderEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $title;
    public $body;
    public $data;

    /**
     * Create a new event instance.
     */
    public function __construct(Order $order)
    {
        // Load full order with relationships (same as FCM notification)
        $orderWithRelations = $order->load(['user', 'store', 'items.options', 'items.addons', 'branch', 'vendorInvoice']);

        $this->title = 'New Order Received! 🛒';
        $this->body = "Order #{$order->order_number}\nTotal: {$order->total}\nPayment: {$order->payment_method}";
        $this->data = [
            'type' => 'new_order',
            'order_id' => (string) $order->id,
            'order_number' => $order->order_number,
            'total' => (string) $order->total,
            'payment_method' => $order->payment_method,
            'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            'order' => $orderWithRelations,
        ];

        // Log how many admins are currently subscribed to the channel
        $this->logAdminSubscriberCount($order->order_number);
    }

    /**
     * Log the number of admin subscribers on the Pusher channel.
     */
    private function logAdminSubscriberCount(string $orderNumber): void
    {
        try {
            $pusher = new \Pusher\Pusher(
                config('broadcasting.connections.pusher.key'),
                config('broadcasting.connections.pusher.secret'),
                config('broadcasting.connections.pusher.app_id'),
                [
                    'cluster' => config('broadcasting.connections.pusher.options.cluster'),
                    'useTLS'  => true,
                ]
            );

            $channelInfo = $pusher->getChannelInfo('admin-notifications', ['info' => 'subscription_count']);

            $subscriberCount = $channelInfo->subscription_count ?? 0;

            Log::info("NewOrderEvent broadcast: Order #{$orderNumber} — {$subscriberCount} admin(s) currently subscribed to channel 'admin-notifications'");
        } catch (\Exception $e) {
            Log::warning("NewOrderEvent: Could not fetch subscriber count for order #{$orderNumber}: " . $e->getMessage());
        }
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('admin-notifications'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'new-order';
    }
}
