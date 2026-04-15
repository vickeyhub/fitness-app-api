<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use App\Services\InvoiceService;
use App\Support\AuditTrailLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class InvoicesController extends Controller
{
    public function __construct(private readonly InvoiceService $invoiceService)
    {
    }

    public function index(Request $request)
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'currency' => ['nullable', 'string', 'max:10'],
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50,100'],
        ]);

        $query = Invoice::query()
            ->with(['user:id,first_name,last_name,email', 'booking:id,session_id,payment_id,booking_date,time_slot', 'booking.session:id,session_title', 'payment:id,payment_intent_id,status,amount,currency'])
            ->latest('id');

        if (!empty($filters['q'])) {
            $q = trim((string) $filters['q']);
            $query->where(function ($inner) use ($q) {
                $inner->where('invoice_number', 'like', "%{$q}%")
                    ->orWhere('payment_intent_id', 'like', "%{$q}%");
            });
        }
        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['user_id'])) {
            $query->where('user_id', (int) $filters['user_id']);
        }
        if (!empty($filters['currency'])) {
            $query->where('currency', strtolower((string) $filters['currency']));
        }
        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }
        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        return view('admin.invoices.index', [
            'invoices' => $query->paginate((int) ($filters['per_page'] ?? 15))->appends($request->query()),
            'users' => User::query()->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'email']),
            'statuses' => Invoice::query()->select('status')->distinct()->orderBy('status')->pluck('status'),
            'currencies' => Invoice::query()->select('currency')->whereNotNull('currency')->distinct()->pluck('currency'),
            'filters' => $filters,
        ]);
    }

    public function show(Invoice $invoice)
    {
        return response()->json([
            'invoice' => $invoice->load(['user:id,first_name,last_name,email', 'booking.session:id,session_title', 'payment:id,payment_intent_id,status,amount,currency']),
        ]);
    }

    public function generateFromBooking(Booking $booking)
    {
        $invoice = $this->invoiceService->upsertFromBooking($booking);
        if (! $invoice) {
            return response()->json(['message' => 'Invoices table not ready. Please run migrations first.'], 422);
        }
        AuditTrailLogger::log('invoices', 'generate_from_booking', $invoice, ['booking_id' => $booking->id]);

        return response()->json([
            'message' => 'Invoice generated from booking.',
            'invoice' => $invoice->fresh(),
        ]);
    }

    public function generateFromPayment(Payment $payment)
    {
        $invoice = $this->invoiceService->upsertFromPayment($payment);
        if (! $invoice) {
            return response()->json(['message' => 'Invoices table not ready. Please run migrations first.'], 422);
        }
        AuditTrailLogger::log('invoices', 'generate_from_payment', $invoice, ['payment_id' => $payment->id]);

        return response()->json([
            'message' => 'Invoice generated from payment.',
            'invoice' => $invoice->fresh(),
        ]);
    }

    public function print(Invoice $invoice)
    {
        return view('admin.invoices.print', $this->invoiceViewData($invoice));
    }

    public function pdf(Invoice $invoice)
    {
        $data = $this->invoiceViewData($invoice);
        $pdf = Pdf::loadView('admin.invoices.print', $data)->setPaper('a4');

        return $pdf->download(strtolower($invoice->invoice_number ?: ('invoice-' . $invoice->id)) . '.pdf');
    }

    public function settings()
    {
        return view('admin.invoices.settings', [
            'settings' => $this->invoiceService->settings(),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $payload = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'company_email' => ['nullable', 'string', 'max:255'],
            'company_phone' => ['nullable', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:1000'],
            'company_logo_url' => ['nullable', 'string', 'max:1000'],
            'tax_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'default_discount_amount' => ['nullable', 'numeric', 'min:0'],
            'footer_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->invoiceService->saveSettings($payload);

        return redirect()->route('admin.invoices.settings')->with('success', 'Invoice settings updated.');
    }

    /**
     * @return array<string, mixed>
     */
    private function invoiceViewData(Invoice $invoice): array
    {
        $invoice->load(['user:id,first_name,last_name,email', 'booking.session:id,session_title', 'payment:id,payment_intent_id,status,amount,currency']);

        return [
            'invoice' => $invoice,
            'settings' => $this->invoiceService->settings(),
            'snapshot' => is_array($invoice->snapshot) ? $invoice->snapshot : [],
        ];
    }
}
