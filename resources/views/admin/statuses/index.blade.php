@extends('layouts.admin')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Statuses</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Statuses</strong></li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox">
            <div class="ibox-content">
                <form method="GET" action="{{ route('admin.statuses.index') }}" class="row">
                    <div class="col-md-3"><div class="form-group"><label>Caption search</label><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Caption"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>User</label><select name="user_id" class="form-control js-select2"><option value="">All</option>@foreach ($users as $u)<option value="{{ $u->id }}" {{ (string) request('user_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}</option>@endforeach</select></div></div>
                    <div class="col-md-2"><div class="form-group"><label>Type</label><select name="type" class="form-control js-select2"><option value="">All</option><option value="photo" {{ request('type') === 'photo' ? 'selected' : '' }}>Photo</option><option value="video" {{ request('type') === 'video' ? 'selected' : '' }}>Video</option></select></div></div>
                    <div class="col-md-2"><div class="form-group"><label>From</label><input class="form-control js-flatpickr-date" name="created_from" value="{{ request('created_from') }}"></div></div>
                    <div class="col-md-2"><div class="form-group"><label>To</label><input class="form-control js-flatpickr-date" name="created_to" value="{{ request('created_to') }}"></div></div>
                    <div class="col-md-2"><div class="form-group"><label>Per page</label><select class="form-control" name="per_page">@foreach ([10,15,25,50,100] as $pp)<option value="{{ $pp }}" {{ (int) request('per_page', 15) === $pp ? 'selected' : '' }}>{{ $pp }}</option>@endforeach</select></div></div>
                    <div class="col-md-10 text-right"><button class="btn btn-primary"><i class="fa fa-search"></i> Apply</button> <a class="btn btn-white" href="{{ route('admin.statuses.index') }}">Reset</a></div>
                </form>
            </div>
        </div>

        <div class="ibox">
            <div class="ibox-content table-responsive">
                <table class="table table-striped table-bordered">
                    <thead><tr><th>#</th><th>User</th><th>Type</th><th>Caption</th><th>Media</th><th>Created</th><th class="text-right">Action</th></tr></thead>
                    <tbody>
                        @forelse ($statuses as $status)
                            <tr>
                                <td>{{ $status->id }}</td>
                                <td>{{ optional($status->user)->first_name }} {{ optional($status->user)->last_name }}</td>
                                <td><span class="label label-{{ $status->type === 'video' ? 'danger' : 'primary' }}">{{ ucfirst($status->type) }}</span></td>
                                <td>{{ $status->caption ?: '—' }}</td>
                                <td>
                                    @if ($status->media)
                                        <a href="{{ asset('storage/' . $status->media) }}" target="_blank" class="btn btn-xs btn-white"><i class="fa fa-external-link"></i> Open</a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>{{ optional($status->created_at)->format('d M Y h:i A') }}</td>
                                <td class="text-right"><button class="btn btn-xs btn-danger js-delete-status" data-id="{{ $status->id }}"><i class="fa fa-trash"></i> Delete</button></td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">No statuses found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="text-right">{{ $statuses->links() }}</div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
$(function () {
    window.initUiEnhancements(document);
    $(document).on('click', '.js-delete-status', function () {
        const id = $(this).data('id');
        if (!confirm('Delete this status?')) return;
        $.ajax({ url: "{{ url('admin/statuses') }}/" + id, method: 'DELETE' })
            .done(function (res) { toastr.success(res.message || 'Deleted'); location.reload(); })
            .fail(function (xhr) { toastr.error(xhr.responseJSON?.message || 'Delete failed'); });
    });
});
</script>
@endsection
