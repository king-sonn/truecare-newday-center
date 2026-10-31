<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Facades\DB;

/**
 * Revenue Share Service
 *
 * This service is responsible for calculating the revenue share for a payment.
 *
 * @package App\Services
 */
class RevenueShareService
{
    public function __construct(private SettingsRepository $settingsRepository)
    {
    }

    public function settings(): array
    {
        return $this->settingsRepository->get('revenue.distribution', [
            'mode' => 'percentage',
            'value' => 0,
        ]);
    }

    public function calculate(float $amount): array
    {
        $settings = $this->settings();
        $mode = $settings['mode'] ?? 'percentage';
        $value = (float) ($settings['value'] ?? 0);

        $commission = 0.0;

        if ($amount <= 0) {
            return [
                'platform_commission' => 0.0,
                'instructor_earning' => 0.0,
            ];
        }

        if ($mode === 'fixed') {
            $commission = min($value, $amount);
        } else {
            if ($value < 0) {
                $value = 0;
            }
            if ($value > 100) {
                $value = 100;
            }
            $commission = round($amount * ($value / 100), 2);
        }

        $commission = round($commission, 2);
        $instructor = round($amount - $commission, 2);

        if ($instructor < 0) {
            $instructor = 0.0;
        }

        return [
            'platform_commission' => $commission,
            'instructor_earning' => $instructor,
        ];
    }

    public function process(Payment $payment, bool $force = false): Payment
    {
        if (!$force && $payment->commission_processed) {
            return $payment;
        }

        $payment->loadMissing('enrollment.course.instructor');

        $amount = (float) $payment->amount;
        $distribution = $this->calculate($amount);
        $platformCommission = $distribution['platform_commission'];
        $instructorEarning = $distribution['instructor_earning'];

        $instructor = optional($payment->enrollment)->course?->instructor;

        return DB::transaction(function () use ($payment, $platformCommission, $instructorEarning, $instructor) {
            $payment->platform_commission = $platformCommission;
            $payment->instructor_earning = $instructorEarning;
            $payment->commission_processed = true;
            $payment->save();

            if ($instructor && $instructorEarning > 0) {
                $instructor->increment('balance', $instructorEarning);
            }

            return $payment->fresh();
        });
    }
}

