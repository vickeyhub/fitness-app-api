<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $request->validate([
            'amount' => 'required|integer',
            'currency' => 'required|string'
        ]);

        $paymentIntent = PaymentIntent::create([
            'amount' => $request->amount, // Amount in cents
            'currency' => $request->currency,
            'payment_method_types' => ['card'],
            'payment_method' => 'pm_card_visa', // ✅ Attach test payment method
            'confirm' => true // ✅ Auto-confirm the payment
        ]);

        return response()->json([
            'paymentIntentId' => $paymentIntent->id,
            'clientSecret' => $paymentIntent->client_secret,
            'status' => $paymentIntent->status
        ]);
    }

    // 2️⃣ Confirm Payment (if required)
    public function confirmPayment(Request $request)
    {
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));

            $request->validate([
                'payment_intent_id' => 'required|string',
                'payment_method' => 'required|string'
            ]);

            // $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);
            // $paymentIntent->confirm();

            $paymentIntent = PaymentIntent::retrieve($request->payment_intent_id);
            $paymentIntent->confirm([
                'payment_method' => $request->payment_method
            ]);

            if ($paymentIntent->status === 'requires_action') {
                return response()->json([
                    'status' => 'requires_action',
                    'next_action' => $paymentIntent->next_action
                ]);
            }

            return response()->json(['status' => $paymentIntent->status]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
