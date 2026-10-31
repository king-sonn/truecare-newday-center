<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * M-Pesa / Daraja STK Push Service.
 *
 * Handles OAuth access token retrieval (with short-lived cache and 401 refresh)
 * and STK Push requests to Safaricom's Daraja API (sandbox by default).
 *
 * @package App\Services
 */
class MpesaService
{
    protected string $baseUrl;
    protected string $consumerKey;
    protected string $consumerSecret;
    protected string $shortcode;
    protected string $passkey;
    protected string $paybill;
    protected string $account;
    protected string $accountName;
    protected string $callbackUrl;
    protected string $securityCredential;

    public function __construct()
    {
        $this->baseUrl = mpesa_base_url();
        $this->consumerKey = mpesa_config('consumer_key');
        $this->consumerSecret = mpesa_config('consumer_secret');
        $this->shortcode = mpesa_config('shortcode', '174379');
        $this->passkey = mpesa_config('passkey');
        $this->paybill = mpesa_config('paybill', '247247');
        $this->account = mpesa_config('account', '958526');
        $this->accountName = mpesa_config('account_name', 'Truecare new day center');
        $this->callbackUrl = mpesa_config('callback_url', '');
        $this->securityCredential = mpesa_config('security_credential');
    }

    public function getAccessToken(): array
    {
        $cacheKey = 'mpesa_token_' . md5($this->baseUrl . $this->consumerKey);

        $token = Cache::get($cacheKey);
        if ($token) {
            return ['success' => true, 'access_token' => $token, 'source' => 'cache'];
        }

        try {
            $response = Http::withBasicAuth($this->consumerKey, $this->consumerSecret)
                ->timeout(30)
                ->get("{$this->baseUrl}/oauth/v1/generate?grant_type=client_credentials");

            $body = $response->json();

            if ($response->successful() && isset($body['access_token'])) {
                $token = $body['access_token'];
                $ttl = (int) ($body['expires_in'] ?? 3500) - 60;
                Cache::put($cacheKey, $token, max($ttl, 60));

                return ['success' => true, 'access_token' => $token, 'source' => 'fresh'];
            }

            Log::warning('Mpesa getAccessToken unsuccessful', [
                'status' => $response->status(),
                'body' => $body,
            ]);

            return [
                'success' => false,
                'message' => $body['error_description'] ?? 'Failed to obtain M-Pesa access token',
                'status_code' => $response->status(),
            ];
        } catch (Exception $e) {
            Log::error('Mpesa getAccessToken exception', ['exception' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ];
        }
    }

    public function forgetToken(): void
    {
        $cacheKey = 'mpesa_token_' . md5($this->baseUrl . $this->consumerKey);
        Cache::forget($cacheKey);
    }

    public function stkPush(string $phone, $amount, string $accountReference = '958526', string $transactionDesc = 'Donation to Truecare Newday Center'): array
    {
        $tokenResult = $this->getAccessToken();

        if (! $tokenResult['success']) {
            return $tokenResult;
        }

        $accessToken = $tokenResult['access_token'];
        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        $payload = [
            'BusinessShortCode' => $this->shortcode,
            'Password' => $password,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => (int) $amount,
            'PartyA' => $phone,
            'PartyB' => $this->shortcode,
            'PhoneNumber' => $phone,
            'CallBackURL' => $this->callbackUrl,
            'AccountReference' => $accountReference,
            'TransactionDesc' => $transactionDesc,
        ];

        try {
            $response = Http::withToken($accessToken)
                ->timeout(30)
                ->post("{$this->baseUrl}/mpesa/stkpush/v1/request", $payload);

            $body = $response->json();

            if ($response->successful() && isset($body['ResponseCode']) && $body['ResponseCode'] === '0') {
                return [
                    'success' => true,
                    'message' => 'STK push sent. Check your phone and enter M-Pesa PIN to pay to ' . $this->paybill . '/' . $this->account,
                    'merchant_request_id' => $body['MerchantRequestID'] ?? null,
                    'checkout_request_id' => $body['CheckoutRequestID'] ?? null,
                    'customer_message' => $body['CustomerMessage'] ?? null,
                    'response_code' => $body['ResponseCode'] ?? null,
                    'raw' => $body,
                ];
            }

            if ($response->status() === 401) {
                $this->forgetToken();
            }

            Log::warning('Mpesa stkPush business error', [
                'status' => $response->status(),
                'body' => $body,
            ]);

            return [
                'success' => false,
                'message' => $body['errorMessage'] ?? 'M-Pesa STK push was not accepted.',
                'status_code' => $response->status(),
                'response_code' => $body['ResponseCode'] ?? null,
                'raw' => $body,
            ];
        } catch (Exception $e) {
            Log::error('Mpesa stkPush exception', ['exception' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ];
        }
    }

    public function queryStatus(string $checkoutRequestId): array
    {
        $tokenResult = $this->getAccessToken();

        if (! $tokenResult['success']) {
            return $tokenResult;
        }

        $timestamp = date('YmdHis');
        $password = base64_encode($this->shortcode . $this->passkey . $timestamp);

        try {
            $response = Http::withToken($tokenResult['access_token'])
                ->timeout(30)
                ->post("{$this->baseUrl}/mpesa/stkpushquery/v1/query", [
                    'BusinessShortCode' => $this->shortcode,
                    'Password' => $password,
                    'Timestamp' => $timestamp,
                    'CheckoutRequestID' => $checkoutRequestId,
                ]);

            $body = $response->json();

            return [
                'success' => $response->successful(),
                'status_code' => $response->status(),
                'result_code' => $body['ResultCode'] ?? null,
                'result_desc' => $body['ResultDesc'] ?? null,
                'raw' => $body,
            ];
        } catch (Exception $e) {
            Log::error('Mpesa queryStatus exception', ['exception' => $e->getMessage()]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ];
        }
    }
}