@extends('layouts.admin')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Exercise Logs</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Exercise Logs</strong></li>
            </ol>
        </div>
    </div>

    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox">
            <div class="ibox-content">
                <form method="GET" class="row">
                    <div class="col-md-3">
                        <label>User</label>
                        <select name="user_id" class="form-control js-select2" data-placeholder="All users">
                            <option value="">All users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ (string)($filters['user_id'] ?? '') === (string)$user->id ? 'selected' : '' }}>
                                    {{ trim($user->first_name . ' ' . $user->last_name) }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Workout Log</label>
                        <select name="workout_id" class="form-control js-select2" data-placeholder="All workouts">
                            <option value="">All workouts</option>
                            @foreach($workoutLogs as $workout)
                                <option value="{{ $workout->id }}" {{ (string)($filters['workout_id'] ?? '') === (string)$workout->id ? 'selected' : '' }}>
                                    #{{ $workout->id }} - {{ $workout->start_time }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>From</label>
                        <input type="text" name="from_date" value="{{ $filters['from_date'] ?? '' }}" class="form-control js-flatpickr-date" placeholder="YYYY-MM-DD">
                    </div>
                    <div class="col-md-2">
                        <label>To</label>
                        <input type="text" name="to_date" value="{{ $filters['to_date'] ?? '' }}" class="form-control js-flatpickr-date" placeholder="YYYY-MM-DD">
                    </div>
                    <div class="col-md-2">
                        <label>Per page</label>
                        <select name="per_page" class="form-control">
                            @foreach([10,15,25,50,100] as $size)
                                <option value="{{ $size }}" {{ (string)($filters['per_page'] ?? 15) === (string)$size ? 'selected' : '' }}>{{ $size }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12 m-t-sm">
                        <button class="btn btn-primary" type="submit"><i class="fa fa-filter"></i> Apply Filters</button>
                        <a class="btn btn-white" href="{{ route('admin.exercise-logs.index') }}">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="ibox">
            <div class="ibox-content">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Workout</th>
                                <th>User</th>
                                <th>Exercise</th>
                                <th>Sets x Reps</th>
                                <th>Weight</th>
                                <th>Duration</th>
                                <th>Distance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exerciseLogs as $log)
                                <tr>
                                    <td>{{ $log->id }}</td>
                                    <td>#{{ $log->workout_id }}</td>
                                    <td>{{ trim(($log->first_name ?? '') . ' ' . ($log->last_name ?? '')) ?: '—' }}</td>
                                    <td>{{ $log->exercise_name }}</td>
                                    <td>{{ $log->sets }} x {{ $log->reps_per_set }}</td>
                                    <td>{{ $log->weight_kg ?? '—' }}</td>
                                    <td>{{ $log->duration_seconds ? $log->duration_seconds . ' sec' : '—' }}</td>
                                    <td>{{ $log->distance_km ? $log->distance_km . ' km' : '—' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="8" class="text-center text-muted">No exercise logs found.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="text-right">
                    {{ $exerciseLogs->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
