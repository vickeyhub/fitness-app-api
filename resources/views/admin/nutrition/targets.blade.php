@extends('layouts.admin')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Nutrition Targets</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Nutrition Targets</strong></li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row m-b-sm">
            <div class="col-sm-8">
                <form method="GET" action="{{ route('admin.nutrition.targets.index') }}" class="form-inline">
                    <select name="user_id" class="form-control js-select2" data-placeholder="All users"><option value="">All users</option>@foreach ($users as $u)<option value="{{ $u->id }}" {{ (string) request('user_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}</option>@endforeach</select>
                    <input class="form-control" name="q" value="{{ request('q') }}" placeholder="Search user name/email">
                    <select class="form-control" name="per_page">@foreach ([10,15,25,50,100] as $pp)<option value="{{ $pp }}" {{ (int) request('per_page', 15) === $pp ? 'selected' : '' }}>{{ $pp }}</option>@endforeach</select>
                    <button class="btn btn-primary">Apply</button>
                    <a href="{{ route('admin.nutrition.targets.index') }}" class="btn btn-white">Reset</a>
                </form>
            </div>
            <div class="col-sm-4 text-right"><button class="btn btn-primary" id="createTargetBtn"><i class="fa fa-plus"></i> Set Target</button></div>
        </div>
        <div class="ibox"><div class="ibox-content table-responsive">
            <table class="table table-striped table-bordered">
                <thead><tr><th>#</th><th>User</th><th>Calories</th><th>Proteins</th><th>Fats</th><th>Carbs</th><th>Updated</th><th class="text-right">Action</th></tr></thead>
                <tbody>
                @forelse ($targets as $target)
                    <tr>
                        <td>{{ $target->id }}</td>
                        <td>{{ optional($target->user)->first_name }} {{ optional($target->user)->last_name }}</td>
                        <td>{{ $target->calories }}</td>
                        <td>{{ $target->proteins }}</td>
                        <td>{{ $target->fats }}</td>
                        <td>{{ $target->carbs }}</td>
                        <td>{{ optional($target->updated_at)->format('d M Y h:i A') }}</td>
                        <td class="text-right">
                            <button class="btn btn-xs btn-info js-edit-target" data-id="{{ $target->id }}"><i class="fa fa-pencil"></i> Edit</button>
                            <button class="btn btn-xs btn-danger js-delete-target" data-id="{{ $target->id }}"><i class="fa fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center text-muted">No targets found.</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="text-right">{{ $targets->links() }}</div>
        </div></div>
    </div>

    <div class="modal fade" id="targetModal" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content">
            <form id="targetForm">@csrf
                <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title" id="targetModalTitle">Set Target</h4></div>
                <div class="modal-body">
                    <input type="hidden" id="targetId">
                    <div class="form-group"><label>User</label><select id="targetUserId" class="form-control js-select2" required data-placeholder="Select user"><option value=""></option>@foreach ($users as $u)<option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}</option>@endforeach</select></div>
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>Calories</label><input id="targetCalories" type="number" min="0" class="form-control" required></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Proteins</label><input id="targetProteins" type="number" min="0" class="form-control" required></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Fats</label><input id="targetFats" type="number" min="0" class="form-control" required></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Carbs</label><input id="targetCarbs" type="number" min="0" class="form-control" required></div></div>
                    </div>
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
    function openTargetModal(d) {
        const x = d || {};
        $('#targetForm')[0].reset();
        $('#targetId').val(x.id || '');
        $('#targetUserId').val(x.user_id || '').trigger('change');
        $('#targetCalories').val(x.calories || 2000);
        $('#targetProteins').val(x.proteins || 100);
        $('#targetFats').val(x.fats || 70);
        $('#targetCarbs').val(x.carbs || 250);
        $('#targetModalTitle').text(x.id ? 'Edit Target' : 'Set Target');
        $('#targetModal').modal('show');
        window.initUiEnhancements('#targetModal');
    }
    $('#createTargetBtn').on('click', function () { openTargetModal(); });
    $(document).on('click', '.js-edit-target', function () {
        const id = $(this).data('id');
        $.get("{{ url('admin/nutrition/targets') }}/" + id, function (res) { openTargetModal(res.data || {}); })
            .fail(function () { toastr.error('Unable to load target'); });
    });
    $('#targetForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#targetId').val();
        const payload = {
            user_id: $('#targetUserId').val(),
            calories: $('#targetCalories').val(),
            proteins: $('#targetProteins').val(),
            fats: $('#targetFats').val(),
            carbs: $('#targetCarbs').val()
        };
        const url = id ? ("{{ url('admin/nutrition/targets') }}/" + id) : "{{ route('admin.nutrition.targets.store') }}";
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
    $(document).on('click', '.js-delete-target', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this nutrition target?')) return;
        $.ajax({ url: "{{ url('admin/nutrition/targets') }}/" + id, method: 'DELETE' })
            .done(function (res) { toastr.success(res.message || 'Deleted'); location.reload(); })
            .fail(function (xhr) { toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Delete failed'); });
    });
});
</script>
@endsection
