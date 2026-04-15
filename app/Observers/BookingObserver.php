<?php

namespace App\Observers;

use App\Models\Booking;
use App\Services\InvoiceService;

class BookingObserver
{
    public function saved(Booking $booking): void
    {
        try {
            if ($booking->payment_status === 'paid') {
                app(InvoiceService::class)->upsertFromBooking($booking);
            }
        } catch (\Throwable) {
            // Never break booking writes because of invoice generation.
        }
    }
}
