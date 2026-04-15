<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentsController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->integer('per_page', 25);
        if (! in_array($perPage, [10, 25, 50, 100], true)) {
            $perPage = 25;
        }

        $payments = Payment::with('user')
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->string('q'));
                $query->where(function ($inner) use ($term) {
                    $inner->where('payment_intent_id', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhere('name', 'like', "%{$term}%")
                        ->orWhere('customer_id', 'like', "%{$term}%");
                });
            })
            ->when($request->filled('user_id'), function ($query) use ($request) {
                $query->where('user_id', (int) $request->integer('user_id'));
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', (string) $request->string('status'));
            })
            ->when($request->filled('currency'), function ($query) use ($request) {
                $query->where('currency', strtolower((string) $request->string('currency')));
            })
            ->when($request->filled('created_from'), function ($query) use ($request) {
                $query->whereDate('created_at', '>=', (string) $request->string('created_from'));
            })
            ->when($request->filled('created_to'), function ($query) use ($request) {
                $query->whereDate('created_at', '<=', (string) $request->string('created_to'));
            })
            ->when($request->filled('amount_min'), function ($query) use ($request) {
                $query->where('amount', '>=', (int) $request->integer('amount_min'));
            })
            ->when($request->filled('amount_max'), function ($query) use ($request) {
                $query->where('amount', '<=', (int) $request->integer('amount_max'));
            })
            ->orderByDesc('id')
            ->paginate($perPage)
            ->appends($request->query());

        $users = User::query()
            ->whereIn('id', Payment::query()->select('user_id')->whereNotNull('user_id')->distinct())
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'email']);

        $statuses = Payment::query()
            ->select('status')
            ->whereNotNull('status')
            ->distinct()
            ->orderBy('status')
            ->pluck('status');

        $currencies = Payment::query()
            ->select('currency')
            ->whereNotNull('currency')
            ->distinct()
            ->orderBy('currency')
            ->pluck('currency')
            ->map(fn ($currency) => strtolower((string) $currency));

        return view('admin.payments.index', compact('payments', 'users', 'statuses', 'currencies', 'perPage'));
    }

    public function show(Payment $payment)
    {
        $payment->load('user');

        return response()->json(['payment' => $payment]);
    }
}
