<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\OrderChat;
use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    use ApiResponse;

    /**
     * Send a message to the user
     */
    public function sendMessage(Request $request, $orderId): JsonResponse
    {
        $delivery = auth('deliveries')->user();

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('errors.validation_failed', $validator->errors(), 422);
        }

        $order = Order::where('id', $orderId)
            ->where('delivery_id', $delivery->id)
            ->first();

        if (!$order) {
            return $this->errorResponse('errors.order_not_found', [], 404);
        }

        if (in_array($order->simple_status, ['delivered', 'cancelled'])) {
            return $this->errorResponse('errors.chat_closed', [], 400);
        }

        // Broadcast event
        broadcast(new OrderChat(
            $order->id,
            $request->message,
            'delivery',
            $delivery->id,
            $delivery->name
        ))->toOthers();

        return $this->successResponse(null, 'Message sent successfully');
    }
}
