@extends('layouts.admin')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Bookings</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Bookings</strong></li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <button type="button" class="btn btn-primary m-b-sm" data-toggle="modal" data-target="#addBookingModal">
            <i class="fa fa-plus"></i> Add booking
        </button>

        <div id="addBookingModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Add booking</h4>
                    </div>
                    <div class="modal-body">
                        <form id="addBookingForm" class="m-t">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Customer <span class="text-danger">*</span></label>
                                        <select name="user_id" class="form-control" required>
                                            <option value="">—</option>
                                            @foreach ($customers as $u)
                                                <option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Session <span class="text-danger">*</span></label>
                                        <select name="session_id" class="form-control" required>
                                            <option value="">—</option>
                                            @foreach ($sessions as $s)
                                                <option value="{{ $s->id }}">#{{ $s->id }} {{ $s->session_title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Trainer</label>
                                        <select name="trainer_id" class="form-control">
                                            <option value="">—</option>
                                            @foreach ($trainers as $t)
                                                <option value="{{ $t->id }}">{{ $t->first_name }} {{ $t->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Gym</label>
                                        <select name="gym_id" class="form-control">
                                            <option value="">—</option>
                                            @foreach ($gyms as $g)
                                                <option value="{{ $g->id }}">{{ $g->first_name }} {{ $g->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Booking date <span class="text-danger">*</span></label>
                                        <input type="date" name="booking_date" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Time slot <span class="text-danger">*</span></label>
                                        <input type="text" name="time_slot" class="form-control" placeholder="e.g. 10:00 - 11:00" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control" required>
                                            <option value="2">Pending</option>
                                            <option value="1" selected>Confirmed</option>
                                            <option value="0">Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Payment status <span class="text-danger">*</span></label>
                                        <select name="payment_status" class="form-control" required>
                                            <option value="pending" selected>Pending</option>
                                            <option value="paid">Paid</option>
                                            <option value="failed">Failed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Payment intent ID</label>
                                        <input type="text" name="payment_id" class="form-control" placeholder="Optional Stripe id">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="editBookingModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Edit booking</h4>
                    </div>
                    <div class="modal-body">
                        <form id="editBookingForm" class="m-t">
                            <input type="hidden" name="booking_id" id="edit_booking_id">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Customer <span class="text-danger">*</span></label>
                                        <select name="user_id" id="edit_booking_user_id" class="form-control" required>
                                            @foreach ($customers as $u)
                                                <option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Session <span class="text-danger">*</span></label>
                                        <select name="session_id" id="edit_booking_session_id" class="form-control" required>
                                            @foreach ($sessions as $s)
                                                <option value="{{ $s->id }}">#{{ $s->id }} {{ $s->session_title }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Trainer</label>
                                        <select name="trainer_id" id="edit_booking_trainer_id" class="form-control">
                                            <option value="">—</option>
                                            @foreach ($trainers as $t)
                                                <option value="{{ $t->id }}">{{ $t->first_name }} {{ $t->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Gym</label>
                                        <select name="gym_id" id="edit_booking_gym_id" class="form-control">
                                            <option value="">—</option>
                                            @foreach ($gyms as $g)
                                                <option value="{{ $g->id }}">{{ $g->first_name }} {{ $g->last_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Booking date <span class="text-danger">*</span></label>
                                        <input type="date" name="booking_date" id="edit_booking_date" class="form-control" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Time slot <span class="text-danger">*</span></label>
                                        <input type="text" name="time_slot" id="edit_booking_time_slot" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Status <span class="text-danger">*</span></label>
                                        <select name="status" id="edit_booking_status" class="form-control" required>
                                            <option value="2">Pending</option>
                                            <option value="1">Confirmed</option>
                                            <option value="0">Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Payment status <span class="text-danger">*</span></label>
                                        <select name="payment_status" id="edit_booking_payment_status" class="form-control" required>
                                            <option value="pending">Pending</option>
                                            <option value="paid">Paid</option>
                                            <option value="failed">Failed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Payment intent ID</label>
                                        <input type="text" name="payment_id" id="edit_booking_payment_id" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Update</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="viewBookingModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Booking details</h4>
                    </div>
                    <div class="modal-body">
                        <pre id="viewBookingContent" class="small" style="white-space:pre-wrap;max-height:420px;overflow:auto;"></pre>
                    </div>
                </div>
            </div>
        </div>

        <div class="row m-t-xs">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Date</th>
                                        <th>Customer</th>
                                        <th>Session</th>
                                        <th>Status</th>
                                        <th>Pay</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bookings as $b)
                                        <tr>
                                            <td>{{ $b->id }}</td>
                                            <td>{{ $b->booking_date }}<br><small class="text-muted">{{ $b->time_slot }}</small></td>
                                            <td>{{ optional($b->user)->first_name }} {{ optional($b->user)->last_name }}</td>
                                            <td>{{ optional($b->session)->session_title ?? '—' }}</td>
                                            <td>
                                                @if ($b->status === '1')
                                                    <span class="label label-primary">Confirmed</span>
                                                @elseif ($b->status === '0')
                                                    <span class="label label-danger">Cancelled</span>
                                                @else
                                                    <span class="label label-warning">Pending</span>
                                                @endif
                                            </td>
                                            <td><span class="label label-default">{{ $b->payment_status }}</span></td>
                                            <td><a href="#" class="btn-view-booking text-navy" data-id="{{ $b->id }}"><i class="fa fa-eye"></i></a></td>
                                            <td><a href="#" class="btn-edit-booking text-navy" data-id="{{ $b->id }}"><i class="fa fa-pencil"></i></a></td>
                                            <td><a href="#" class="btn-delete-booking text-danger" data-id="{{ $b->id }}"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $bookings->links() }}
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
                    $.each(xhr.responseJSON.errors, function (k, v) { msgs = msgs.concat(v); });
                    toastr.error(msgs.join('<br>'));
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error('Request failed.');
                }
            }

            $('#addBookingForm').on('submit', function (e) {
                e.preventDefault();
                $.ajax({
                    url: "{{ route('admin.bookings.store') }}",
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function (res) {
                        toastr.success(res.message || 'Saved.');
                        $('#addBookingModal').modal('hide');
                        window.location.reload();
                    },
                    error: toastErrors
                });
            });

            $(document).on('click', '.btn-edit-booking', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                $.get("{{ url('admin/bookings') }}/" + id, function (res) {
                    var b = res.booking;
                    $('#edit_booking_id').val(b.id);
                    $('#edit_booking_user_id').val(String(b.user_id));
                    $('#edit_booking_session_id').val(String(b.session_id));
                    $('#edit_booking_trainer_id').val(b.trainer_id ? String(b.trainer_id) : '');
                    $('#edit_booking_gym_id').val(b.gym_id ? String(b.gym_id) : '');
                    $('#edit_booking_date').val((b.booking_date || '').toString().substring(0, 10));
                    $('#edit_booking_time_slot').val(b.time_slot || '');
                    $('#edit_booking_status').val(String(b.status));
                    $('#edit_booking_payment_status').val(b.payment_status || 'pending');
                    $('#edit_booking_payment_id').val(b.payment_id || '');
                    $('#editBookingModal').modal('show');
                }).fail(toastErrors);
            });

            $('#editBookingForm').on('submit', function (e) {
                e.preventDefault();
                var id = $('#edit_booking_id').val();
                $.ajax({
                    url: "{{ url('admin/bookings') }}/" + id,
                    method: 'POST',
                    data: $(this).serialize() + '&_method=PUT',
                    success: function (res) {
                        toastr.success(res.message || 'Updated.');
                        $('#editBookingModal').modal('hide');
                        window.location.reload();
                    },
                    error: toastErrors
                });
            });

            $(document).on('click', '.btn-view-booking', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                $.get("{{ url('admin/bookings') }}/" + id, function (res) {
                    $('#viewBookingContent').text(JSON.stringify(res.booking, null, 2));
                    $('#viewBookingModal').modal('show');
                }).fail(toastErrors);
            });

            $(document).on('click', '.btn-delete-booking', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                if (!confirm('Delete this booking?')) return;
                $.ajax({
                    url: "{{ url('admin/bookings') }}/" + id,
                    method: 'POST',
                    data: {_method: 'DELETE'},
                    success: function (res) {
                        toastr.success(res.message || 'Deleted.');
                        window.location.reload();
                    },
                    error: toastErrors
                });
            });
        });
    </script>
@endsection
