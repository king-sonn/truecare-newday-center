<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Services\SettingsRepository;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\User;
use App\Services\FileUploadService;
use App\Services\PaymentGatewayService;
use App\Services\NotificationPreferenceService;
use App\Services\RevenueShareService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Yabacon\Paystack;
use App\Mail\StudentCourseEnrollmentMail;
use App\Mail\InstructorCourseEnrollmentMail;
use Illuminate\Support\Facades\Mail;

class CourseEnrollmentController extends Controller
{
    protected $fileUploadService;
    protected PaymentGatewayService $paymentGatewayService;

    public function __construct(
        FileUploadService $fileUploadService,
        PaymentGatewayService $paymentGatewayService,
        protected SettingsRepository $settingsRepository,
        private NotificationPreferenceService $notificationPreferenceService,
        private RevenueShareService $revenueShareService
    ) {
        $this->fileUploadService = $fileUploadService;
        $this->paymentGatewayService = $paymentGatewayService;
        $this->settingsRepository = $settingsRepository;
    }

    /**
     * Process checkout
     */
    public function enroll(CheckoutRequest $request, $id)
    {
        $course = Course::findOrFail($id);
        $user = Auth::user();
        /** @var User $user */

        if ($course->status !== 'published' || $course->visibility !== 'public') {
            return response()->json([
                'success' => false,
                'message' => 'This course is not available for enrollment.'
            ], 400);
        }

        $existingEnrollment = Enrollment::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->first();

        if ($existingEnrollment) {
            if (
                $existingEnrollment->status === Enrollment::STATUS_APPROVED ||
                $existingEnrollment->status === Enrollment::STATUS_COMPLETED
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are already enrolled in this course.'
                ], 400);
            }

            // If it's pending, we'll check if it's an offline payment.
            // Offline payments should be kept for admin review.
            $payment = $existingEnrollment->payment;
            if ($payment && $payment->payment_method === Payment::PAYMENT_METHOD_OFFLINE) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have a pending offline payment request. Please wait for admin approval.'
                ], 400);
            }

            // If it's pending (and not offline), we'll delete it to allow a fresh enrollment attempt.
            // This is especially useful if a previous payment flow failed or was interrupted.
            $existingEnrollment->delete();
        }

        $price = $this->calculatePrice($course);
        $paymentMethod = strtolower($request->payment_method);

        if ($price > 0 && !$this->paymentGatewayService->isEnabled($paymentMethod)) {
            return response()->json([
                'success' => false,
                'message' => 'Selected payment method is not available.'
            ], 400);
        }

        DB::beginTransaction();

        try {
            if ($price > 0 && $paymentMethod === Payment::PAYMENT_METHOD_RAZORPAY) {
                $razorpayOrder = $this->createRazorpayOrder($price, $course->id, $user->id);

                $enrollment = Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'status' => Enrollment::STATUS_PENDING,
                ]);

                $payment = Payment::create([
                    'enrollment_id' => $enrollment->id,
                    'payment_method' => Payment::PAYMENT_METHOD_RAZORPAY,
                    'amount' => $price,
                    'status' => Payment::STATUS_PENDING,
                    'razorpay_order_id' => $razorpayOrder['id'],
                ]);

                $enrollment->update(['payment_id' => $payment->id]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'order_id' => $razorpayOrder['id'],
                    'amount' => $razorpayOrder['amount'],
                    'currency' => $razorpayOrder['currency'],
                    'key' => $this->credentials('razorpay')['key'] ?? '',
                    'name' => config('app.name'),
                    'description' => 'Course Enrollment: ' . $course->title,
                    'prefill' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'contact' => $user->phone ?? '',
                    ],
                    'enrollment_id' => $enrollment->id,
                ]);
            } elseif ($price > 0 && $paymentMethod === Payment::PAYMENT_METHOD_SSLCOMMERZ) {
                $credentials = $this->credentials('sslcommerz');
                $tranId = 'SSLCZ_' . uniqid() . '_' . $user->id;

                $enrollment = Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'status' => Enrollment::STATUS_PENDING,
                ]);

                $payment = Payment::create([
                    'enrollment_id' => $enrollment->id,
                    'payment_method' => Payment::PAYMENT_METHOD_SSLCOMMERZ,
                    'amount' => $price,
                    'status' => Payment::STATUS_PENDING,
                    'sslcommerz_tran_id' => $tranId,
                ]);

                $enrollment->update(['payment_id' => $payment->id]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'tran_id' => $tranId,
                    'amount' => $price,
                    'store_id' => $credentials['store_id'] ?? '',
                    'store_password' => $credentials['store_password'] ?? '',
                    'mode' => $credentials['mode'] ?? 'sandbox',
                    'enrollment_id' => $enrollment->id,
                    'prefill' => [
                        'name' => $user->name,
                        'email' => $user->email,
                        'contact' => $user->phone ?? '',
                    ],
                ]);
            } elseif ($price > 0 && $paymentMethod === Payment::PAYMENT_METHOD_STRIPE) {
                $credentials = $this->credentials('stripe');
                $stripeSecret = $credentials['secret'] ?? '';

                if (empty($stripeSecret)) {
                    throw new \Exception('Stripe credentials are not configured.');
                }

                Stripe::setApiKey($stripeSecret);

                $paymentIntent = PaymentIntent::create([
                    'amount' => (int) ($price * 100), // convert to cents
                    'currency' => strtolower($this->settingsRepository->get('platform.general.default_currency', 'INR')),
                    'payment_method_types' => ['card'],
                    'metadata' => [
                        'course_id' => $course->id,
                        'user_id' => $user->id,
                    ],
                ]);

                $enrollment = Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'status' => Enrollment::STATUS_PENDING,
                ]);

                $payment = Payment::create([
                    'enrollment_id' => $enrollment->id,
                    'payment_method' => Payment::PAYMENT_METHOD_STRIPE,
                    'amount' => $price,
                    'status' => Payment::STATUS_PENDING,
                    'stripe_payment_intent_id' => $paymentIntent->id,
                    'stripe_client_secret' => $paymentIntent->client_secret,
                ]);

                $enrollment->update(['payment_id' => $payment->id]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'payment_intent_id' => $paymentIntent->id,
                    'client_secret' => $paymentIntent->client_secret,
                    'publishable_key' => $credentials['key'] ?? '',
                    'enrollment_id' => $enrollment->id,
                    'amount' => $price,
                ]);
            } elseif ($price > 0 && $paymentMethod === Payment::PAYMENT_METHOD_PAYSTACK) {
                $credentials = $this->credentials('paystack');
                $paystackSecret = $credentials['secret_key'] ?? '';
                $paystackPublic = $credentials['public_key'] ?? '';

                if (empty($paystackSecret)) {
                    throw new \Exception('Paystack credentials are not configured.');
                }

                $paystack = new Paystack($paystackSecret);

                // Generate unique reference
                $reference = 'PAY_' . time() . '_' . $user->id;
                $amountInKobo = (int) ($price * 100);

                try {
                    $transaction = $paystack->transaction->initialize([
                        'email' => $user->email,
                        'amount' => $amountInKobo,
                        'reference' => $reference,
                        'callback_url' => config('app.url') . '/payment/paystack/callback',
                        'metadata' => [
                            'course_id' => $course->id,
                            'user_id' => $user->id,
                        ],
                    ]);

                    if (!$transaction->status || !$transaction->data) {
                        throw new \Exception('Failed to initialize Paystack transaction.');
                    }

                    $enrollment = Enrollment::create([
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                        'status' => Enrollment::STATUS_PENDING,
                    ]);

                    $payment = Payment::create([
                        'enrollment_id' => $enrollment->id,
                        'payment_method' => Payment::PAYMENT_METHOD_PAYSTACK,
                        'amount' => $price,
                        'status' => Payment::STATUS_PENDING,
                        'paystack_reference' => $reference,
                        'paystack_access_code' => $transaction->data->access_code,
                    ]);

                    $enrollment->update(['payment_id' => $payment->id]);

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'authorization_url' => $transaction->data->authorization_url,
                        'access_code' => $transaction->data->access_code,
                        'reference' => $reference,
                        'public_key' => $paystackPublic,
                        'enrollment_id' => $enrollment->id,
                        'amount' => $price,
                    ]);
                } catch (\Exception $e) {
                    throw new \Exception('Failed to create Paystack transaction: ' . $e->getMessage());
                }
            } elseif ($price > 0 && $paymentMethod === Payment::PAYMENT_METHOD_MOLLIE) {
                try {
                    $mollieData = $this->createMolliePayment($price, $course, $user);

                    $enrollment = Enrollment::create([
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                        'status' => Enrollment::STATUS_PENDING,
                    ]);

                    $payment = Payment::create([
                        'enrollment_id' => $enrollment->id,
                        'payment_method' => Payment::PAYMENT_METHOD_MOLLIE,
                        'amount' => $price,
                        'status' => Payment::STATUS_PENDING,
                        'mollie_payment_id' => $mollieData['payment_id'],
                    ]);

                    $enrollment->update(['payment_id' => $payment->id]);

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'checkout_url' => $mollieData['checkout_url'],
                        'payment_id' => $mollieData['payment_id'],
                        'enrollment_id' => $enrollment->id,
                        'amount' => $price,
                    ]);
                } catch (\Exception $e) {
                    throw new \Exception('Failed to create Mollie payment: ' . $e->getMessage());
                }
            } elseif ($price > 0 && $paymentMethod === Payment::PAYMENT_METHOD_PAYPAL) {
                try {
                    $paypalData = $this->createPaypalOrder($price, $course, $user);

                    $enrollment = Enrollment::create([
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                        'status' => Enrollment::STATUS_PENDING,
                    ]);

                    $payment = Payment::create([
                        'enrollment_id' => $enrollment->id,
                        'payment_method' => Payment::PAYMENT_METHOD_PAYPAL,
                        'amount' => $price,
                        'status' => Payment::STATUS_PENDING,
                        'paypal_order_id' => $paypalData['order_id'],
                    ]);

                    $enrollment->update(['payment_id' => $payment->id]);

                    DB::commit();

                    $credentials = $this->paymentGatewayService->credentialsFor(Payment::PAYMENT_METHOD_PAYPAL);
                    return response()->json([
                        'success' => true,
                        'order_id' => $paypalData['order_id'],
                        'approve_url' => $paypalData['approve_url'],
                        'enrollment_id' => $enrollment->id,
                        'amount' => $price,
                    ]);
                } catch (\Exception $e) {
                    throw new \Exception('Failed to create PayPal order: ' . $e->getMessage());
                }
            } elseif ($price > 0 && $paymentMethod === Payment::PAYMENT_METHOD_OFFLINE) {
                $receiptPath = null;
                if ($request->hasFile('receipt_file')) {
                    $receiptPath = $this->fileUploadService->uploadReceipt($request->file('receipt_file'));
                }

                $enrollment = Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'status' => Enrollment::STATUS_PENDING,
                ]);

                $payment = Payment::create([
                    'enrollment_id' => $enrollment->id,
                    'payment_method' => Payment::PAYMENT_METHOD_OFFLINE,
                    'amount' => $price,
                    'status' => Payment::STATUS_PENDING,
                    'receipt_file' => $receiptPath,
                    'transaction_id' => $request->transaction_id,
                ]);

                $enrollment->update(['payment_id' => $payment->id]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Your enrollment request has been submitted. Please wait for admin approval.',
                    'enrollment_id' => $enrollment->id,
                ]);
            } elseif ($price == 0) {
                // Handle free course
                $enrollment = Enrollment::create([
                    'user_id' => $user->id,
                    'course_id' => $course->id,
                    'status' => Enrollment::STATUS_APPROVED,
                    'enrolled_at' => now(),
                ]);

                DB::commit();

                // For a real app, you might want to call these to notify
                $this->sendEnrollmentNotifications($enrollment, $course, $user);

                return response()->json([
                    'success' => true,
                    'message' => 'You have successfully enrolled in this course!',
                    'enrollment_id' => $enrollment->id,
                ]);
            } else {
                // If we get here, it means price > 0 but no payment method matched
                throw new \Exception('Invalid payment method selected for paid course.');
            }
        } catch (\Exception $e) {
            Log::error('Enrollment failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: 'An error occurred during enrollment. Please try again.',
            ], 500);
        }
    }

    public function razorpayVerify(Request $request)
    {
        $request->validate([
            'razorpay_order_id' => 'required|string',
            'razorpay_payment_id' => 'required|string',
            'razorpay_signature' => 'required|string',
            'enrollment_id' => 'required|exists:enrollments,id',
        ]);

        $enrollment = Enrollment::with('payment')->findOrFail($request->enrollment_id);

        if (!$enrollment->payment || $enrollment->payment->razorpay_order_id !== $request->razorpay_order_id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment details.'
            ], 400);
        }

        $isValid = $this->verifyRazorpaySignature(
            $request->razorpay_order_id,
            $request->razorpay_payment_id,
            $request->razorpay_signature
        );

        DB::beginTransaction();

        try {
            if ($isValid) {
                $enrollment->payment->update([
                    'razorpay_payment_id' => $request->razorpay_payment_id,
                    'razorpay_signature' => $request->razorpay_signature,
                    'status' => Payment::STATUS_COMPLETED,
                ]);

                $enrollment->update([
                    'status' => Enrollment::STATUS_APPROVED,
                    'enrolled_at' => now(),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful! You have been enrolled in the course.',
                    'enrollment_id' => $enrollment->id,
                ]);
            } else {
                $enrollment->payment->update([
                    'status' => Payment::STATUS_FAILED,
                ]);

                DB::commit();

                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed.'
                ], 400);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred processing your payment.'
            ], 500);
        }
    }
    public function sslcommerzVerify(Request $request)
    {
        $request->validate([
            'tran_id' => 'required|string',
            'val_id' => 'required|string',
            'amount' => 'required',
            'enrollment_id' => 'required|exists:enrollments,id',
        ]);

        $enrollment = Enrollment::with('payment')->findOrFail($request->enrollment_id);

        if (!$enrollment->payment || $enrollment->payment->sslcommerz_tran_id !== $request->tran_id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment details.'
            ], 400);
        }

        $credentials = $this->credentials('sslcommerz');
        $storeId = $credentials['store_id'] ?? '';
        $storePassword = $credentials['store_password'] ?? '';
        $mode = $credentials['mode'] ?? 'sandbox';

        $validationUrl = ($mode === 'live')
            ? "https://securepay.sslcommerz.com/validator/api/validationserverAPI.php"
            : "https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php";

        $queryParams = [
            'val_id' => $request->val_id,
            'store_id' => $storeId,
            'store_passwd' => $storePassword,
            'format' => 'json'
        ];

        $url = $validationUrl . "?" . http_build_query($queryParams);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For sandbox sometimes needed
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // 10 seconds timeout
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // 30 seconds total
        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment with SSLCommerz. Connection error.'
            ], 500);
        }

        $result = json_decode($response, true);

        $status = strtoupper($result['status'] ?? '');

        if ($status === 'VALID' || $status === 'SUCCESS' || $status === 'VALIDATED') {
            DB::beginTransaction();
            try {
                $enrollment->payment->update([
                    'sslcommerz_val_id' => $request->val_id,
                    'status' => Payment::STATUS_COMPLETED,
                ]);

                $enrollment->update([
                    'status' => Enrollment::STATUS_APPROVED,
                    'enrolled_at' => now(),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful! You have been enrolled in the course.',
                    'enrollment_id' => $enrollment->id,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred processing your payment.'
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment validation failed: ' . ($result['error'] ?? 'Unknown error'),
            'details' => $result
        ], 400);
    }

    public function stripeVerify(Request $request)
    {
        $request->validate([
            'payment_intent_id' => 'required|string',
            'enrollment_id' => 'required|exists:enrollments,id',
        ]);

        $enrollment = Enrollment::with('payment')->findOrFail($request->enrollment_id);

        if (!$enrollment->payment || $enrollment->payment->stripe_payment_intent_id !== $request->payment_intent_id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment details.'
            ], 400);
        }

        $credentials = $this->credentials('stripe');
        $stripeSecret = $credentials['secret'] ?? '';

        if (empty($stripeSecret)) {
            return response()->json([
                'success' => false,
                'message' => 'Stripe credentials are not configured.'
            ], 500);
        }

        Stripe::setApiKey($stripeSecret);

        try {
            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);

            if ($paymentIntent->status === 'succeeded') {
                DB::beginTransaction();
                try {
                    $enrollment->payment->update([
                        'status' => Payment::STATUS_COMPLETED,
                    ]);

                    $enrollment->update([
                        'status' => Enrollment::STATUS_APPROVED,
                        'enrolled_at' => now(),
                    ]);

                    DB::commit();

                    return response()->json([
                        'success' => true,
                        'message' => 'Payment successful! You have been enrolled in the course.',
                        'enrollment_id' => $enrollment->id,
                    ]);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'An error occurred processing your payment.'
                    ], 500);
                }
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Stripe payment not succeeded. Status: ' . $paymentIntent->status,
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Stripe payment: ' . $e->getMessage()
            ], 500);
        }
    }

    public function paystackVerify(Request $request)
    {
        $request->validate([
            'reference' => 'required|string',
            'enrollment_id' => 'required|exists:enrollments,id',
        ]);

        $enrollment = Enrollment::with('payment')->findOrFail($request->enrollment_id);

        if (!$enrollment->payment || $enrollment->payment->paystack_reference !== $request->reference) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment details.'
            ], 400);
        }

        $credentials = $this->credentials('paystack');
        $paystackSecret = $credentials['secret_key'] ?? '';

        if (empty($paystackSecret)) {
            return response()->json([
                'success' => false,
                'message' => 'Paystack credentials are not configured.'
            ], 500);
        }

        // Verify transaction with Paystack
        $url = "https://api.paystack.co/transaction/verify/{$request->reference}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: Bearer {$paystackSecret}",
            "Content-Type: application/json",
        ]);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to verify payment with Paystack.'
            ], 500);
        }

        $result = json_decode($response, true);

        if (isset($result['status']) && $result['status'] && $result['data']['status'] === 'success') {
            DB::beginTransaction();
            try {
                $enrollment->payment->update([
                    'status' => Payment::STATUS_COMPLETED,
                ]);

                $enrollment->update([
                    'status' => Enrollment::STATUS_APPROVED,
                    'enrolled_at' => now(),
                ]);

                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful! You have been enrolled in the course.',
                    'enrollment_id' => $enrollment->id,
                ]);
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'An error occurred processing your payment.'
                ], 500);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Payment verification failed: ' . ($result['message'] ?? 'Unknown error'),
        ], 400);
    }

    private function calculatePrice(Course $course): float
    {
        if ($course->pricing_model === 'free') {
            return 0;
        }

        return $course->sale_price ? (float) $course->sale_price : (float) $course->regular_price;
    }

    private function createRazorpayOrder(float $amount, int $courseId, int $userId): array
    {
        $credentials = $this->credentials('razorpay');
        $apiKey = $credentials['key'] ?? null;
        $apiSecret = $credentials['secret'] ?? null;

        if (!$apiKey || !$apiSecret) {
            throw new \RuntimeException('Razorpay credentials are not configured.');
        }

        $amountInPaise = (int) ($amount * 100);

        $currency = 'INR';

        $ch = curl_init('https://api.razorpay.com/v1/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, $apiKey . ':' . $apiSecret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            'amount' => $amountInPaise,
            'currency' => $currency,
            'receipt' => 'course_' . $courseId . '_user_' . $userId . '_' . time(),
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception('Failed to create Razorpay order: ' . $response);
        }

        return json_decode($response, true);
    }

    private function verifyRazorpaySignature(string $orderId, string $paymentId, string $signature): bool
    {
        $apiSecret = $this->credentials('razorpay')['secret'] ?? null;
        if (!$apiSecret)
            return false;

        $payload = $orderId . '|' . $paymentId;
        $expectedSignature = hash_hmac('sha256', $payload, $apiSecret);
        return hash_equals($expectedSignature, $signature);
    }

    private function credentials(string $identifier): array
    {
        return $this->paymentGatewayService->credentialsFor($identifier);
    }

    private function sendEnrollmentNotifications(Enrollment $enrollment, ?Course $course = null, ?User $student = null): void
    {
        $enrollment->loadMissing('course.instructor.notificationSettings', 'user.notificationSettings');

        $course = $course ?? $enrollment->course;
        $student = $student ?? $enrollment->user;
        $instructor = $course?->instructor;
        $enrolledAt = $enrollment->enrolled_at ?? now();

        if ($student && $student->email && $this->notificationPreferenceService->prefers($student, 'email_notifications')) {
            Mail::to($student->email)->send(new StudentCourseEnrollmentMail($course, $student, $enrolledAt));
        }

        if ($instructor && $instructor->email && $student && $this->notificationPreferenceService->prefers($instructor, 'course_updates')) {
            Mail::to($instructor->email)->send(new InstructorCourseEnrollmentMail($course, $student, $enrolledAt));
        }
    }

    private function processRevenueShare(Enrollment $enrollment): void
    {
        if (!$enrollment->payment_id) {
            return;
        }

        $payment = Payment::with('enrollment.course.instructor')->find($enrollment->payment_id);

        if (!$payment || $payment->status !== Payment::STATUS_COMPLETED) {
            return;
        }

        $this->revenueShareService->process($payment);
    }

    /**
     * Create Mollie payment
     */
    private function createMolliePayment(float $amount, Course $course, $user): array
    {
        $apiKey = $this->credentials('mollie')['api_key'] ?? null;

        if (!$apiKey) {
            throw new \RuntimeException('Mollie API key is not configured.');
        }

        $baseUrl = 'https://api.mollie.com/v2/payments';
        $amountValue = number_format($amount, 2, '.', '');

        // Mollie requires currency. Default to EUR if not set.
        $currency = strtoupper($this->settingsRepository->get('platform.general.default_currency', 'EUR'));

        // If currency is not USD or EUR, fallback to EUR
        if (!in_array($currency, ['USD', 'EUR', 'GBP'])) {
            $currency = 'EUR';
        }

        $paymentData = [
            'amount' => [
                'currency' => $currency,
                'value' => $amountValue,
            ],
            'description' => 'Course Enrollment: ' . $course->title,
            'redirectUrl' => config('app.url') . '/payment/mollie/callback',
            'metadata' => [
                'course_id' => $course->id,
                'user_id' => $user->id,
                'course_title' => $course->title,
            ],
        ];

        $ch = curl_init($baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paymentData));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey,
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new \Exception('Mollie cURL error: ' . $curlError);
        }

        if ($httpCode !== 201) {
            throw new \Exception('Failed to create Mollie payment. HTTP Code: ' . $httpCode . ' Response: ' . $response);
        }

        $responseData = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Invalid JSON response from Mollie API.');
        }

        if (!isset($responseData['id']) || !isset($responseData['_links']['checkout']['href'])) {
            $errorMsg = $responseData['detail'] ?? $responseData['title'] ?? 'Unknown error';
            throw new \Exception('Mollie error: ' . $errorMsg);
        }

        return [
            'payment_id' => $responseData['id'],
            'checkout_url' => $responseData['_links']['checkout']['href'],
        ];
    }

    /**
     * Verify Mollie payment
     */
    public function mollieVerify(Request $request)
    {
        $request->validate([
            'payment_id' => 'required|string',
        ]);

        $paymentId = $request->payment_id;
        $apiKey = $this->credentials('mollie')['api_key'] ?? null;

        if (!$apiKey) {
            return response()->json(['success' => false, 'message' => 'Mollie API key not configured.'], 500);
        }

        $baseUrl = 'https://api.mollie.com/v2/payments/' . $paymentId;

        $ch = curl_init($baseUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $apiKey,
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            return response()->json(['success' => false, 'message' => 'Failed to verify payment with Mollie.'], 400);
        }

        $paymentData = json_decode($response, true);

        if (($paymentData['status'] ?? '') === 'paid') {
            $payment = Payment::where('mollie_payment_id', $paymentId)->first();

            if ($payment && $payment->status === Payment::STATUS_PENDING) {
                DB::beginTransaction();
                try {
                    $payment->update(['status' => Payment::STATUS_COMPLETED]);
                    $payment->enrollment->update([
                        'status' => Enrollment::STATUS_APPROVED,
                        'enrolled_at' => now(),
                    ]);

                    $this->processRevenueShare($payment->enrollment);
                    $this->sendEnrollmentNotifications($payment->enrollment);

                    DB::commit();
                    return response()->json(['success' => true, 'message' => 'Payment verified successfully.']);
                } catch (\Exception $e) {
                    DB::rollBack();
                    return response()->json(['success' => false, 'message' => 'Error updating enrollment: ' . $e->getMessage()], 500);
                }
            }

            return response()->json(['success' => true, 'message' => 'Payment already processed or not found.']);
        }

        return response()->json(['success' => false, 'message' => 'Payment status: ' . ($paymentData['status'] ?? 'unknown')]);
    }

    public function paypalVerify(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'enrollment_id' => 'required|exists:enrollments,id',
        ]);

        $enrollment = Enrollment::with('payment')->findOrFail($request->enrollment_id);

        if (!$enrollment->payment || $enrollment->payment->paypal_order_id !== $request->order_id) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid payment details.'
            ], 400);
        }

        $accessToken = $this->getPaypalAccessToken();
        $credentials = $this->paymentGatewayService->credentialsFor('paypal');
        $mode = $credentials['mode'] ?? 'sandbox';
        $baseUrl = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        DB::beginTransaction();

        try {
            // Capture the order
            $ch = curl_init($baseUrl . '/v2/checkout/orders/' . $request->order_id . '/capture');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $accessToken,
            ]);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode !== 201) {
                $enrollment->payment->update([
                    'status' => Payment::STATUS_FAILED,
                ]);

                DB::commit();

                return response()->json([
                    'success' => false,
                    'message' => 'Payment capture failed.'
                ], 400);
            }

            $captureData = json_decode($response, true);

            if (!isset($captureData['status']) || $captureData['status'] !== 'COMPLETED') {
                $enrollment->payment->update([
                    'status' => Payment::STATUS_FAILED,
                ]);

                DB::commit();

                return response()->json([
                    'success' => false,
                    'message' => 'Payment was not successful.'
                ], 400);
            }

            $payerId = $captureData['payer']['payer_id'] ?? null;
            $paymentId = $captureData['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;

            $enrollment->payment->update([
                'paypal_payer_id' => $payerId,
                'paypal_payment_id' => $paymentId,
                'status' => Payment::STATUS_COMPLETED,
            ]);

            $enrollment->update([
                'status' => Enrollment::STATUS_APPROVED,
                'enrolled_at' => now(),
            ]);

            $this->processRevenueShare($enrollment);

            DB::commit();

            $this->sendEnrollmentNotifications($enrollment, $enrollment->course, $enrollment->user);

            return response()->json([
                'success' => true,
                'message' => 'Payment successful! You have been enrolled in the course.',
                'enrollment_id' => $enrollment->id,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'An error occurred processing your payment: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getPaypalAccessToken(): string
    {
        $credentials = $this->paymentGatewayService->credentialsFor('paypal');
        $clientId = $credentials['client_id'] ?? null;
        $clientSecret = $credentials['client_secret'] ?? null;
        $mode = $credentials['mode'] ?? 'sandbox';

        if (!$clientId || !$clientSecret) {
            throw new \RuntimeException('PayPal credentials are not configured.');
        }

        $baseUrl = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $ch = curl_init($baseUrl . '/v1/oauth2/token');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, $clientId . ':' . $clientSecret);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json',
            'Accept-Language: en_US',
        ]);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            throw new \Exception('Failed to get PayPal access token.');
        }

        $tokenData = json_decode($response, true);

        if (!isset($tokenData['access_token'])) {
            throw new \Exception('Invalid response from PayPal token endpoint.');
        }

        return $tokenData['access_token'];
    }

    private function createPaypalOrder(float $amount, Course $course, $user): array
    {
        $accessToken = $this->getPaypalAccessToken();
        $credentials = $this->paymentGatewayService->credentialsFor('paypal');
        $mode = $credentials['mode'] ?? 'sandbox';
        $baseUrl = $mode === 'live'
            ? 'https://api-m.paypal.com'
            : 'https://api-m.sandbox.paypal.com';

        $orderData = [
            'intent' => 'CAPTURE',
            'purchase_units' => [
                [
                    'reference_id' => 'course_' . $course->id . '_user_' . $user->id . '_' . time(),
                    'description' => 'Course Enrollment: ' . $course->title,
                    'amount' => [
                        'currency_code' => 'USD',
                        'value' => number_format($amount, 2, '.', ''),
                    ],
                ]
            ],
            'application_context' => [
                'brand_name' => config('app.name'),
                'landing_page' => 'BILLING',
                'user_action' => 'PAY_NOW',
                'return_url' => config('app.url') . '/payment/paypal/callback?success=true',
                'cancel_url' => config('app.url') . '/payment/paypal/callback?success=false',
            ],
        ];

        $ch = curl_init($baseUrl . '/v2/checkout/orders');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $accessToken,
            'PayPal-Request-Id: ' . uniqid(),
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($orderData));
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 201) {
            throw new \Exception('Failed to create PayPal order.');
        }

        $orderResponse = json_decode($response, true);

        if (!isset($orderResponse['id']) || !isset($orderResponse['links'])) {
            throw new \Exception('Invalid response from PayPal.');
        }

        $approveUrl = null;
        foreach ($orderResponse['links'] as $link) {
            if ($link['rel'] === 'approve') {
                $approveUrl = $link['href'];
                break;
            }
        }

        if (!$approveUrl) {
            throw new \Exception('Approve URL not found in PayPal response.');
        }

        return [
            'order_id' => $orderResponse['id'],
            'approve_url' => $approveUrl,
        ];
    }
}
