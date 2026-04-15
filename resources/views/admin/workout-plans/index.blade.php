@extends('layouts.admin')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Workout Plans</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Workout Plans</strong></li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row m-b-sm">
            <div class="col-sm-8">
                <form method="GET" action="{{ route('admin.workout-plans.index') }}" class="form-inline">
                    <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Plan name">
                    <select name="user_id" class="form-control js-select2" data-placeholder="All users">
                        <option value="">All users</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}" {{ (string) request('user_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}</option>
                        @endforeach
                    </select>
                    <select class="form-control" name="per_page">@foreach ([10,15,25,50,100] as $pp)<option value="{{ $pp }}" {{ (int) request('per_page', 15) === $pp ? 'selected' : '' }}>{{ $pp }}</option>@endforeach</select>
                    <button class="btn btn-primary">Apply</button>
                    <a href="{{ route('admin.workout-plans.index') }}" class="btn btn-white">Reset</a>
                </form>
            </div>
            <div class="col-sm-4 text-right">
                <button class="btn btn-primary" id="createPlanBtn"><i class="fa fa-plus"></i> Add Plan</button>
            </div>
        </div>
        <div class="ibox"><div class="ibox-content">
            <table class="table table-striped table-bordered">
                <thead><tr><th>#</th><th>Plan</th><th>User</th><th>Exercises</th><th>Details</th><th>Created</th><th class="text-right">Action</th></tr></thead>
                <tbody>
                @forelse ($plans as $plan)
                    <tr>
                        <td>{{ $plan->id }}</td>
                        <td>{{ $plan->name }}</td>
                        <td>{{ optional($plan->user)->first_name }} {{ optional($plan->user)->last_name }}</td>
                        <td>{{ $plan->exercises_count }}</td>
                        <td>
                            @foreach ($plan->exercises as $line)
                                <div class="small text-muted">• {{ optional($line->exercise)->name ?: 'Exercise #' . $line->exercise_id }} — {{ $line->sets }}x{{ $line->reps }}</div>
                            @endforeach
                        </td>
                        <td>{{ optional($plan->created_at)->format('d M Y h:i A') }}</td>
                        <td class="text-right">
                            <button class="btn btn-xs btn-info js-edit-plan" data-id="{{ $plan->id }}"><i class="fa fa-pencil"></i> Edit</button>
                            <button class="btn btn-xs btn-danger js-delete-plan" data-id="{{ $plan->id }}"><i class="fa fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No workout plans found.</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="text-right">{{ $plans->links() }}</div>
        </div></div>
    </div>

    <div class="modal fade" id="planModal" tabindex="-1">
        <div class="modal-dialog modal-lg"><div class="modal-content">
            <form id="planForm">@csrf
                <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title" id="planModalTitle">Add Plan</h4></div>
                <div class="modal-body">
                    <input type="hidden" id="planId">
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>User</label><select id="planUserId" class="form-control js-select2" required data-placeholder="Select user"><option value=""></option>@foreach ($users as $u)<option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}</option>@endforeach</select></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Plan name</label><input id="planName" class="form-control" maxlength="255" required></div></div>
                    </div>
                    <hr>
                    <div class="m-b-sm"><strong>Exercises</strong> <button type="button" class="btn btn-xs btn-default" id="addPlanLineBtn"><i class="fa fa-plus"></i> Add row</button></div>
                    <div id="planLines"></div>
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

    function lineTemplate(line) {
        const l = line || {};
        let options = '<option value=""></option>';
        @foreach ($exercises as $ex)
            options += '<option value="{{ $ex->id }}">{{ addslashes($ex->name) }}</option>';
        @endforeach
        return '' +
            '<div class="row js-plan-line m-b-sm">' +
                '<div class="col-md-4"><select class="form-control js-line-exercise js-select2" data-placeholder="Exercise" required>' + options + '</select></div>' +
                '<div class="col-md-2"><input type="number" min="1" class="form-control js-line-sets" placeholder="Sets" required value="' + (l.sets || '') + '"></div>' +
                '<div class="col-md-2"><input type="number" min="1" class="form-control js-line-reps" placeholder="Reps" required value="' + (l.reps || '') + '"></div>' +
                '<div class="col-md-2"><input type="number" min="0" class="form-control js-line-rest" placeholder="Rest sec" required value="' + (l.rest_seconds || l.restSeconds || '') + '"></div>' +
                '<div class="col-md-1"><input type="number" min="0" step="0.1" class="form-control js-line-weight" placeholder="Kg" value="' + (l.weight || '') + '"></div>' +
                '<div class="col-md-1"><button type="button" class="btn btn-danger btn-sm js-remove-line"><i class="fa fa-times"></i></button></div>' +
            '</div>';
    }

    function addLine(line) {
        const $line = $(lineTemplate(line));
        $('#planLines').append($line);
        window.initUiEnhancements($line);
        if (line && line.exercise_id) {
            $line.find('.js-line-exercise').val(String(line.exercise_id)).trigger('change');
        } else if (line && line.exerciseId) {
            $line.find('.js-line-exercise').val(String(line.exerciseId)).trigger('change');
        }
    }

    function openPlanModal(data) {
        const d = data || {};
        $('#planForm')[0].reset();
        $('#planId').val(d.id || '');
        $('#planName').val(d.name || '');
        $('#planUserId').val(d.user_id || '').trigger('change');
        $('#planLines').empty();
        const lines = (d.exercises || []);
        if (lines.length) {
            lines.forEach(addLine);
        } else {
            addLine();
        }
        $('#planModalTitle').text(d.id ? 'Edit Plan' : 'Add Plan');
        $('#planModal').modal('show');
        window.initUiEnhancements('#planModal');
    }

    $('#createPlanBtn').on('click', function () { openPlanModal(); });
    $('#addPlanLineBtn').on('click', function () { addLine(); });
    $(document).on('click', '.js-remove-line', function () {
        const count = $('#planLines .js-plan-line').length;
        if (count <= 1) return;
        $(this).closest('.js-plan-line').remove();
    });

    $(document).on('click', '.js-edit-plan', function () {
        const id = $(this).data('id');
        $.get("{{ url('admin/workout-plans') }}/" + id, function (res) {
            const d = res.data || {};
            d.user_id = d.user_id || (d.user ? d.user.id : '');
            openPlanModal(d);
        }).fail(function () { toastr.error('Unable to load plan'); });
    });

    $('#planForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#planId').val();
        const payload = {
            user_id: $('#planUserId').val(),
            name: $('#planName').val(),
            exercises: []
        };
        $('#planLines .js-plan-line').each(function () {
            payload.exercises.push({
                exercise_id: $(this).find('.js-line-exercise').val(),
                sets: $(this).find('.js-line-sets').val(),
                reps: $(this).find('.js-line-reps').val(),
                rest_seconds: $(this).find('.js-line-rest').val(),
                weight: $(this).find('.js-line-weight').val()
            });
        });

        const url = id ? ("{{ url('admin/workout-plans') }}/" + id) : "{{ route('admin.workout-plans.store') }}";
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

    $(document).on('click', '.js-delete-plan', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this workout plan?')) return;
        $.ajax({ url: "{{ url('admin/workout-plans') }}/" + id, method: 'DELETE' })
            .done(function (res) { toastr.success(res.message || 'Deleted'); location.reload(); })
            .fail(function (xhr) { toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Delete failed'); });
    });
});
</script>
@endsection
