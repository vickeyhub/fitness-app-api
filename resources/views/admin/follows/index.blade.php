@extends('layouts.admin')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Follows</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Follows</strong></li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox">
            <div class="ibox-content">
                <form method="GET" action="{{ route('admin.follows.index') }}" class="row">
                    <div class="col-md-3"><div class="form-group"><label>Search user</label><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Name or email"></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Follower (who follows)</label><select name="follower_id" class="form-control js-select2"><option value="">All</option>@foreach ($users as $u)<option value="{{ $u->id }}" {{ (string) request('follower_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}</option>@endforeach</select></div></div>
                    <div class="col-md-3"><div class="form-group"><label>Following (being followed)</label><select name="following_id" class="form-control js-select2"><option value="">All</option>@foreach ($users as $u)<option value="{{ $u->id }}" {{ (string) request('following_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}</option>@endforeach</select></div></div>
                    <div class="col-md-1"><div class="form-group"><label>From</label><input class="form-control js-flatpickr-date" name="created_from" value="{{ request('created_from') }}"></div></div>
                    <div class="col-md-1"><div class="form-group"><label>To</label><input class="form-control js-flatpickr-date" name="created_to" value="{{ request('created_to') }}"></div></div>
                    <div class="col-md-1"><div class="form-group"><label>Per page</label><select class="form-control" name="per_page">@foreach ([10,15,25,50,100] as $pp)<option value="{{ $pp }}" {{ (int) request('per_page', 15) === $pp ? 'selected' : '' }}>{{ $pp }}</option>@endforeach</select></div></div>
                    <div class="col-md-12 text-right"><button class="btn btn-primary"><i class="fa fa-search"></i> Apply</button> <a class="btn btn-white" href="{{ route('admin.follows.index') }}">Reset</a></div>
                </form>
            </div>
        </div>

        <div class="ibox">
            <div class="ibox-content table-responsive">
                <table class="table table-striped table-bordered">
                    <thead><tr><th>#</th><th>Follower</th><th>Following</th><th>Created</th><th class="text-right">Action</th></tr></thead>
                    <tbody>
                        @forelse ($follows as $follow)
                            <tr>
                                <td>{{ $follow->id }}</td>
                                <td>
                                    <div>{{ optional($follow->follower)->first_name }} {{ optional($follow->follower)->last_name }}</div>
                                    <div class="small text-muted">{{ optional($follow->follower)->email }}</div>
                                </td>
                                <td>
                                    <div>{{ optional($follow->following)->first_name }} {{ optional($follow->following)->last_name }}</div>
                                    <div class="small text-muted">{{ optional($follow->following)->email }}</div>
                                </td>
                                <td>{{ optional($follow->created_at)->format('d M Y h:i A') }}</td>
                                <td class="text-right"><button class="btn btn-xs btn-danger js-delete-follow" data-id="{{ $follow->id }}"><i class="fa fa-chain-broken"></i> Remove</button></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">No follow links found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="text-right">{{ $follows->links() }}</div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
$(function () {
    window.initUiEnhancements(document);
    $(document).on('click', '.js-delete-follow', function () {
        var id = $(this).data('id');
        if (!confirm('Remove this follow link?')) return;
        $.ajax({ url: "{{ url('admin/follows') }}/" + id, method: 'DELETE' })
            .done(function (res) { toastr.success(res.message || 'Removed'); location.reload(); })
            .fail(function (xhr) {
                var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Remove failed';
                toastr.error(msg);
            });
    });
});
</script>
@endsection
