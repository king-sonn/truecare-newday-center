<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Services\MpesaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * MpesaController - handles STK Push donation flow for Truecare Newday Center.
 *
 * @package App\Http\Controllers\Front
 */
class MpesaController extends Controller
{
    public function __construct(protected MpesaService $mpesa)
    {
    }

    public function stkPush(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'regex:/^254[71][0-9]{8}$/'],
            'amount' => ['required', 'numeric', 'min:1', 'max:150000'],
        ]);

        $phone = mpesa_normalise_phone($validated['phone']);
        $amount = (int) $validated['amount'];

        if (! preg_match('/^254[71][0-9]{8}$/', $phone)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number. Use format 2547XXXXXXXX.',
            ], 422);
        }

        $donation = Donation::create([
            'phone' => $phone,
            'amount' => $amount,
            'business_no' => '247247',
            'account_no' => '958526',
            'account_name' => 'Truecare new day center',
            'status' => Donation::STATUS_PENDING,
        ]);

        $result = $this->mpesa->stkPush(
            phone: $phone,
            amount: $amount,
            accountReference: '958526',
            transactionDesc: 'Donation to Truecare Newday Center'
        );

        if (! $result['success']) {
            $donation->update([
                'status' => Donation::STATUS_FAILED,
                'result_code' => $result['response_code'] ?? null,
                'result_desc' => $result['message'] ?? 'STK push failed',
            ]);

            \Log::channel('mpesa')->error('STK push failed', [
                'donation_id' => $donation->id,
                'phone' => $phone,
                'amount' => $amount,
                'result' => $result,
            ]);

            return response()->json([
                'success' => false,
                'message' => $result['message'] ?? 'STK push failed. Please try again.',
                'error' => $result['message'] ?? null,
            ], 402);
        }

        $donation->update([
            'checkout_request_id' => $result['checkout_request_id'] ?? null,
            'merchant_request_id' => $result['merchant_request_id'] ?? null,
        ]);

        \Log::channel('mpesa')->info('STK push initiated', [
            'donation_id' => $donation->id,
            'phone' => $phone,
            'amount' => $amount,
            'checkout_request_id' => $result['checkout_request_id'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => $result['message'] ?? 'Check your phone and enter M-Pesa PIN to pay to 247247/958526',
            'checkout_id' => $result['checkout_request_id'] ?? null,
            'merchant_request_id' => $result['merchant_request_id'] ?? null,
            'donation_id' => $donation->id,
        ]);
    }

    /**
     * Poll donation status by checkout request ID.
     */
    public function status(string $checkoutId): JsonResponse
    {
        $donation = Donation::where('checkout_request_id', $checkoutId)->first();

        if (! $donation) {
            return response()->json([
                'success' => false,
                'message' => 'Donation not found.',
            ], 404);
        }

        // Query Daraja for latest status
        $query = $this->mpesa->queryStatus($checkoutId);

        \Log::channel('mpesa')->info('Donation status poll', [
            'donation_id' => $donation->id,
            'checkout_request_id' => $checkoutId,
            'query' => $query,
        ]);

        $current = $donation->status;
        $resultCode = $query['result_code'] ?? null;
        $resultDesc = $query['result_desc'] ?? null;

        // Daraja ResultCode '0' = success
        if ($query['success'] && $resultCode === '0' && $current !== Donation::STATUS_COMPLETED) {
            $donation->update([
                'status' => Donation::STATUS_COMPLETED,
                'mpesa_receipt' => $query['raw']['MpesaReceiptNumber'] ?? null,
                'result_code' => $resultCode,
                'result_desc' => $resultDesc ?? 'Transaction completed',
            ]);
            $current = Donation::STATUS_COMPLETED;
        } elseif ($query['success'] && $resultCode !== '0' && $resultCode !== null && $current !== Donation::STATUS_FAILED) {
            $donation->update([
                'status' => Donation::STATUS_FAILED,
                'result_code' => $resultCode,
                'result_desc' => $resultDesc ?? 'Transaction failed',
            ]);
            $current = Donation::STATUS_FAILED;
        }

        return response()->json([
            'success' => true,
            'checkout_id' => $checkoutId,
            'status' => $current,
            'amount' => $donation->amount,
            'phone' => $donation->phone,
            'business_no' => $donation->business_no,
            'account_no' => $donation->account_no,
            'account_name' => $donation->account_name,
            'mpesa_receipt' => $donation->mpesa_receipt,
            'result_code' => $donation->result_code,
            'result_desc' => $donation->result_desc,
            'created_at' => $donation->created_at,
        ]);
    }

    /**
     * M-Pesa callback endpoint (called by Safaricom Daraja).
     * Must be publicly reachable (e.g. via ngrok).
     */
    public function callback(Request $request): JsonResponse
    {
        $payload = $request->all();

        \Log::channel('mpesa')->info('M-Pesa callback received', $payload);

        $body = $payload['Body']['stkCallback'] ?? $payload;
        $merchantRequestId = $body['MerchantRequestID'] ?? null;
        $checkoutRequestId = $body['CheckoutRequestID'] ?? null;
        $resultCode = $body['ResultCode'] ?? null;
        $resultDesc = $body['ResultDesc'] ?? null;

        $donation = Donation::where('checkout_request_id', $checkoutRequestId)
            ->orWhere('merchant_request_id', $merchantRequestId)
            ->first();

        if ($donation) {
            $update = [
                'result_code' => (string) $resultCode,
                'result_desc' => $resultDesc,
            ];

            if ($resultCode == '0') {
                $update['status'] = Donation::STATUS_COMPLETED;
                $metadata = $body['CallbackMetadata']['Item'] ?? [];
                foreach ($metadata as $item) {
                    if ($item['Name'] === 'MpesaReceiptNumber') {
                        $update['mpesa_receipt'] = $item['Value'];
                    }
                }
            } else {
                $update['status'] = Donation::STATUS_FAILED;
            }

            $donation->update($update);
        }

        // Always acknowledge to Daraja
        return response()->json(['ResultCode' => 0, 'ResultDesc' => 'Accepted']);
    }
}