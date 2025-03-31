<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Customer;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function createPaymentIntent(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));

        $request->validate([
            'amount' => 'required|integer',
            'currency' => 'required|string'
        ]);

        $user = $request->user();

        if (!$user->stripe_customer_id) {
            $customer = Customer::create([
                'email' => $user->email,
                'name' => $user->first_name.' ' .$user->last_name
            ]);

            // Store Customer ID in DB
            $user->stripe_customer_id = $customer->id;
            $user->save();
        } else {
            $customer = Customer::retrieve($user->stripe_customer_id);
        }

        $paymentIntent = PaymentIntent::create([
            'amount' => $request->amount, // Amount in cents
            'currency' => $request->currency,
            'payment_method_types' => ['card'],
            'payment_method' => 'pm_card_visa', // ✅ Attach test payment method
            'confirm' => true // ✅ Auto-confirm the payment
        ]);

        // 4️⃣ Store Payment in DB
        Payment::create([
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'email' => $user->email,
            'name' => $user->first_name.' '.$user->last_name,
            'payment_intent_id' => $paymentIntent->id,
            'status' => $paymentIntent->status,
            'amount' => $request->amount,
            'currency' => $request->currency,
            'response_data' => $paymentIntent
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
