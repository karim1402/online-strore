<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Channels\FcmChannel;
use App\Models\Order;

class NewOrderNotification extends Notification
{
    use Queueable;

    protected $order;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return [FcmChannel::class];
    }

    /**
     * Get the FCM representation of the notification.
     */
    public function toFcm($notifiable): array
    {
        $title = 'New Order Received! 🛒';
        $body = "Order #{$this->order->order_number}\n"
              . "Total: {$this->order->total}\n"
              . "Payment: {$this->order->payment_method}";

        // Load full order with relationships (like show method)
        $orderWithRelations = $this->order->load(['user', 'store', 'items.options', 'items.addons', 'branch', 'vendorInvoice']);

        return [
            'title' => $title,
            'body' => $body,
            'data' => [
                'type' => 'new_order',
                'order_id' => (string) $this->order->id,
                'order_number' => $this->order->order_number,
                'total' => (string) $this->order->total,
                'payment_method' => $this->order->payment_method,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'order' => $orderWithRelations,
            ],
        ];
    }
}
