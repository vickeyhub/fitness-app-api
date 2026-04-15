@extends('layouts.admin')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Exercises</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Exercises</strong></li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row m-b-sm">
            <div class="col-sm-8">
                <form method="GET" action="{{ route('admin.exercises.index') }}" class="form-inline">
                    <input class="form-control" type="text" name="q" value="{{ request('q') }}" placeholder="Search name/description">
                    <select class="form-control js-select2" name="exercise_category_id" data-placeholder="All categories">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ (string) request('exercise_category_id') === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <select class="form-control" name="per_page">@foreach ([10,15,25,50,100] as $pp)<option value="{{ $pp }}" {{ (int) request('per_page', 15) === $pp ? 'selected' : '' }}>{{ $pp }}</option>@endforeach</select>
                    <button class="btn btn-primary"><i class="fa fa-search"></i> Apply</button>
                    <a href="{{ route('admin.exercises.index') }}" class="btn btn-white">Reset</a>
                </form>
            </div>
            <div class="col-sm-4 text-right"><button class="btn btn-primary" id="createExerciseBtn"><i class="fa fa-plus"></i> Add Exercise</button></div>
        </div>
        <div class="ibox"><div class="ibox-content table-responsive">
            <table class="table table-striped table-bordered">
                <thead><tr><th>#</th><th>Name</th><th>Category</th><th>Description</th><th class="text-right">Action</th></tr></thead>
                <tbody>
                @forelse ($exercises as $exercise)
                    <tr>
                        <td>{{ $exercise->id }}</td>
                        <td>{{ $exercise->name }}</td>
                        <td>{{ optional($exercise->category)->name ?: '—' }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($exercise->description, 100) ?: '—' }}</td>
                        <td class="text-right">
                            <button class="btn btn-xs btn-info js-edit-exercise" data-id="{{ $exercise->id }}"><i class="fa fa-pencil"></i> Edit</button>
                            <button class="btn btn-xs btn-danger js-delete-exercise" data-id="{{ $exercise->id }}"><i class="fa fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No exercises found.</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="text-right">{{ $exercises->links() }}</div>
        </div></div>
    </div>

    <div class="modal fade" id="exerciseModal" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content">
            <form id="exerciseForm">@csrf
                <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title" id="exerciseModalTitle">Add Exercise</h4></div>
                <div class="modal-body">
                    <input type="hidden" id="exerciseId">
                    <div class="form-group"><label>Name</label><input id="exerciseName" class="form-control" required maxlength="255"></div>
                    <div class="form-group"><label>Category</label>
                        <select id="exerciseCategoryId" class="form-control js-select2" required data-placeholder="Select category">
                            <option value=""></option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group m-b-none"><label>Description</label><textarea id="exerciseDescription" class="form-control" rows="4"></textarea></div>
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
    function openExerciseModal(data) {
        const d = data || {};
        $('#exerciseId').val(d.id || '');
        $('#exerciseName').val(d.name || '');
        $('#exerciseCategoryId').val(d.exercise_category_id || '').trigger('change');
        $('#exerciseDescription').val(d.description || '');
        $('#exerciseModalTitle').text(d.id ? 'Edit Exercise' : 'Add Exercise');
        $('#exerciseModal').modal('show');
        window.initUiEnhancements('#exerciseModal');
    }
    $('#createExerciseBtn').on('click', function () { openExerciseModal(); });
    $(document).on('click', '.js-edit-exercise', function () {
        const id = $(this).data('id');
        $.get("{{ url('admin/exercises') }}/" + id, function (res) { openExerciseModal(res.data || {}); })
            .fail(function () { toastr.error('Unable to load exercise'); });
    });
    $('#exerciseForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#exerciseId').val();
        const payload = {
            name: $('#exerciseName').val(),
            exercise_category_id: $('#exerciseCategoryId').val(),
            description: $('#exerciseDescription').val()
        };
        const url = id ? ("{{ url('admin/exercises') }}/" + id) : "{{ route('admin.exercises.store') }}";
        const method = id ? 'PUT' : 'POST';
        $.ajax({ url: url, method: method, data: payload })
            .done(function (res) { toastr.success(res.message || 'Saved'); location.reload(); })
            .fail(function (xhr) { toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Save failed'); });
    });
    $(document).on('click', '.js-delete-exercise', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this exercise?')) return;
        $.ajax({ url: "{{ url('admin/exercises') }}/" + id, method: 'DELETE' })
            .done(function (res) { toastr.success(res.message || 'Deleted'); location.reload(); })
            .fail(function (xhr) { toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Delete failed'); });
    });
});
</script>
@endsection
