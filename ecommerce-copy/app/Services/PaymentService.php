<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    /**
     * Initiate payment based on method.
     */
    public function initiatePayment(Order $order, string $method): ?string
    {
        return match ($method) {
            'bkash' => $this->createBkashPayment($order)['url'] ?? null,
            'sslcommerz' => $this->createSslcommerzPayment($order)['url'] ?? null,
            'nagad' => $this->createNagadPayment($order)['url'] ?? null,
            'rocket' => $this->createRocketPayment($order)['url'] ?? null,
            default => null,
        };
    }

    /**
     * Create bKash payment.
     */
    public function createBkashPayment(Order $order): array
    {
        $config = $this->getBkashConfig();

        try {
            // Get token
            $tokenResponse = Http::withHeaders([
                'username' => $config['username'],
                'password' => $config['password'],
            ])->post($this->getBkashBaseUrl() . '/tokenized/checkout/token/grant', [
                'app_key' => $config['app_key'],
                'app_secret' => $config['app_secret'],
            ]);

            $token = $tokenResponse->json('id_token');

            // Create payment
            $response = Http::withHeaders([
                'Authorization' => $token,
                'X-APP-Key' => $config['app_key'],
            ])->post($this->getBkashBaseUrl() . '/tokenized/checkout/create', [
                'mode' => '0011',
                'payerReference' => $order->order_number,
                'callbackURL' => route('payment.bkash.callback', ['order_number' => $order->order_number]),
                'amount' => $order->total,
                'currency' => 'BDT',
                'intent' => 'sale',
                'merchantInvoiceNumber' => $order->order_number,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Execute bKash payment.
     */
    public function executeBkashPayment(string $paymentId): array
    {
        $config = $this->getBkashConfig();

        try {
            $tokenResponse = Http::withHeaders([
                'username' => $config['username'],
                'password' => $config['password'],
            ])->post($this->getBkashBaseUrl() . '/tokenized/checkout/token/grant', [
                'app_key' => $config['app_key'],
                'app_secret' => $config['app_secret'],
            ]);

            $token = $tokenResponse->json('id_token');

            $response = Http::withHeaders([
                'Authorization' => $token,
                'X-APP-Key' => $config['app_key'],
            ])->post($this->getBkashBaseUrl() . '/tokenized/checkout/execute', [
                'paymentID' => $paymentId,
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Create SSLCommerz payment.
     */
    public function createSslcommerzPayment(Order $order): array
    {
        $config = $this->getSslcommerzConfig();

        try {
            $response = Http::asForm()->post($this->getSslcommerzBaseUrl() . '/gwprocess/v4/api.php', [
                'store_id' => $config['store_id'],
                'store_passwd' => $config['store_password'],
                'total_amount' => $order->total,
                'currency' => 'BDT',
                'tran_id' => $order->order_number,
                'success_url' => route('payment.sslcommerz.success'),
                'fail_url' => route('payment.sslcommerz.fail'),
                'cancel_url' => route('payment.sslcommerz.cancel'),
                'ipn_url' => route('payment.sslcommerz.ipn'),
                'cus_name' => $order->shipping_name,
                'cus_email' => $order->shipping_email,
                'cus_phone' => $order->shipping_phone,
                'cus_add1' => $order->shipping_address,
                'cus_city' => $order->shipping_city,
                'cus_state' => $order->shipping_state,
                'cus_postcode' => $order->shipping_postcode,
                'cus_country' => 'Bangladesh',
                'shipping_method' => 'NO',
                'product_name' => 'Order ' . $order->order_number,
                'product_category' => 'E-commerce',
                'product_profile' => 'general',
            ]);

            return $response->json();
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Create Nagad payment (placeholder).
     */
    public function createNagadPayment(Order $order): array
    {
        // Nagad integration would go here
        return ['error' => 'Nagad integration coming soon'];
    }

    /**
     * Create Rocket payment (placeholder).
     */
    public function createRocketPayment(Order $order): array
    {
        // Rocket integration would go here
        return ['error' => 'Rocket integration coming soon'];
    }

    /**
     * Get bKash configuration.
     */
    private function getBkashConfig(): array
    {
        return [
            'merchant_number' => Setting::get('bkash_merchant_number', env('BKASH_MERCHANT_NUMBER')),
            'username' => Setting::get('bkash_username', env('BKASH_USERNAME')),
            'password' => Setting::get('bkash_password', env('BKASH_PASSWORD')),
            'app_key' => Setting::get('bkash_app_key', env('BKASH_APP_KEY')),
            'app_secret' => Setting::get('bkash_app_secret', env('BKASH_APP_SECRET')),
            'sandbox' => Setting::get('bkash_sandbox', env('BKASH_SANDBOX', true)),
        ];
    }

    /**
     * Get SSLCommerz configuration.
     */
    private function getSslcommerzConfig(): array
    {
        return [
            'store_id' => Setting::get('sslcommerz_store_id', env('SSLCOMMERZ_STORE_ID')),
            'store_password' => Setting::get('sslcommerz_store_password', env('SSLCOMMERZ_STORE_PASSWORD')),
            'sandbox' => Setting::get('sslcommerz_sandbox', env('SSLCOMMERZ_SANDBOX', true)),
        ];
    }

    /**
     * Get bKash base URL.
     */
    private function getBkashBaseUrl(): string
    {
        $sandbox = Setting::get('bkash_sandbox', env('BKASH_SANDBOX', true));
        return $sandbox 
            ? 'https://tokenized.sandbox.bka.sh/v1.2.0-beta'
            : 'https://tokenized.pay.bka.sh/v1.2.0-beta';
    }

    /**
     * Get SSLCommerz base URL.
     */
    private function getSslcommerzBaseUrl(): string
    {
        $sandbox = Setting::get('sslcommerz_sandbox', env('SSLCOMMERZ_SANDBOX', true));
        return $sandbox 
            ? 'https://sandbox.sslcommerz.com'
            : 'https://securepay.sslcommerz.com';
    }
}
