@extends('layouts.admin')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Nutrition Meals</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Nutrition Meals</strong></li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row m-b-sm">
            <div class="col-sm-8">
                <form method="GET" action="{{ route('admin.nutrition.meals.index') }}" class="form-inline">
                    <select name="user_id" class="form-control js-select2" data-placeholder="All users"><option value="">All users</option>@foreach ($users as $u)<option value="{{ $u->id }}" {{ (string) request('user_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}</option>@endforeach</select>
                    <input class="form-control js-flatpickr-date" name="date" value="{{ request('date') }}" placeholder="Date">
                    <input class="form-control" name="meal_type" value="{{ request('meal_type') }}" placeholder="Meal type">
                    <select class="form-control" name="per_page">@foreach ([10,15,25,50,100] as $pp)<option value="{{ $pp }}" {{ (int) request('per_page', 15) === $pp ? 'selected' : '' }}>{{ $pp }}</option>@endforeach</select>
                    <button class="btn btn-primary">Apply</button>
                    <a href="{{ route('admin.nutrition.meals.index') }}" class="btn btn-white">Reset</a>
                </form>
            </div>
            <div class="col-sm-4 text-right"><button class="btn btn-primary" id="createMealBtn"><i class="fa fa-plus"></i> Add Meal</button></div>
        </div>
        <div class="ibox"><div class="ibox-content table-responsive">
            <table class="table table-striped table-bordered">
                <thead><tr><th>#</th><th>User</th><th>Date</th><th>Meal Type</th><th>Proteins</th><th>Fats</th><th>Carbs</th><th>Calories</th><th class="text-right">Action</th></tr></thead>
                <tbody>
                @forelse ($meals as $meal)
                    <tr>
                        <td>{{ $meal->id }}</td>
                        <td>{{ optional($meal->user)->first_name }} {{ optional($meal->user)->last_name }}</td>
                        <td>{{ optional($meal->date)->format('Y-m-d') }}</td>
                        <td>{{ $meal->meal_type }}</td>
                        <td>{{ $meal->proteins }}</td>
                        <td>{{ $meal->fats }}</td>
                        <td>{{ $meal->carbs }}</td>
                        <td>{{ $meal->calories }}</td>
                        <td class="text-right">
                            <button class="btn btn-xs btn-info js-edit-meal" data-id="{{ $meal->id }}"><i class="fa fa-pencil"></i> Edit</button>
                            <button class="btn btn-xs btn-danger js-delete-meal" data-id="{{ $meal->id }}"><i class="fa fa-trash"></i> Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="9" class="text-center text-muted">No meals found.</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="text-right">{{ $meals->links() }}</div>
        </div></div>
    </div>

    <div class="modal fade" id="mealModal" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content">
            <form id="mealForm">@csrf
                <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title" id="mealModalTitle">Add Meal</h4></div>
                <div class="modal-body">
                    <input type="hidden" id="mealId">
                    <div class="form-group"><label>User</label><select id="mealUserId" class="form-control js-select2" required data-placeholder="Select user"><option value=""></option>@foreach ($users as $u)<option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}</option>@endforeach</select></div>
                    <div class="row">
                        <div class="col-md-6"><div class="form-group"><label>Date</label><input id="mealDate" class="form-control js-flatpickr-date" required></div></div>
                        <div class="col-md-6"><div class="form-group"><label>Meal Type</label><input id="mealType" class="form-control" required maxlength="255" placeholder="breakfast/lunch/dinner"></div></div>
                    </div>
                    <div class="row">
                        <div class="col-md-3"><div class="form-group"><label>Proteins</label><input id="mealProteins" type="number" min="0" class="form-control" required></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Fats</label><input id="mealFats" type="number" min="0" class="form-control" required></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Carbs</label><input id="mealCarbs" type="number" min="0" class="form-control" required></div></div>
                        <div class="col-md-3"><div class="form-group"><label>Calories</label><input id="mealCalories" type="number" min="0" class="form-control" required></div></div>
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
    function openMealModal(d) {
        const x = d || {};
        $('#mealForm')[0].reset();
        $('#mealId').val(x.id || '');
        $('#mealUserId').val(x.user_id || '').trigger('change');
        $('#mealDate').val(x.date || '');
        $('#mealType').val(x.meal_type || '');
        $('#mealProteins').val(x.proteins || 0);
        $('#mealFats').val(x.fats || 0);
        $('#mealCarbs').val(x.carbs || 0);
        $('#mealCalories').val(x.calories || 0);
        $('#mealModalTitle').text(x.id ? 'Edit Meal' : 'Add Meal');
        $('#mealModal').modal('show');
        window.initUiEnhancements('#mealModal');
    }
    $('#createMealBtn').on('click', function () { openMealModal(); });
    $(document).on('click', '.js-edit-meal', function () {
        const id = $(this).data('id');
        $.get("{{ url('admin/nutrition/meals') }}/" + id, function (res) { openMealModal(res.data || {}); })
            .fail(function () { toastr.error('Unable to load meal'); });
    });
    $('#mealForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#mealId').val();
        const payload = {
            user_id: $('#mealUserId').val(),
            date: $('#mealDate').val(),
            meal_type: $('#mealType').val(),
            proteins: $('#mealProteins').val(),
            fats: $('#mealFats').val(),
            carbs: $('#mealCarbs').val(),
            calories: $('#mealCalories').val()
        };
        const url = id ? ("{{ url('admin/nutrition/meals') }}/" + id) : "{{ route('admin.nutrition.meals.store') }}";
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
    $(document).on('click', '.js-delete-meal', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this meal?')) return;
        $.ajax({ url: "{{ url('admin/nutrition/meals') }}/" + id, method: 'DELETE' })
            .done(function (res) { toastr.success(res.message || 'Deleted'); location.reload(); })
            .fail(function (xhr) { toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Delete failed'); });
    });
});
</script>
@endsection
