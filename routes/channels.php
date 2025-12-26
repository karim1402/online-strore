<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('order.{orderId}', function ($user, $orderId) {
    // Check if user is a customer
    if ($user instanceof \App\Models\User) {
        return \App\Models\Order::where('id', $orderId)
            ->where('user_id', $user->id)
            ->exists();
    }

    // Check if user is a delivery person
    if ($user instanceof \App\Models\Delivery) {
        return \App\Models\Order::where('id', $orderId)
            ->where('delivery_id', $user->id)
            ->exists();
    }

    return false;
}, ['guards' => ['api', 'deliveries']]);
