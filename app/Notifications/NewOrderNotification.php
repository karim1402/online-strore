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
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return [FcmChannel::class];
    }

    /**
     * Get the FCM representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toFcm($notifiable)
    {
        $title = 'New Order Received';
        $body = "New order #{$this->order->order_number} has been placed. Total: {$this->order->total}";

        return [
            'title' => $title,
            'body' => $body,
            'data' => [
                'type' => 'new_order',
                'order_id' => (string) $this->order->id,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK'
            ]
        ];
    }
}
