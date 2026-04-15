@extends('layouts.admin')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Comments</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Comments</strong></li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox">
            <div class="ibox-content">
                <form method="GET" action="{{ route('admin.comments.index') }}" class="row">
                    <div class="col-md-3"><div class="form-group"><label>Search</label><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Comment text"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Post</label><select name="post_id" class="form-control js-select2"><option value="">All</option>@foreach ($posts as $post)<option value="{{ $post->id }}" {{ (string) request('post_id') === (string) $post->id ? 'selected' : '' }}>#{{ $post->id }} — {{ \Illuminate\Support\Str::limit($post->title, 45) }}</option>@endforeach</select></div></div>
                    <div class="col-md-3"><div class="form-group"><label>User</label><select name="user_id" class="form-control js-select2"><option value="">All</option>@foreach ($users as $u)<option value="{{ $u->id }}" {{ (string) request('user_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}</option>@endforeach</select></div></div>
                    <div class="col-md-1"><div class="form-group"><label>Visible</label><select class="form-control" name="is_hidden"><option value="">All</option><option value="0" {{ request('is_hidden') === '0' ? 'selected' : '' }}>Visible</option><option value="1" {{ request('is_hidden') === '1' ? 'selected' : '' }}>Hidden</option></select></div></div>
                    <div class="col-md-1"><div class="form-group"><label>From</label><input class="form-control js-flatpickr-date" name="created_from" value="{{ request('created_from') }}"></div></div>
                    <div class="col-md-1"><div class="form-group"><label>To</label><input class="form-control js-flatpickr-date" name="created_to" value="{{ request('created_to') }}"></div></div>
                    <div class="col-md-1"><div class="form-group"><label>Per page</label><select class="form-control" name="per_page">@foreach ([10,15,25,50,100] as $pp)<option value="{{ $pp }}" {{ (int) request('per_page', 15) === $pp ? 'selected' : '' }}>{{ $pp }}</option>@endforeach</select></div></div>
                    <div class="col-md-12 text-right">
                        <button class="btn btn-primary"><i class="fa fa-search"></i> Apply</button>
                        <a href="{{ route('admin.comments.index') }}" class="btn btn-white">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="ibox">
            <div class="ibox-content table-responsive">
                <table class="table table-striped table-bordered">
                    <thead><tr><th>#</th><th>Comment</th><th>Post</th><th>User</th><th>Visibility</th><th>Created</th><th>Updated</th><th class="text-right">Action</th></tr></thead>
                    <tbody>
                        @forelse ($comments as $comment)
                            <tr>
                                <td>{{ $comment->id }}</td>
                                <td style="max-width: 420px;">{{ $comment->comment }}</td>
                                <td>#{{ $comment->post_id }} — {{ optional($comment->post)->title ?: '—' }}</td>
                                <td>{{ optional($comment->user)->first_name }} {{ optional($comment->user)->last_name }}</td>
                                <td>{!! $comment->is_hidden ? '<span class="label label-warning">Hidden</span>' : '<span class="label label-primary">Visible</span>' !!}</td>
                                <td>
                                    @if($comment->created_at)
                                        <div>{{ $comment->created_at->format('d M Y, h:i A') }}</div>
                                        <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                                    @else — @endif
                                </td>
                                <td>
                                    @if($comment->updated_at)
                                        <div>{{ $comment->updated_at->format('d M Y, h:i A') }}</div>
                                        <small class="text-muted">{{ $comment->updated_at->diffForHumans() }}</small>
                                    @else — @endif
                                </td>
                                <td class="text-right">
                                    <button class="btn btn-xs btn-default js-toggle-comment-visibility" data-id="{{ $comment->id }}"><i class="fa fa-eye-slash"></i> {{ $comment->is_hidden ? 'Unhide' : 'Hide' }}</button>
                                    <button class="btn btn-xs btn-danger js-delete-comment" data-id="{{ $comment->id }}"><i class="fa fa-trash"></i> Delete</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center text-muted">No comments found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="text-right">{{ $comments->links() }}</div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
$(function () {
    window.initUiEnhancements(document);
    $(document).on('click', '.js-delete-comment', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this comment?')) return;
        $.ajax({ url: "{{ url('admin/comments') }}/" + id, method: 'DELETE' })
            .done(function (res) { toastr.success(res.message || 'Deleted'); location.reload(); })
            .fail(function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Delete failed';
                toastr.error(msg);
            });
    });

    $(document).on('click', '.js-toggle-comment-visibility', function () {
        const id = $(this).data('id');
        $.post("{{ url('admin/comments') }}/" + id + "/toggle-visibility")
            .done(function (res) { toastr.success(res.message || 'Updated'); location.reload(); })
            .fail(function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Update failed';
                toastr.error(msg);
            });
    });
});
</script>
@endsection
