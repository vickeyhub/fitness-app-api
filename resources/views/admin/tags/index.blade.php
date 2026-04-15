@extends('layouts.admin')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Tags</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Tags</strong></li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row m-b-sm">
            <div class="col-sm-6">
                <form method="GET" action="{{ route('admin.tags.index') }}" class="form-inline">
                    <input type="text" name="q" class="form-control" value="{{ request('q') }}" placeholder="Search tags">
                    <select class="form-control" name="per_page">@foreach ([10,15,25,50,100] as $pp)<option value="{{ $pp }}" {{ (int) request('per_page', 15) === $pp ? 'selected' : '' }}>{{ $pp }}</option>@endforeach</select>
                    <button class="btn btn-primary"><i class="fa fa-search"></i> Apply</button>
                    <a href="{{ route('admin.tags.index') }}" class="btn btn-white">Reset</a>
                </form>
            </div>
            <div class="col-sm-6 text-right">
                <button class="btn btn-primary" id="createTagBtn"><i class="fa fa-plus"></i> Add tag</button>
            </div>
        </div>

        <div class="ibox">
            <div class="ibox-content table-responsive">
                <table class="table table-striped table-bordered">
                    <thead><tr><th>#</th><th>Name</th><th>Posts linked</th><th>Created</th><th>Updated</th><th class="text-right">Action</th></tr></thead>
                    <tbody>
                        @forelse ($tags as $tag)
                            <tr>
                                <td>{{ $tag->id }}</td>
                                <td>{{ $tag->name }}</td>
                                <td>{{ $tag->posts_count }}</td>
                                <td>
                                    @if($tag->created_at)
                                        <div>{{ $tag->created_at->format('d M Y, h:i A') }}</div>
                                        <small class="text-muted">{{ $tag->created_at->diffForHumans() }}</small>
                                    @else — @endif
                                </td>
                                <td>
                                    @if($tag->updated_at)
                                        <div>{{ $tag->updated_at->format('d M Y, h:i A') }}</div>
                                        <small class="text-muted">{{ $tag->updated_at->diffForHumans() }}</small>
                                    @else — @endif
                                </td>
                                <td class="text-right">
                                    <button class="btn btn-xs btn-info js-edit-tag" data-id="{{ $tag->id }}" data-name="{{ $tag->name }}"><i class="fa fa-pencil"></i> Edit</button>
                                    <button class="btn btn-xs btn-danger js-delete-tag" data-id="{{ $tag->id }}"><i class="fa fa-trash"></i> Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted">No tags found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="text-right">{{ $tags->links() }}</div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tagModal" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content">
            <form id="tagForm">
                @csrf
                <div class="modal-header"><button type="button" class="close" data-dismiss="modal">&times;</button><h4 class="modal-title" id="tagModalTitle">Add Tag</h4></div>
                <div class="modal-body">
                    <input type="hidden" id="tagId">
                    <div class="form-group">
                        <label>Name</label>
                        <input class="form-control" id="tagName" maxlength="50" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit" id="saveTagBtn">Save</button>
                    <button class="btn btn-white" type="button" data-dismiss="modal">Cancel</button>
                </div>
            </form>
        </div></div>
    </div>
@endsection

@section('scripts')
<script>
$(function () {
    let editingId = null;

    function openTagModal(id, name) {
        editingId = id || null;
        $('#tagId').val(editingId || '');
        $('#tagName').val(name || '');
        $('#tagModalTitle').text(editingId ? 'Edit Tag' : 'Add Tag');
        $('#tagModal').modal('show');
    }

    $('#createTagBtn').on('click', function () { openTagModal(null, ''); });
    $(document).on('click', '.js-edit-tag', function () { openTagModal($(this).data('id'), $(this).data('name')); });

    $('#tagForm').on('submit', function (e) {
        e.preventDefault();
        const id = $('#tagId').val();
        const payload = { name: $('#tagName').val() };
        const url = id ? ("{{ url('admin/tags') }}/" + id) : "{{ route('admin.tags.store') }}";
        const method = id ? 'PUT' : 'POST';
        $.ajax({ url, method, data: payload })
            .done(function (res) { toastr.success(res.message || 'Saved'); $('#tagModal').modal('hide'); location.reload(); })
            .fail(function (xhr) { toastr.error(xhr.responseJSON?.message || 'Save failed'); });
    });

    $(document).on('click', '.js-delete-tag', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this tag?')) return;
        $.ajax({ url: "{{ url('admin/tags') }}/" + id, method: 'DELETE' })
            .done(function (res) { toastr.success(res.message || 'Deleted'); location.reload(); })
            .fail(function (xhr) { toastr.error(xhr.responseJSON?.message || 'Delete failed'); });
    });
});
</script>
@endsection
