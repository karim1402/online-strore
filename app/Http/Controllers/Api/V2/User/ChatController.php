<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\OrderChat;
use App\Models\Order;
use App\Services\LocalizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    /**
     * Send a message to the delivery person
     */
    public function sendMessage(Request $request, $orderId): JsonResponse
    {
        $user = auth('api')->user();

        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.validation_failed'),
                'errors' => $validator->errors(),
            ], 422);
        }

        $order = Order::with('delivery')->where('id', $orderId)
            ->where('user_id', $user->id)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('errors.order_not_found'),
            ], 404);
        }

        if (!$order->delivery_id) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('chat.no_delivery_person'),
            ], 400);
        }

        if (in_array($order->simple_status, ['delivered', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => LocalizationService::getMessage('chat.chat_closed'),
            ], 400);
        }

        // Broadcast event
        broadcast(new OrderChat(
            $order->id,
            $request->message,
            'user',
            $user->id,
            $user->name
        ))->toOthers();

        // Send FCM notification to delivery
        $this->notifyDelivery($order, $user, $request->message);

        return response()->json([
            'success' => true,
            'message' => LocalizationService::getMessage('chat.message_sent'),
        ]);
    }

    /**
     * Send FCM notification to the delivery person
     */
    private function notifyDelivery(Order $order, $user, string $message): void
    {
        try {
            if (!$order->delivery || !$order->delivery->fcm_token) {
                Log::info('No FCM token for delivery of order: ' . $order->order_number);
                return;
            }

            $fcmService = app(\App\Services\FcmService::class);

            $title = "Message from {$user->name}";
            $body = mb_strlen($message) > 100 ? mb_substr($message, 0, 100) . '...' : $message;

            $data = [
                'type' => 'chat_message',
                'order_id' => (string) $order->id,
                'order_number' => $order->order_number,
                'channel_id' => 'order.' . $order->id,
                'sender_type' => 'user',
                'sender_id' => (string) $user->id,
                'sender_name' => $user->name,
                'message' => $message,
            ];

            $success = $fcmService->sendNotification($order->delivery->fcm_token, $title, $body, $data);

            Log::info('Chat notification sent to delivery for order: ' . $order->order_number, [
                'delivery_id' => $order->delivery->id,
                'success' => $success,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send chat notification to delivery: ' . $e->getMessage());
        }
    }
}
