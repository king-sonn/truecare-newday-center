<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Donation model - records M-Pesa STK Push donations to Truecare Newday Center.
 *
 * @package App\Models
 */
class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'phone',
        'amount',
        'business_no',
        'account_no',
        'account_name',
        'checkout_request_id',
        'merchant_request_id',
        'mpesa_receipt',
        'status',
        'result_code',
        'result_desc',
    ];

    protected $casts = [
        'amount' => 'integer',
        'status' => 'string',
    ];

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }
}