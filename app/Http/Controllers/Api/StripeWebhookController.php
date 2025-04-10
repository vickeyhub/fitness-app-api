<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Payment;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle_full_code(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET'); // You'll get this from Stripe dashboard

        try {
            $event = Webhook::constructEvent($payload, $sigHeader, $endpointSecret);
        } catch (\UnexpectedValueException $e) {
            return response()->json(['message' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // Handle successful payment
        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event->data->object;

            // You can find booking/payment using $paymentIntent->id
            $payment = Payment::where('payment_intent_id', $paymentIntent->id)->first();

            if ($payment) {
                $payment->status = 'succeeded';
                $payment->save();

                // Optionally update related booking
                $booking = Booking::where('payment_id', $paymentIntent->id)->first();
                if ($booking) {
                    $booking->payment_status = 'paid';
                    $booking->status = '1'; // Confirmed
                    $booking->save();
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            return response()->json(['message' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return response()->json(['message' => 'Invalid signature'], 400);
        }

        // 📝 Save the webhook data as JSON in public/stripe_webhooks/
        $timestamp = now()->format('Y-m-d_H-i-s');
        $filename = public_path("stripe_webhooks/stripe_webhook_log_{$timestamp}.json");

        // Ensure the directory exists
        if (!file_exists(public_path('stripe_webhooks'))) {
            mkdir(public_path('stripe_webhooks'), 0755, true);
        }

        file_put_contents($filename, json_encode($event, JSON_PRETTY_PRINT));

        return response()->json(['status' => 'logged']);
    }

}
