<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Comment;
use App\Models\Meal;
use App\Models\NutritionTarget;
use App\Models\Payment;
use App\Models\Post;
use App\Models\User;
use App\Models\WorkoutLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today();
        $dailyBookings = Booking::query()->whereDate('booking_date', $today)->count();
        $dailyRevenue = (float) Payment::query()
            ->whereDate('created_at', $today)
            ->whereIn(DB::raw('LOWER(status)'), ['succeeded', 'paid'])
            ->sum('amount');
        $activeUsers = User::query()->where('status', '1')->count();
        $postVolume = Post::query()->whereDate('created_at', $today)->count();
        $commentVolume = Comment::query()->whereDate('created_at', $today)->count();
        $workoutLogsCount = WorkoutLog::query()->whereDate('start_time', $today)->count();

        $nutritionAdherence = $this->todayAdherencePercent($today);
        $series = $this->buildSeries($today->copy()->subDays(13), $today);

        return view('admin.dashboard', [
            'kpis' => [
                'daily_bookings' => $dailyBookings,
                'daily_revenue' => $dailyRevenue,
                'active_users' => $activeUsers,
                'post_volume' => $postVolume,
                'comment_volume' => $commentVolume,
                'workout_logs_count' => $workoutLogsCount,
                'nutrition_adherence' => $nutritionAdherence,
            ],
            'series' => $series,
        ]);
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
        $today = Carbon::today();
        $series = $this->buildSeries($today->copy()->subDays(13), $today);

        return response()->json([
            'xValues' => $series['labels'],
            'usersData' => $series['active_users'],
            'gymsData' => $series['workout_logs'],
            'bookingsData' => $series['bookings'],
        ]);
    }

    private function todayAdherencePercent(Carbon $date): float
    {
        $consumedByUser = Meal::query()
            ->select('user_id', DB::raw('SUM(calories) as consumed_calories'))
            ->whereDate('date', $date->toDateString())
            ->groupBy('user_id')
            ->pluck('consumed_calories', 'user_id');

        if ($consumedByUser->isEmpty()) {
            return 0.0;
        }

        $targets = NutritionTarget::query()
            ->whereIn('user_id', $consumedByUser->keys())
            ->pluck('calories', 'user_id');

        $percentages = $consumedByUser->map(function ($consumed, $userId) use ($targets) {
            $target = (float) ($targets[$userId] ?? 2000);
            if ($target <= 0) {
                return 0;
            }
            return min(200, round(((float) $consumed / $target) * 100, 2));
        });

        return round((float) $percentages->avg(), 2);
    }

    private function buildSeries(Carbon $from, Carbon $to): array
    {
        $labels = [];
        for ($day = $from->copy(); $day->lte($to); $day->addDay()) {
            $labels[] = $day->toDateString();
        }

        $bookings = Booking::query()
            ->selectRaw('DATE(booking_date) as d, COUNT(*) as c')
            ->whereBetween('booking_date', [$from->toDateString(), $to->toDateString()])
            ->groupBy('d')
            ->pluck('c', 'd');

        $revenue = Payment::query()
            ->selectRaw('DATE(created_at) as d, SUM(amount) as s')
            ->whereBetween(DB::raw('DATE(created_at)'), [$from->toDateString(), $to->toDateString()])
            ->whereIn(DB::raw('LOWER(status)'), ['succeeded', 'paid'])
            ->groupBy('d')
            ->pluck('s', 'd');

        $posts = Post::query()
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->whereBetween(DB::raw('DATE(created_at)'), [$from->toDateString(), $to->toDateString()])
            ->groupBy('d')
            ->pluck('c', 'd');

        $comments = Comment::query()
            ->selectRaw('DATE(created_at) as d, COUNT(*) as c')
            ->whereBetween(DB::raw('DATE(created_at)'), [$from->toDateString(), $to->toDateString()])
            ->groupBy('d')
            ->pluck('c', 'd');

        $workoutLogs = WorkoutLog::query()
            ->selectRaw('DATE(start_time) as d, COUNT(*) as c')
            ->whereBetween(DB::raw('DATE(start_time)'), [$from->toDateString(), $to->toDateString()])
            ->groupBy('d')
            ->pluck('c', 'd');

        $activeUsers = User::query()
            ->selectRaw('DATE(created_at) as d, SUM(CASE WHEN status = "1" THEN 1 ELSE 0 END) as c')
            ->whereBetween(DB::raw('DATE(created_at)'), [$from->toDateString(), $to->toDateString()])
            ->groupBy('d')
            ->pluck('c', 'd');

        return [
            'labels' => $labels,
            'bookings' => array_map(fn ($d) => (int) ($bookings[$d] ?? 0), $labels),
            'revenue' => array_map(fn ($d) => (float) ($revenue[$d] ?? 0), $labels),
            'posts' => array_map(fn ($d) => (int) ($posts[$d] ?? 0), $labels),
            'comments' => array_map(fn ($d) => (int) ($comments[$d] ?? 0), $labels),
            'workout_logs' => array_map(fn ($d) => (int) ($workoutLogs[$d] ?? 0), $labels),
            'active_users' => array_map(fn ($d) => (int) ($activeUsers[$d] ?? 0), $labels),
        ];
    }
}
