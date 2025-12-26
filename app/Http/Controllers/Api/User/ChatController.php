<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Events\OrderChat;
use App\Models\Order;
use App\Services\LocalizationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

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

        $order = Order::where('id', $orderId)
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
                'message' => 'No delivery person assigned to this order yet.',
            ], 400);
        }

        if (in_array($order->simple_status, ['delivered', 'cancelled'])) {
            return response()->json([
                'success' => false,
                'message' => 'Chat is closed for this order.',
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

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
        ]);
    }
}
