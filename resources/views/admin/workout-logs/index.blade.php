@extends('layouts.admin')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Workout Logs</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Workout Logs</strong></li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row m-b-sm">
            <div class="col-sm-8">
                <form method="GET" action="{{ route('admin.workout-logs.index') }}" class="form-inline">
                    <select name="user_id" class="form-control js-select2" data-placeholder="All users"><option value="">All users</option>@foreach ($users as $u)<option value="{{ $u->id }}" {{ (string) request('user_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}</option>@endforeach</select>
                    <input class="form-control" name="workout_type" value="{{ request('workout_type') }}" placeholder="Workout type">
                    <input class="form-control js-flatpickr-date" name="from_date" value="{{ request('from_date') }}" placeholder="From">
                    <input class="form-control js-flatpickr-date" name="to_date" value="{{ request('to_date') }}" placeholder="To">
                    <select class="form-control" name="per_page">@foreach ([10,15,25,50,100] as $pp)<option value="{{ $pp }}" {{ (int) request('per_page', 15) === $pp ? 'selected' : '' }}>{{ $pp }}</option>@endforeach</select>
                    <button class="btn btn-primary">Apply</button>
                    <a href="{{ route('admin.workout-logs.index') }}" class="btn btn-white">Reset</a>
                </form>
            </div>
            <div class="col-sm-4 text-right">
                <button class="btn btn-primary" id="createLogBtn"><i class="fa fa-plus"></i> Add Log</button>
            </div>
        </div>
        <div class="ibox"><div class="ibox-content">
            <table class="table table-striped table-bordered">
                <thead><tr><th>#</th><th>User</th><th>Workout ID</th><th>Type</th><th>Start</th><th>End</th><th>Duration</th><th>Calories</th><th>Notes</th><th>Created</th><th>Updated</th><th class="text-right">Action</th></tr></thead>
                <tbody>
                @forelse ($logs as $log)
                    <tr>
                        <td>{{ $log->id }}</td>
                        <td>{{ optional($log->user)->first_name }} {{ optional($log->user)->last_name }}</td>
                        <td>{{ $log->workout_id }}</td>
                        <td>{{ $log->workout_type }}</td>
                        <td>{{ optional($log->start_time)->format('d M Y h:i A') }}</td>
                        <td>{{ optional($log->end_time)->format('d M Y h:i A') }}</td>
                        <td>{{ $log->duration_minutes }} min</td>
                        <td>{{ $log->calories_burned ?? '—' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($log->notes, 80) ?: '—' }}</td>
                        <td>
                            @if($log->created_at)
                                <div>{{ $log->created_at->format('d M Y, h:i A') }}</div>
                                <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                            @else — @endif
                        </td>
                        <td>
                            @if($log->updated_at)
                                <div>{{ $log->updated_at->format('d M Y, h:i A') }}</div>
                                <small class="text-muted">{{ $log->updated_at->diffForHumans() }}</small>
                            @else — @endif
                        </td>
                        <td class="text-right">
                            <button class="btn btn-xs btn-info js-edit-log" data-id="{{ $log->id }}"><i class="fa fa-pencil"></i> Edit</button>
                            <button class="btn btn-xs btn-danger js-delete-log" data-id="{{ $log->id }}"><i class="fa fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="12" class="text-center text-muted">No workout logs found.</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="text-right">{{ $logs->links() }}</div>
        </div></div>
    </div>

    <div class="modal fade" id="logModal" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content">
            <form id="logForm">@csrf
                <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title" id="logModalTitle">Add Workout Log</h4></div>
                <div class="modal-body">
                    <input type="hidden" id="logId">
                    <div class="form-group"><label>User</label><select id="logUserId" class="form-control js-select2" required data-placeholder="Select user"><option value=""></option>@foreach ($users as $u)<option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}</option>@endforeach</select></div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Workout ID</label><input id="logWorkoutId" class="form-control" required maxlength="255"></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Workout Type</label><input id="logWorkoutType" class="form-control" required maxlength="255"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Start Time</label><input id="logStartTime" class="form-control js-flatpickr-datetime" required></div></div>
                        <div class="col-md-6"><div class="form-group"><label>End Time</label><input id="logEndTime" class="form-control js-flatpickr-datetime" required></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Duration (minutes)</label><input id="logDuration" type="number" min="1" class="form-control" required></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Calories Burned</label><input id="logCalories" type="number" min="0" class="form-control"></div></div>
                    </div>
                    <div class="form-group m-b-none"><label>Notes</label><textarea id="logNotes" class="form-control" rows="3"></textarea></div>
                </div>
                <div class="modal-footer"><button class="btn btn-primary" type="submit">Save</button><button class="btn btn-white" type="button" data-dismiss="modal">Cancel</button></div>
            </form>
        </div></div>
    </div>
@endsection

@section('scripts')
<script>
$(function () {
    window.initUiEnhancements(document);

    function initDateTimePicker(selector) {
        $(selector).each(function() {
            if (this._flatpickr) return;
            flatpickr(this, {
                enableTime: true,
                dateFormat: 'Y-m-d H:i',
                time_24hr: true,
                minuteIncrement: 5,
                disableMobile: true
            });
        });
    }
    initDateTimePicker('.js-flatpickr-datetime');

    function openLogModal(data) {
        const d = data || {};
        $('#logForm')[0].reset();
        $('#logId').val(d.id || '');
        $('#logUserId').val(d.user_id || '').trigger('change');
        $('#logWorkoutId').val(d.workout_id || '');
        $('#logWorkoutType').val(d.workout_type || '');
        $('#logStartTime').val(d.start_time ? String(d.start_time).replace('T', ' ').substring(0, 16) : '');
        $('#logEndTime').val(d.end_time ? String(d.end_time).replace('T', ' ').substring(0, 16) : '');
        $('#logDuration').val(d.duration_minutes || '');
        $('#logCalories').val(d.calories_burned || '');
        $('#logNotes').val(d.notes || '');
        $('#logModalTitle').text(d.id ? 'Edit Workout Log' : 'Add Workout Log');
        $('#logModal').modal('show');
        window.initUiEnhancements('#logModal');
        initDateTimePicker('#logModal .js-flatpickr-datetime');
    }

    $('#createLogBtn').on('click', function () { openLogModal(); });
    $(document).on('click', '.js-edit-log', function () {
        const id = $(this).data('id');
        $.get("{{ url('admin/workout-logs') }}/" + id, function (res) { openLogModal(res.data || {}); })
            .fail(function () { toastr.error('Unable to load log'); });
    });

    $('#logForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#logId').val();
        const payload = {
            user_id: $('#logUserId').val(),
            workout_id: $('#logWorkoutId').val(),
            workout_type: $('#logWorkoutType').val(),
            start_time: $('#logStartTime').val(),
            end_time: $('#logEndTime').val(),
            duration_minutes: $('#logDuration').val(),
            calories_burned: $('#logCalories').val(),
            notes: $('#logNotes').val()
        };
        const url = id ? ("{{ url('admin/workout-logs') }}/" + id) : "{{ route('admin.workout-logs.store') }}";
        const method = id ? 'PUT' : 'POST';
        $.ajax({ url: url, method: method, data: payload })
            .done(function (res) { toastr.success(res.message || 'Saved'); location.reload(); })
            .fail(function (xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    const first = Object.keys(xhr.responseJSON.errors)[0];
                    toastr.error(xhr.responseJSON.errors[first][0] || 'Validation failed');
                } else {
                    toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Save failed');
                }
            });
    });

    $(document).on('click', '.js-delete-log', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this workout log?')) return;
        $.ajax({ url: "{{ url('admin/workout-logs') }}/" + id, method: 'DELETE' })
            .done(function (res) { toastr.success(res.message || 'Deleted'); location.reload(); })
            .fail(function (xhr) { toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Delete failed'); });
    });
});
</script>
@endsection
