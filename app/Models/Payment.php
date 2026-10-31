<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Payment Model
 *
 * This model represents a payment for a course.
 *
 * @package App\Models
 */
class Payment extends Model
{
    protected $fillable = [
        'enrollment_id',
        'payment_method',
        'amount',
        'platform_commission',
        'instructor_earning',
        'commission_processed',
        'status',
        'razorpay_order_id',
        'razorpay_payment_id',
        'razorpay_signature',
        'stripe_payment_intent_id',
        'stripe_client_secret',
        'paystack_reference',
        'paystack_access_code',
        'flutterwave_transaction_id',
        'flutterwave_tx_ref',
        'paypal_order_id',
        'paypal_payer_id',
        'paypal_payment_id',
        'sslcommerz_tran_id',
        'sslcommerz_session_key',
        'sslcommerz_val_id',
        'mollie_payment_id',
        'receipt_file',
        'transaction_id',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'platform_commission' => 'decimal:2',
        'instructor_earning' => 'decimal:2',
        'commission_processed' => 'boolean',
    ];

    const PAYMENT_METHOD_RAZORPAY = 'razorpay';
    const PAYMENT_METHOD_OFFLINE = 'offline';
    const PAYMENT_METHOD_STRIPE = 'stripe';
    const PAYMENT_METHOD_PAYSTACK = 'paystack';
    const PAYMENT_METHOD_FLUTTERWAVE = 'flutterwave';
    const PAYMENT_METHOD_PAYPAL = 'paypal';
    const PAYMENT_METHOD_SSLCOMMERZ = 'sslcommerz';
    const PAYMENT_METHOD_MOLLIE = 'mollie';

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_REFUNDED = 'refunded';

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function getReceiptUrlAttribute(): ?string
    {
        if (!$this->receipt_file) {
            return null;
        }

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('public');

        if (!$disk->exists($this->receipt_file)) {
            return null;
        }

        return $disk->url($this->receipt_file);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function isRazorpay(): bool
    {
        return $this->payment_method === self::PAYMENT_METHOD_RAZORPAY;
    }

    public function isOffline(): bool
    {
        return $this->payment_method === self::PAYMENT_METHOD_OFFLINE;
    }

    public function isStripe(): bool
    {
        return $this->payment_method === self::PAYMENT_METHOD_STRIPE;
    }

    public function isPaystack(): bool
    {
        return $this->payment_method === self::PAYMENT_METHOD_PAYSTACK;
    }

    public function isFlutterwave(): bool
    {
        return $this->payment_method === self::PAYMENT_METHOD_FLUTTERWAVE;
    }

    public function isPaypal(): bool
    {
        return $this->payment_method === self::PAYMENT_METHOD_PAYPAL;
    }

    public function isSslcommerz(): bool
    {
        return $this->payment_method === self::PAYMENT_METHOD_SSLCOMMERZ;
    }

    public function isMollie(): bool
    {
        return $this->payment_method === self::PAYMENT_METHOD_MOLLIE;
    }
}
