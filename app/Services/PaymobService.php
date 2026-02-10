<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymobService
{
    protected $baseUrl;
    protected $secretKey;
    protected $integrationId;
    protected $hmacSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.paymob.base_url');
        $this->secretKey = config('services.paymob.secret_key');
        $this->integrationId = config('services.paymob.integration_id');
        $this->hmacSecret = config('services.paymob.hmac_secret');
    }

    /**
     * Create a payment intention
     * 
     * @param array $data Order data
     * @return array from Paymob
     */
    public function createPaymentIntention(array $data)
    {
        try {
            $url = $this->baseUrl . '/v1/intention/';
            
            $payload = [
                'amount' => $data['amount_cents'], // Amount in cents
                'currency' => $data['currency'] ?? 'EGP',
                'payment_methods' => [
                    (int) $this->integrationId
                ],
                'items' => $data['items'] ?? [],
                'billing_data' => $data['billing_data'],
                'special_reference' => $data['special_reference'] ?? null,
                'notification_url' => $data['notification_url'] ?? route('api.payments.webhook'),
                'redirection_url' => 'https://makook.devdigitalvibes.com/payment/order?success=true&id=123456789&amount_cents=15000&currency=EGP&order=987654321&is_3d_secure=true&pending=false&is_standalone_payment=true&hmac=a1b2c3d4...', // User provided URL
            ];

            Log::info('Creating Paymob intention payload:', $payload);

            $response = Http::withHeaders([
                'Authorization' => 'Token ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Paymob intention creation failed:', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new \Exception('Failed to create payment intention: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Paymob Service Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Verify HMAC signature for webhook
     */
    public function verifyHmac(array $data, string $hmac): bool
    {
        $hmacString = '';
        $hmacMapping = [
            'amount_cents',
            'created_at',
            'currency',
            'error_occured',
            'has_parent_transaction',
            'id',
            'integration_id',
            'is_3d_secure',
            'is_auth',
            'is_capture',
            'is_refunded',
            'is_standalone_payment',
            'is_voided',
            'order.id',
            'owner',
            'pending',
            'source_data.pan',
            'source_data.sub_type',
            'source_data.type',
            'success',
        ];

        foreach ($hmacMapping as $key) {
            $value = $this->getValueByDotNotation($data, $key);
            $hmacString .= $value;
        }

        $calculatedHmac = hash_hmac('sha512', $hmacString, $this->hmacSecret);

        return $calculatedHmac === $hmac;
    }

    private function getValueByDotNotation($array, $key)
    {
        $keys = explode('.', $key);
        foreach ($keys as $k) {
            if (!isset($array[$k])) {
                // Handle different boolean representations Paymob sends
                if ($k === 'error_occured' || $k === 'has_parent_transaction' || 
                    $k === 'is_3d_secure' || $k === 'is_auth' || $k === 'is_capture' || 
                    $k === 'is_refunded' || $k === 'is_standalone_payment' || 
                    $k === 'is_voided' || $k === 'pending' || $k === 'success') {
                    return 'false';
                }
                return '';
            }
            $array = $array[$k];
        }

        if (is_bool($array)) {
            return $array ? 'true' : 'false';
        }

        return $array;
    }
}
