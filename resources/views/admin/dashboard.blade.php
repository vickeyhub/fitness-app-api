@extends('layouts.admin')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Dashboard 2.0</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Dashboard</strong></li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-3"><div class="ibox"><div class="ibox-content"><h5>Daily bookings</h5><h2>{{ number_format($kpis['daily_bookings']) }}</h2></div></div></div>
            <div class="col-md-3"><div class="ibox"><div class="ibox-content"><h5>Daily revenue</h5><h2>{{ number_format($kpis['daily_revenue'], 2) }}</h2></div></div></div>
            <div class="col-md-3"><div class="ibox"><div class="ibox-content"><h5>Active users</h5><h2>{{ number_format($kpis['active_users']) }}</h2></div></div></div>
            <div class="col-md-3"><div class="ibox"><div class="ibox-content"><h5>Nutrition adherence</h5><h2>{{ number_format($kpis['nutrition_adherence'], 1) }}%</h2></div></div></div>
        </div>
        <div class="row">
            <div class="col-md-4"><div class="ibox"><div class="ibox-content"><h5>Post volume (today)</h5><h2>{{ number_format($kpis['post_volume']) }}</h2></div></div></div>
            <div class="col-md-4"><div class="ibox"><div class="ibox-content"><h5>Comment volume (today)</h5><h2>{{ number_format($kpis['comment_volume']) }}</h2></div></div></div>
            <div class="col-md-4"><div class="ibox"><div class="ibox-content"><h5>Workout logs (today)</h5><h2>{{ number_format($kpis['workout_logs_count']) }}</h2></div></div></div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="ibox">
                    <div class="ibox-title"><h5>Bookings & Revenue (14 days)</h5></div>
                    <div class="ibox-content"><canvas id="bookingRevenueChart" height="130"></canvas></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="ibox">
                    <div class="ibox-title"><h5>Content & Workout Activity (14 days)</h5></div>
                    <div class="ibox-content"><canvas id="activityChart" height="130"></canvas></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        (function () {
            var labels = @json($series['labels']);
            var bookings = @json($series['bookings']);
            var revenue = @json($series['revenue']);
            var posts = @json($series['posts']);
            var comments = @json($series['comments']);
            var workoutLogs = @json($series['workout_logs']);

            var bookingRevenueCtx = document.getElementById('bookingRevenueChart').getContext('2d');
            new Chart(bookingRevenueCtx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Bookings', data: bookings, borderColor: '#1c84c6', yAxisID: 'y', tension: 0.25 },
                        { label: 'Revenue', data: revenue, borderColor: '#1ab394', yAxisID: 'y1', tension: 0.25 }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true, position: 'left' },
                        y1: { beginAtZero: true, position: 'right', grid: { drawOnChartArea: false } }
                    }
                }
            });

            var activityCtx = document.getElementById('activityChart').getContext('2d');
            new Chart(activityCtx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        { label: 'Posts', data: posts, backgroundColor: '#f8ac59' },
                        { label: 'Comments', data: comments, backgroundColor: '#23c6c8' },
                        { label: 'Workout Logs', data: workoutLogs, backgroundColor: '#ed5565' }
                    ]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });
        })();
    </script>
@endsection
