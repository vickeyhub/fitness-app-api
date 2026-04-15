@extends('layouts.admin')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>{{ $scopeLabel ?? 'Users' }}</h2>
            <ol class="breadcrumb">
                <li>
                    <a href="{{ route('admin.dashboard') }}">Home</a>
                </li>
                <li class="active">
                    <strong>{{ $scopeLabel ?? 'Users' }}</strong>
                </li>
            </ol>
        </div>
        <div class="col-lg-2">
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        @php
            $isTrainersScope = ($scopeType ?? null) === 'trainer';
            $isGymsScope = ($scopeType ?? null) === 'gym';
        @endphp
        <div class="m-b-sm">
            <a href="{{ route('admin.users') }}" class="btn btn-sm {{ !$isTrainersScope && !$isGymsScope ? 'btn-primary' : 'btn-white' }}">All Users</a>
            <a href="{{ route('admin.users.trainers') }}" class="btn btn-sm {{ $isTrainersScope ? 'btn-primary' : 'btn-white' }}">Trainers</a>
            <a href="{{ route('admin.users.gyms') }}" class="btn btn-sm {{ $isGymsScope ? 'btn-primary' : 'btn-white' }}">Gyms</a>
        </div>
        <button type="button" class="btn btn-primary m-b-sm" data-toggle="modal" data-target="#addUserModal">
            <i class="fa fa-plus"></i> Add User
        </button>

        <div id="addUserModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add User</h4>
                    </div>
                    <div class="modal-body">
                        <form id="addUserForm" class="m-t" role="form">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>First name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Last name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" name="username" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mobile number</label>
                                        <input type="text" name="mobile_number" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>User type <span class="text-danger">*</span></label>
                                        <select name="user_type" class="form-control" required>
                                            <option value="">Select</option>
                                            <option value="super_admin" {{ !$isTrainersScope && !$isGymsScope ? '' : 'disabled' }}>Super admin</option>
                                            <option value="admin" {{ !$isTrainersScope && !$isGymsScope ? '' : 'disabled' }}>Admin</option>
                                            <option value="trainer" {{ $isTrainersScope ? 'selected' : '' }}>Trainer</option>
                                            <option value="user">User (customer)</option>
                                            <option value="gym" {{ $isGymsScope ? 'selected' : '' }}>Gym</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control" required>
                                            <option value="0">Pending</option>
                                            <option value="1" selected>Active</option>
                                            <option value="2">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Gender</label>
                                        <select name="gender" class="form-control">
                                            <option value="">—</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Height</label>
                                        <input type="text" name="height" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Height unit</label>
                                        <input type="text" name="height_parameter" class="form-control" placeholder="e.g. cm">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Weight</label>
                                        <input type="text" name="weight" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Weight unit</label>
                                        <input type="text" name="weight_parameter" class="form-control" placeholder="e.g. kg">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Password <span class="text-danger">*</span></label>
                                        <input type="password" name="password" class="form-control" required minlength="6">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Confirm password <span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" class="form-control" required minlength="6">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="editUserModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit User</h4>
                    </div>
                    <div class="modal-body">
                        <form id="editUserForm" class="m-t" role="form">
                            <input type="hidden" name="user_id" id="edit_user_id">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>First name <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" id="edit_first_name" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Last name <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" id="edit_last_name" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" id="edit_email" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Username</label>
                                        <input type="text" name="username" id="edit_username" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Mobile number</label>
                                        <input type="text" name="mobile_number" id="edit_mobile_number" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>User type <span class="text-danger">*</span></label>
                                        <select name="user_type" id="edit_user_type" class="form-control" required>
                                            <option value="super_admin">Super admin</option>
                                            <option value="admin">Admin</option>
                                            <option value="trainer">Trainer</option>
                                            <option value="user">User (customer)</option>
                                            <option value="gym">Gym</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Status <span class="text-danger">*</span></label>
                                        <select name="status" id="edit_status" class="form-control" required>
                                            <option value="0">Pending</option>
                                            <option value="1">Active</option>
                                            <option value="2">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Gender</label>
                                        <select name="gender" id="edit_gender" class="form-control">
                                            <option value="">—</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Height</label>
                                        <input type="text" name="height" id="edit_height" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Height unit</label>
                                        <input type="text" name="height_parameter" id="edit_height_parameter" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Weight</label>
                                        <input type="text" name="weight" id="edit_weight" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Weight unit</label>
                                        <input type="text" name="weight_parameter" id="edit_weight_parameter" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>New password</label>
                                        <input type="password" name="password" id="edit_password" class="form-control" placeholder="Leave blank to keep current" minlength="6">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Confirm new password</label>
                                        <input type="password" name="password_confirmation" id="edit_password_confirmation" class="form-control" minlength="6">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="viewUserModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">User details</h4>
                    </div>
                    <div class="modal-body">
                        <dl class="dl-horizontal" id="viewUserContent"></dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="row m-t-xs">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="usersTable">
                                <thead>
                                    <tr>
                                        <th>S.No.</th>
                                        <th>Joined</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Profile</th>
                                        <th>Mobile</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($users as $user)
                                        <tr data-user-id="{{ $user->id }}">
                                            <td>{{ $user->id }}</td>
                                            <td>{{ $user->created_at ? $user->created_at->format('d/m/Y') : '—' }}</td>
                                            <td>{{ trim($user->first_name . ' ' . $user->last_name) }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td><span class="label label-default">{{ str_replace('_', ' ', $user->user_type ?? '—') }}</span></td>
                                            <td>
                                                @if ($user->status === '1')
                                                    <span class="label label-primary">Active</span>
                                                @elseif ($user->status === '2')
                                                    <span class="label label-warning">Inactive</span>
                                                @else
                                                    <span class="label label-default">Pending</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if (optional($user->profile)->profile_picture)
                                                    <img alt="avatar" class="img-circle" style="width:36px;height:36px;object-fit:cover;"
                                                        src="{{ $user->profile->profile_picture }}">
                                                @else
                                                    <span class="text-muted">No image</span>
                                                @endif
                                            </td>
                                            <td>{{ $user->mobile_number ?? '—' }}</td>
                                            <td>
                                                <a href="#" class="btn-view text-navy" data-user-id="{{ $user->id }}" title="View"><i class="fa fa-eye"></i></a>
                                            </td>
                                            <td>
                                                <a href="#" class="btn-edit text-navy" data-user-id="{{ $user->id }}" title="Edit"><i class="fa fa-pencil"></i></a>
                                            </td>
                                            <td>
                                                <a href="#" class="btn-delete text-danger" data-user-id="{{ $user->id }}" title="Delete"><i class="fa fa-trash"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            function toastErrors(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.errors) {
                    var msgs = [];
                    $.each(xhr.responseJSON.errors, function (key, val) {
                        msgs = msgs.concat(val);
                    });
                    toastr.error(msgs.join('<br>'));
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error('Something went wrong.');
                }
            }

            $('#addUserModal').on('hidden.bs.modal', function () {
                $('#addUserForm')[0].reset();
            });

            $('#addUserForm').on('submit', function (e) {
                e.preventDefault();
                var $form = $(this);
                $.ajax({
                    url: "{{ route('admin.users.store') }}",
                    method: 'POST',
                    data: $form.serialize(),
                    success: function (res) {
                        toastr.success(res.message || 'Saved.');
                        $('#addUserModal').modal('hide');
                        window.location.reload();
                    },
                    error: function (xhr) {
                        toastErrors(xhr);
                    }
                });
            });

            function fillEditForm(user) {
                var p = user.profile || {};
                $('#edit_user_id').val(user.id);
                $('#edit_first_name').val(user.first_name || '');
                $('#edit_last_name').val(user.last_name || '');
                $('#edit_email').val(user.email || '');
                $('#edit_username').val(user.username || '');
                $('#edit_mobile_number').val(user.mobile_number || '');
                $('#edit_user_type').val(user.user_type || 'user');
                $('#edit_status').val(user.status != null ? String(user.status) : '1');
                $('#edit_gender').val(p.gender || '');
                $('#edit_height').val(p.height || '');
                $('#edit_height_parameter').val(p.height_parameter || '');
                $('#edit_weight').val(p.weight || '');
                $('#edit_weight_parameter').val(p.weight_parameter || '');
                $('#edit_password').val('');
                $('#edit_password_confirmation').val('');
            }

            $(document).on('click', '.btn-edit', function (e) {
                e.preventDefault();
                var id = $(this).data('user-id');
                $.get("{{ url('admin/users') }}/" + id, function (res) {
                    fillEditForm(res.user);
                    $('#editUserModal').modal('show');
                }).fail(function (xhr) {
                    toastErrors(xhr);
                });
            });

            $('#editUserForm').on('submit', function (e) {
                e.preventDefault();
                var id = $('#edit_user_id').val();
                var data = $(this).serialize();
                $.ajax({
                    url: "{{ url('admin/users') }}/" + id,
                    method: 'POST',
                    data: data + '&_method=PUT',
                    success: function (res) {
                        toastr.success(res.message || 'Updated.');
                        $('#editUserModal').modal('hide');
                        window.location.reload();
                    },
                    error: function (xhr) {
                        toastErrors(xhr);
                    }
                });
            });

            function statusLabel(status) {
                if (String(status) === '1') return '<span class="label label-primary">Active</span>';
                if (String(status) === '2') return '<span class="label label-warning">Inactive</span>';
                return '<span class="label label-default">Pending</span>';
            }

            $(document).on('click', '.btn-view', function (e) {
                e.preventDefault();
                var id = $(this).data('user-id');
                $.get("{{ url('admin/users') }}/" + id, function (res) {
                    var u = res.user;
                    var p = u.profile || {};
                    var name = $.trim((u.first_name || '') + ' ' + (u.last_name || ''));
                    var links = {
                        bookings: "{{ url('admin/bookings') }}" + '?user_id=' + encodeURIComponent(u.id),
                        payments: "{{ url('admin/payments') }}" + '?user_id=' + encodeURIComponent(u.id),
                        posts: "{{ url('admin/posts') }}" + '?user_id=' + encodeURIComponent(u.id),
                        plans: "{{ url('admin/workout-plans') }}" + '?user_id=' + encodeURIComponent(u.id),
                        logs: "{{ url('admin/workout-logs') }}" + '?user_id=' + encodeURIComponent(u.id),
                        meals: "{{ url('admin/nutrition/meals') }}" + '?user_id=' + encodeURIComponent(u.id),
                        targets: "{{ url('admin/nutrition/targets') }}" + '?user_id=' + encodeURIComponent(u.id)
                    };
                    var html = ''
                        + '<dt>ID</dt><dd>' + u.id + '</dd>'
                        + '<dt>Name</dt><dd>' + $('<div/>').text(name).html() + '</dd>'
                        + '<dt>Email</dt><dd>' + $('<div/>').text(u.email || '').html() + '</dd>'
                        + '<dt>Username</dt><dd>' + $('<div/>').text(u.username || '—').html() + '</dd>'
                        + '<dt>Mobile</dt><dd>' + $('<div/>').text(u.mobile_number || '—').html() + '</dd>'
                        + '<dt>Type</dt><dd>' + $('<div/>').text(u.user_type || '—').html() + '</dd>'
                        + '<dt>Status</dt><dd>' + statusLabel(u.status) + '</dd>'
                        + '<dt>Gender</dt><dd>' + $('<div/>').text(p.gender || '—').html() + '</dd>'
                        + '<dt>Height</dt><dd>' + $('<div/>').text((p.height || '—') + (p.height_parameter ? ' ' + p.height_parameter : '')).html() + '</dd>'
                        + '<dt>Weight</dt><dd>' + $('<div/>').text((p.weight || '—') + (p.weight_parameter ? ' ' + p.weight_parameter : '')).html() + '</dd>'
                        + '<dt>Joined</dt><dd>' + $('<div/>').text(u.created_at || '—').html() + '</dd>'
                        + '<dt>Quick links</dt><dd>'
                        + '<a class="btn btn-xs btn-white m-r-xs" href="' + links.bookings + '">Bookings</a>'
                        + '<a class="btn btn-xs btn-white m-r-xs" href="' + links.payments + '">Payments</a>'
                        + '<a class="btn btn-xs btn-white m-r-xs" href="' + links.posts + '">Posts</a>'
                        + '<a class="btn btn-xs btn-white m-r-xs" href="' + links.plans + '">Workout Plans</a>'
                        + '<a class="btn btn-xs btn-white m-r-xs" href="' + links.logs + '">Workout Logs</a>'
                        + '<a class="btn btn-xs btn-white m-r-xs" href="' + links.meals + '">Meals</a>'
                        + '<a class="btn btn-xs btn-white" href="' + links.targets + '">Targets</a>'
                        + '</dd>';
                    $('#viewUserContent').html(html);
                    $('#viewUserModal').modal('show');
                }).fail(function (xhr) {
                    toastErrors(xhr);
                });
            });

            $(document).on('click', '.btn-delete', function (e) {
                e.preventDefault();
                var id = $(this).data('user-id');
                if (!confirm('Delete this user? This cannot be undone.')) {
                    return;
                }
                $.ajax({
                    url: "{{ url('admin/users') }}/" + id,
                    method: 'POST',
                    data: { _method: 'DELETE' },
                    success: function (res) {
                        toastr.success(res.message || 'Deleted.');
                        window.location.reload();
                    },
                    error: function (xhr) {
                        toastErrors(xhr);
                    }
                });
            });
        });
    </script>
@endsection
