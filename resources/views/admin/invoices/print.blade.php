<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>{{ $invoice->invoice_number ?: ('Invoice #' . $invoice->id) }}</title>
    <style>
        body { font-family: Arial, sans-serif; color:#222; font-size: 13px; margin: 24px; }
        .row { width: 100%; clear: both; margin-bottom: 16px; }
        .col-6 { width: 48%; float: left; }
        .right { float: right; text-align: right; }
        .muted { color: #666; }
        .title { font-size: 24px; margin: 0; }
        .box { border: 1px solid #ddd; border-radius: 6px; padding: 12px; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background: #f6f6f6; }
        .text-right { text-align: right; }
        .clearfix::after { content: ""; display: table; clear: both; }
    </style>
</head>
<body>
@php
    $snap = is_array($snapshot ?? null) ? $snapshot : [];
    $snapUser = $snap['user'] ?? [];
    $snapBooking = $snap['booking'] ?? [];
    $snapSession = $snap['session'] ?? [];
    $snapPayment = $snap['payment'] ?? [];
    $name = trim((optional($invoice->user)->first_name ?? '') . ' ' . (optional($invoice->user)->last_name ?? '')) ?: ($snapUser['name'] ?? 'Customer');
@endphp

<div class="row clearfix">
    <div class="col-6">
        <h1 class="title">{{ $settings['company_name'] ?? config('app.name') }}</h1>
        <div class="muted">{{ $settings['company_email'] ?? '' }}</div>
        <div class="muted">{{ $settings['company_phone'] ?? '' }}</div>
        <div class="muted">{{ $settings['company_address'] ?? '' }}</div>
    </div>
    <div class="col-6 right">
        <h2 style="margin:0;">INVOICE</h2>
        <div><strong>{{ $invoice->invoice_number }}</strong></div>
        <div class="muted">Issued: {{ optional($invoice->issued_at)->format('d M Y, h:i A') ?: '—' }}</div>
        <div class="muted">Due: {{ optional($invoice->due_at)->format('d M Y, h:i A') ?: '—' }}</div>
        <div class="muted">Status: {{ ucfirst((string) $invoice->status) }}</div>
    </div>
</div>

<div class="row clearfix">
    <div class="col-6 box">
        <strong>Bill To</strong><br>
        {{ $name }}<br>
        {{ optional($invoice->user)->email ?: ($snapUser['email'] ?? '—') }}
    </div>
    <div class="col-6 box right">
        <strong>References</strong><br>
        Booking: {{ $invoice->booking_id ? '#'.$invoice->booking_id : ($snapBooking['id'] ?? '—') }}<br>
        Payment Intent: {{ $invoice->payment_intent_id ?: ($snapPayment['payment_intent_id'] ?? '—') }}<br>
        Paid At: {{ optional($invoice->paid_at)->format('d M Y, h:i A') ?: '—' }}
    </div>
</div>

<div class="row">
    <table>
        <thead>
        <tr>
            <th>Description</th>
            <th class="text-right">Amount</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{ $snapSession['title'] ?? 'Booking / Payment charge' }}</td>
            <td class="text-right">{{ number_format((float)$invoice->subtotal, 2) }} {{ strtoupper((string)$invoice->currency) }}</td>
        </tr>
        <tr>
            <td>Tax</td>
            <td class="text-right">{{ number_format((float)$invoice->tax_amount, 2) }} {{ strtoupper((string)$invoice->currency) }}</td>
        </tr>
        <tr>
            <td>Discount</td>
            <td class="text-right">- {{ number_format((float)$invoice->discount_amount, 2) }} {{ strtoupper((string)$invoice->currency) }}</td>
        </tr>
        <tr>
            <th class="text-right">Total</th>
            <th class="text-right">{{ number_format((float)$invoice->total, 2) }} {{ strtoupper((string)$invoice->currency) }}</th>
        </tr>
        </tbody>
    </table>
</div>

<div class="row clearfix">
    <div class="col-6 box">
        <strong>Booking Details</strong><br>
        Date: {{ $snapBooking['booking_date'] ?? '—' }}<br>
        Time: {{ $snapBooking['time_slot'] ?? '—' }}
    </div>
    <div class="col-6 box">
        <strong>Payment Details</strong><br>
        Status: {{ $snapPayment['status'] ?? $invoice->status }}<br>
        Amount: {{ number_format((float)($snapPayment['amount'] ?? $invoice->total), 2) }} {{ strtoupper((string)($snapPayment['currency'] ?? $invoice->currency)) }}
    </div>
</div>

<div class="row muted">
    {{ $invoice->notes ?: ($settings['footer_notes'] ?? '') }}
</div>
</body>
</html>
