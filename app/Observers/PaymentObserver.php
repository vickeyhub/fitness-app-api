<?php

namespace App\Observers;

use App\Models\Payment;
use App\Services\InvoiceService;

class PaymentObserver
{
    public function saved(Payment $payment): void
    {
        try {
            $status = strtolower((string) $payment->status);
            if (in_array($status, ['paid', 'succeeded'], true)) {
                app(InvoiceService::class)->upsertFromPayment($payment);
            }
        } catch (\Throwable) {
            // Never break payment writes because of invoice generation.
        }
    }
}
