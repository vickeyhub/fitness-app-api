<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $data['users'] = User::selectRaw("
        Count(*) as total_users,
        SUM(CASE WHEN user_type = 'user' THEN 1 ELSE 0 END) as total_users_count,
        SUM(CASE WHEN user_type = 'gym' THEN 1 ELSE 0 END) as total_gym_count,
        SUM(CASE WHEN user_type = 'trainer' THEN 1 ELSE 0 END) as total_trainer_count,
        SUM(CASE WHEN status = '1' THEN 1 ELSE 0 END) as total_active_users_count,
        SUM(CASE WHEN status = '2' THEN 1 ELSE 0 END) as total_inactive_users_count")
            ->first();

        $userCounts = User::select('user_type', DB::raw('COUNT(*) as count'))
            ->groupBy('user_type')
            ->get();
        $data['labels'] = $userCounts->pluck('user_type');
        $data['user_count'] = $userCounts->pluck('count');
        return view('admin.dashboard', $data);
    }

    public function chartDataLine()
    {
        // Simulated x-axis values (e.g., months, days, or IDs)
        $xValues = [100, 200, 300, 400, 500, 600, 700, 800, 900, 1000];

        // Fetch counts dynamically (replace with real logic)
        $totalUsers = User::count();  // Replace with real-time data logic
        $totalGyms = User::where('user_type', 'gym')->count();
        $totalBookings = Booking::count();  // If you have a bookings table

        // Generate some dummy values (replace with real aggregated data)
        $usersData = array_map(fn($x) => rand(1000, $totalUsers * 10), $xValues);
        $gymsData = array_map(fn($x) => rand(100, $totalGyms * 10), $xValues);
        $bookingsData = array_map(fn($x) => rand(100, $totalBookings * 10), $xValues);

        return response()->json([
            'xValues' => $xValues,
            'usersData' => $usersData,
            'gymsData' => $gymsData,
            'bookingsData' => $bookingsData
        ]);
    }

    public function newChartDataLine()
    {
        // Fetch user registrations grouped by date
        $usersData = User::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Fetch gyms added per day
        $gymsData = User::where('user_type', 'gym')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Fetch total bookings per day
        $bookingsData = Booking::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        return response()->json([
            'xValues' => $usersData->pluck('date'), // Extract only dates
            'usersData' => $usersData->pluck('count'), // Users per date
            'gymsData' => $gymsData->pluck('count'), // Gyms per date
            'bookingsData' => $bookingsData->pluck('count') // Bookings per date
        ]);
    }
}
