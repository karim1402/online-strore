<?php

namespace App\Http\Controllers\Api\Delivery;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\OrderChat;
use App\Models\Order;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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

        $order = Order::with('user')->where('id', $orderId)
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

        // Send FCM notification to user
        $this->notifyUser($order, $delivery, $request->message);

        return $this->successResponse(null, 'Message sent successfully');
    }

    /**
     * Send FCM notification to the user
     */
    private function notifyUser(Order $order, $delivery, string $message): void
    {
        try {
            if (!$order->user) {
                Log::info('No user for order: ' . $order->order_number);
                return;
            }

            $tokens = \App\Models\FcmToken::getTokensForUser(\App\Models\User::class, $order->user->id);

            if (empty($tokens)) {
                Log::info('No FCM tokens for user of order: ' . $order->order_number);
                return;
            }

            $fcmService = app(\App\Services\FcmService::class);

            $title = "Message from {$delivery->name}";
            $body = mb_strlen($message) > 100 ? mb_substr($message, 0, 100) . '...' : $message;

            $data = [
                'type' => 'chat_message',
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
                'channel_id' => 'order.' . $order->id,
                'sender_type' => 'delivery',
                'sender_id' => (string) $delivery->id,
                'sender_name' => $delivery->name,
                'message' => $message,
            ];

            $result = $fcmService->sendToMultiple($tokens, $title, $body, $data);

            Log::info('Chat notification sent to user for order: ' . $order->order_number, [
                'user_id' => $order->user->id,
                'tokens_count' => count($tokens),
                'success' => $result['success'] ?? 0,
                'failure' => $result['failure'] ?? 0,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send chat notification to user: ' . $e->getMessage());
        }
    }
}
