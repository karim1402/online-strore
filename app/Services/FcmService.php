<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;

class FcmService
{
    protected $messaging;

    public function __construct()
    {
        try {
            $credentialsPath = base_path(env('FIREBASE_CREDENTIALS', 'storage/app/firebase-auth.json'));
            
            if (!file_exists($credentialsPath)) {
                Log::warning('Firebase credentials file not found at: ' . $credentialsPath);
                return;
            }

            $factory = (new Factory)
                ->withServiceAccount($credentialsPath);

            $this->messaging = $factory->createMessaging();
        } catch (\Exception $e) {
            Log::error('Firebase initialization error: ' . $e->getMessage());
        }
    }

    /**
     * Send notification to a specific device token
     *
     * @param string $token
     * @param string $title
     * @param string $body
     * @param array $data
     * @param string|null $imageUrl
     * @return bool
     */
    public function sendNotification(string $token, string $title, string $body, array $data = [], ?string $imageUrl = null): bool
    {
        if (!$this->messaging) {
            return false;
        }

        try {
            $notification = Notification::create($title, $body, $imageUrl);
            $message = CloudMessage::withTarget('token', $token)
                ->withNotification($notification)
                ->withData($data);

            $this->messaging->send($message);
            return true;
        } catch (\Exception $e) {
            Log::error('FCM Send Error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send notification to multiple tokens
     *
     * @param array $tokens
     * @param string $title
     * @param string $body
     * @param array $data
     * @param string|null $imageUrl
     * @return array
     */
    public function sendToMultiple(array $tokens, string $title, string $body, array $data = [], ?string $imageUrl = null): array
    {
        if (!$this->messaging) {
            Log::error('FCM sendToMultiple: messaging is not initialized. Check FIREBASE_CREDENTIALS path and file.');
            return ['success' => 0, 'failure' => count($tokens)];
        }

        try {
            $notification = Notification::create($title, $body, $imageUrl);
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($data);

            $report = $this->messaging->sendMulticast($message, $tokens);

            return [
                'success' => $report->successes()->count(),
                'failure' => $report->failures()->count(),
            ];
        } catch (\Exception $e) {
            Log::error('FCM sendToMultiple error: ' . $e->getMessage(), [
                'token_count' => count($tokens),
                'title'       => $title,
            ]);
            return ['success' => 0, 'failure' => count($tokens)];
        }
    }

    /**
     * Send notification to a topic
     *
     * @param string $topic
     * @param string $title
     * @param string $body
     * @param array $data
     * @param string|null $imageUrl
     * @return bool
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = [], ?string $imageUrl = null): bool
    {
        if (!$this->messaging) {
            return false;
        }

        try {
            $notification = Notification::create($title, $body, $imageUrl);
            $message = CloudMessage::withTarget('topic', $topic)
                ->withNotification($notification)
                ->withData($data);

            $this->messaging->send($message);
            return true;
        } catch (\Exception $e) {
            Log::error('FCM Topic Send Error: ' . $e->getMessage());
            return false;
        }
    }
}
