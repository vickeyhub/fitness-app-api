<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportsController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->date('from_date') ?: Carbon::today()->subDays(29);
        $to = $request->date('to_date') ?: Carbon::today();

        $bookingsCount = Booking::query()->whereBetween('booking_date', [$from->toDateString(), $to->toDateString()])->count();
        $paidBookings = Booking::query()->whereBetween('booking_date', [$from->toDateString(), $to->toDateString()])->where('payment_status', 'paid')->count();
        $revenue = (float) Payment::query()
            ->whereBetween(DB::raw('DATE(created_at)'), [$from->toDateString(), $to->toDateString()])
            ->whereIn(DB::raw('LOWER(status)'), ['paid', 'succeeded'])
            ->sum('amount');
        $activeUsers = User::query()->where('status', '1')->count();
        $invoicesIssued = Invoice::query()->whereBetween(DB::raw('DATE(created_at)'), [$from->toDateString(), $to->toDateString()])->count();

        $daily = Booking::query()
            ->selectRaw('DATE(booking_date) as d, COUNT(*) as bookings, SUM(CASE WHEN payment_status = "paid" THEN 1 ELSE 0 END) as paid_bookings')
            ->whereBetween('booking_date', [$from->toDateString(), $to->toDateString()])
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        return view('admin.reports.index', [
            'from' => $from,
            'to' => $to,
            'kpis' => [
                'bookings_count' => $bookingsCount,
                'paid_bookings' => $paidBookings,
                'revenue' => $revenue,
                'active_users' => $activeUsers,
                'invoices_issued' => $invoicesIssued,
            ],
            'daily' => $daily,
        ]);
    }
}
