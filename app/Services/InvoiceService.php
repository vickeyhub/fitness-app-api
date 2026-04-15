<?php

namespace App\Services;

use App\Models\AdminSetting;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class InvoiceService
{
    public function upsertFromBooking(Booking $booking): ?Invoice
    {
        if (! Schema::hasTable('invoices')) {
            return null;
        }

        $booking->loadMissing(['user', 'session', 'payment']);

        $payment = $booking->payment;
        $subtotal = $payment ? (float) $payment->amount : (float) ($booking->session->price ?? 0);
        $currency = strtolower((string) ($payment->currency ?? 'usd'));

        $settings = $this->settings();
        $taxPercent = (float) ($settings['tax_percent'] ?? 0);
        $discountAmount = (float) ($settings['default_discount_amount'] ?? 0);
        $taxAmount = round($subtotal * ($taxPercent / 100), 2);
        $total = max(0, round($subtotal + $taxAmount - $discountAmount, 2));

        $invoice = Invoice::query()->firstOrNew(['booking_id' => $booking->id]);
        if (! $invoice->exists) {
            $invoice->invoice_number = $this->nextInvoiceNumber();
        }

        $invoice->fill([
            'user_id' => $booking->user_id,
            'payment_row_id' => $payment?->id,
            'payment_intent_id' => $booking->payment_id,
            'status' => $booking->payment_status === 'paid' ? 'paid' : 'issued',
            'currency' => $currency,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'issued_at' => $invoice->issued_at ?: Carbon::now(),
            'paid_at' => $booking->payment_status === 'paid' ? Carbon::now() : null,
            'snapshot' => [
                'booking' => [
                    'id' => $booking->id,
                    'booking_date' => $booking->booking_date,
                    'time_slot' => $booking->time_slot,
                    'status' => $booking->status,
                    'payment_status' => $booking->payment_status,
                ],
                'user' => [
                    'id' => $booking->user?->id,
                    'name' => trim((string) ($booking->user?->first_name . ' ' . $booking->user?->last_name)),
                    'email' => $booking->user?->email,
                ],
                'session' => [
                    'id' => $booking->session?->id,
                    'title' => $booking->session?->session_title,
                    'price' => $booking->session?->price,
                ],
                'payment' => $payment ? [
                    'id' => $payment->id,
                    'payment_intent_id' => $payment->payment_intent_id,
                    'status' => $payment->status,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                ] : null,
            ],
            'notes' => (string) ($settings['footer_notes'] ?? ''),
        ]);

        $invoice->save();

        return $invoice;
    }

    public function upsertFromPayment(Payment $payment): ?Invoice
    {
        if (! Schema::hasTable('invoices')) {
            return null;
        }

        $booking = Booking::query()->where('payment_id', $payment->payment_intent_id)->first();
        if ($booking) {
            return $this->upsertFromBooking($booking);
        }

        $payment->loadMissing('user');
        $settings = $this->settings();

        $subtotal = (float) $payment->amount;
        $taxPercent = (float) ($settings['tax_percent'] ?? 0);
        $discountAmount = (float) ($settings['default_discount_amount'] ?? 0);
        $taxAmount = round($subtotal * ($taxPercent / 100), 2);
        $total = max(0, round($subtotal + $taxAmount - $discountAmount, 2));
        $paid = in_array(strtolower((string) $payment->status), ['paid', 'succeeded'], true);

        $invoice = Invoice::query()->firstOrNew(['payment_row_id' => $payment->id]);
        if (! $invoice->exists) {
            $invoice->invoice_number = $this->nextInvoiceNumber();
        }
        $invoice->fill([
            'user_id' => $payment->user_id,
            'payment_intent_id' => $payment->payment_intent_id,
            'status' => $paid ? 'paid' : 'issued',
            'currency' => strtolower((string) $payment->currency),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total' => $total,
            'issued_at' => $invoice->issued_at ?: Carbon::now(),
            'paid_at' => $paid ? Carbon::now() : null,
            'snapshot' => [
                'payment' => [
                    'id' => $payment->id,
                    'payment_intent_id' => $payment->payment_intent_id,
                    'status' => $payment->status,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency,
                ],
                'user' => [
                    'id' => $payment->user?->id,
                    'name' => trim((string) ($payment->user?->first_name . ' ' . $payment->user?->last_name)),
                    'email' => $payment->user?->email,
                ],
            ],
            'notes' => (string) ($settings['footer_notes'] ?? ''),
        ]);
        $invoice->save();

        return $invoice;
    }

    /**
     * @return array<string, mixed>
     */
    public function settings(): array
    {
        if (! Schema::hasTable('admin_settings')) {
            return $this->defaultSettings();
        }

        $row = AdminSetting::query()->where('key', 'invoice')->first();
        $value = is_array($row?->value) ? $row->value : [];

        return array_merge($this->defaultSettings(), $value);
    }

    /**
     * @param  array<string, mixed>  $settings
     */
    public function saveSettings(array $settings): void
    {
        if (! Schema::hasTable('admin_settings')) {
            return;
        }

        AdminSetting::query()->updateOrCreate(
            ['key' => 'invoice'],
            ['value' => array_merge($this->defaultSettings(), $settings)]
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultSettings(): array
    {
        return [
            'company_name' => config('app.name', 'Fitness App'),
            'company_email' => '',
            'company_phone' => '',
            'company_address' => '',
            'company_logo_url' => '',
            'tax_percent' => 0,
            'default_discount_amount' => 0,
            'footer_notes' => 'Thank you for your business.',
        ];
    }

    private function nextInvoiceNumber(): string
    {
        $year = Carbon::now()->year;
        $prefix = "INV-{$year}-";
        $latest = Invoice::query()
            ->where('invoice_number', 'like', $prefix . '%')
            ->orderByDesc('id')
            ->value('invoice_number');

        $seq = 1;
        if ($latest && preg_match('/INV-\d{4}-(\d+)/', $latest, $m)) {
            $seq = ((int) $m[1]) + 1;
        }

        return $prefix . str_pad((string) $seq, 6, '0', STR_PAD_LEFT);
    }
}
