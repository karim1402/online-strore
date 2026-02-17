<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewOrderEvent implements ShouldBroadcast
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
