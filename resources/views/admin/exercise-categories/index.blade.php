@extends('layouts.admin')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Exercise Categories</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Exercise Categories</strong></li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row m-b-sm">
            <div class="col-sm-6">
                <form method="GET" action="{{ route('admin.exercise-categories.index') }}" class="form-inline">
                    <input class="form-control" type="text" name="q" value="{{ request('q') }}" placeholder="Search category">
                    <select class="form-control" name="per_page">@foreach ([10,15,25,50,100] as $pp)<option value="{{ $pp }}" {{ (int) request('per_page', 15) === $pp ? 'selected' : '' }}>{{ $pp }}</option>@endforeach</select>
                    <button class="btn btn-primary"><i class="fa fa-search"></i> Apply</button>
                    <a href="{{ route('admin.exercise-categories.index') }}" class="btn btn-white">Reset</a>
                </form>
            </div>
            <div class="col-sm-6 text-right">
                <button class="btn btn-primary" id="createCategoryBtn"><i class="fa fa-plus"></i> Add Category</button>
            </div>
        </div>
        <div class="ibox">
            <div class="ibox-content table-responsive">
                <table class="table table-striped table-bordered">
                    <thead><tr><th>#</th><th>Name</th><th>Exercises</th><th>Created</th><th>Updated</th><th class="text-right">Action</th></tr></thead>
                    <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td>{{ $category->exercises_count }}</td>
                            <td>
                                @if($category->created_at)
                                    <div>{{ $category->created_at->format('d M Y, h:i A') }}</div>
                                    <small class="text-muted">{{ $category->created_at->diffForHumans() }}</small>
                                @else — @endif
                            </td>
                            <td>
                                @if($category->updated_at)
                                    <div>{{ $category->updated_at->format('d M Y, h:i A') }}</div>
                                    <small class="text-muted">{{ $category->updated_at->diffForHumans() }}</small>
                                @else — @endif
                            </td>
                            <td class="text-right">
                                <button class="btn btn-xs btn-info js-edit-category" data-id="{{ $category->id }}" data-name="{{ $category->name }}"><i class="fa fa-pencil"></i> Edit</button>
                                <button class="btn btn-xs btn-danger js-delete-category" data-id="{{ $category->id }}"><i class="fa fa-trash"></i> Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">No categories found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="text-right">{{ $categories->links() }}</div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content">
            <form id="categoryForm">@csrf
                <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title" id="categoryModalTitle">Add Category</h4></div>
                <div class="modal-body">
                    <input type="hidden" id="categoryId">
                    <div class="form-group"><label>Name</label><input id="categoryName" class="form-control" maxlength="255" required></div>
                </div>
                <div class="modal-footer"><button class="btn btn-primary" type="submit">Save</button><button class="btn btn-white" data-dismiss="modal" type="button">Cancel</button></div>
            </form>
        </div></div>
    </div>
@endsection

@section('scripts')
<script>
$(function () {
    function openModal(id, name) {
        $('#categoryId').val(id || '');
        $('#categoryName').val(name || '');
        $('#categoryModalTitle').text(id ? 'Edit Category' : 'Add Category');
        $('#categoryModal').modal('show');
    }
    $('#createCategoryBtn').on('click', function () { openModal(null, ''); });
    $(document).on('click', '.js-edit-category', function () { openModal($(this).data('id'), $(this).data('name')); });
    $('#categoryForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#categoryId').val();
        const url = id ? ("{{ url('admin/exercise-categories') }}/" + id) : "{{ route('admin.exercise-categories.store') }}";
        const method = id ? 'PUT' : 'POST';
        $.ajax({ url: url, method: method, data: { name: $('#categoryName').val() } })
            .done(function (res) { toastr.success(res.message || 'Saved'); location.reload(); })
            .fail(function (xhr) { toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Save failed'); });
    });
    $(document).on('click', '.js-delete-category', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this category?')) return;
        $.ajax({ url: "{{ url('admin/exercise-categories') }}/" + id, method: 'DELETE' })
            .done(function (res) { toastr.success(res.message || 'Deleted'); location.reload(); })
            .fail(function (xhr) { toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Delete failed'); });
    });
});
</script>
@endsection
