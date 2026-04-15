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
    @php
        $hasActiveBookingFilters = filled(request('q')) ||
            filled(request('user_id')) ||
            filled(request('session_id')) ||
            filled(request('status')) ||
            filled(request('payment_status')) ||
            filled(request('booking_from')) ||
            filled(request('booking_to'));
    @endphp
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row m-b-sm">
            <div class="col-sm-6">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addBookingModal">
                    <i class="fa fa-plus"></i> Add booking
                </button>
            </div>
            <div class="col-sm-6 text-right">
                <button
                    type="button"
                    class="btn btn-default {{ $hasActiveBookingFilters ? '' : 'collapsed' }}"
                    data-toggle="collapse"
                    data-target="#bookingFiltersCollapse"
                    aria-expanded="{{ $hasActiveBookingFilters ? 'true' : 'false' }}"
                    id="toggleBookingFiltersBtn"
                >
                    <i class="fa fa-filter"></i> {{ $hasActiveBookingFilters ? 'Hide filters' : 'Show filters' }}
                </button>
            </div>
        </div>

        <div class="m-t-sm m-b-sm collapse {{ $hasActiveBookingFilters ? 'in' : '' }}" id="bookingFiltersCollapse">
            <div class="ibox m-t-sm">
                <div class="ibox-title" style="min-height: 44px;">
                    <h5>Filters</h5>
                </div>
                <div class="ibox-content">
                    <form method="GET" action="{{ route('admin.bookings.index') }}" class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Time slot or payment id">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Customer</label>
                                <select name="user_id" class="form-control js-select2" data-placeholder="All customers">
                                    <option value="">All</option>
                                    @foreach ($customers as $u)
                                        <option value="{{ $u->id }}" {{ (string) request('user_id') === (string) $u->id ? 'selected' : '' }}>
                                            {{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Session</label>
                                <select name="session_id" class="form-control js-select2" data-placeholder="All sessions">
                                    <option value="">All</option>
                                    @foreach ($sessions as $s)
                                        <option value="{{ $s->id }}" {{ (string) request('session_id') === (string) $s->id ? 'selected' : '' }}>
                                            #{{ $s->id }} {{ $s->session_title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Per page</label>
                                <select name="per_page" class="form-control js-select2" data-placeholder="Per page">
                                    @foreach ([10, 20, 50, 100] as $n)
                                        <option value="{{ $n }}" {{ (int) request('per_page', $perPage ?? 20) === $n ? 'selected' : '' }}>{{ $n }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control js-select2" data-placeholder="All">
                                    <option value="">All</option>
                                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="2" {{ request('status') === '2' ? 'selected' : '' }}>Pending</option>
                                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Payment status</label>
                                <select name="payment_status" class="form-control js-select2" data-placeholder="All">
                                    <option value="">All</option>
                                    <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Booking from</label>
                                <input type="text" name="booking_from" value="{{ request('booking_from') }}" class="form-control js-flatpickr-date" placeholder="YYYY-MM-DD">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Booking to</label>
                                <input type="text" name="booking_to" value="{{ request('booking_to') }}" class="form-control js-flatpickr-date" placeholder="YYYY-MM-DD">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Apply filters</button>
                                <a href="{{ route('admin.bookings.index') }}" class="btn btn-white m-l-xs"><i class="fa fa-refresh"></i> Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

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
                                        <select name="user_id" class="form-control js-select2" data-placeholder="Select customer" required>
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
                                        <select name="session_id" class="form-control js-select2" data-placeholder="Select session" required>
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
                                        <select name="trainer_id" class="form-control js-select2" data-placeholder="Select trainer">
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
                                        <select name="gym_id" class="form-control js-select2" data-placeholder="Select gym">
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
                                        <input type="text" name="booking_date" class="form-control js-flatpickr-date" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Start time <span class="text-danger">*</span></label>
                                        <input type="text" name="start_time" class="form-control js-flatpickr-time" placeholder="e.g. 10:00am" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>End time <span class="text-danger">*</span></label>
                                        <input type="text" name="end_time" class="form-control js-flatpickr-time" placeholder="e.g. 11:00am" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Status <span class="text-danger">*</span></label>
                                        <select name="status" class="form-control js-select2" data-placeholder="Select status" required>
                                            <option value="2">Pending</option>
                                            <option value="1" selected>Confirmed</option>
                                            <option value="0">Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Payment status <span class="text-danger">*</span></label>
                                        <select name="payment_status" class="form-control js-select2" data-placeholder="Select payment status" required>
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
                                        <select name="user_id" id="edit_booking_user_id" class="form-control js-select2" data-placeholder="Select customer" required>
                                            @foreach ($customers as $u)
                                                <option value="{{ $u->id }}">{{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label>Session <span class="text-danger">*</span></label>
                                        <select name="session_id" id="edit_booking_session_id" class="form-control js-select2" data-placeholder="Select session" required>
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
                                        <select name="trainer_id" id="edit_booking_trainer_id" class="form-control js-select2" data-placeholder="Select trainer">
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
                                        <select name="gym_id" id="edit_booking_gym_id" class="form-control js-select2" data-placeholder="Select gym">
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
                                        <input type="text" name="booking_date" id="edit_booking_date" class="form-control js-flatpickr-date" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>Start time <span class="text-danger">*</span></label>
                                        <input type="text" name="start_time" id="edit_booking_start_time" class="form-control js-flatpickr-time" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>End time <span class="text-danger">*</span></label>
                                        <input type="text" name="end_time" id="edit_booking_end_time" class="form-control js-flatpickr-time" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Status <span class="text-danger">*</span></label>
                                        <select name="status" id="edit_booking_status" class="form-control js-select2" data-placeholder="Select status" required>
                                            <option value="2">Pending</option>
                                            <option value="1">Confirmed</option>
                                            <option value="0">Cancelled</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label>Payment status <span class="text-danger">*</span></label>
                                        <select name="payment_status" id="edit_booking_payment_status" class="form-control js-select2" data-placeholder="Select payment status" required>
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

        <div id="viewBookingModal" class="modal fade booking-detail-modal" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content booking-detail-modal-content">
                    <div class="modal-header booking-detail-modal__header">
                        <button type="button" class="close booking-detail-modal__close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title booking-detail-modal__title" id="viewBookingModalTitle">Booking details</h4>
                    </div>
                    <div class="modal-body booking-detail-modal__body">
                        <div id="viewBookingContent"></div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .booking-detail-modal .modal-dialog { max-width: 860px; margin: 24px auto; }
            .booking-detail-modal-content { border: 0; border-radius: 12px; overflow: hidden; box-shadow: 0 14px 38px rgba(0,0,0,.2); }
            .booking-detail-modal__header { border: 0; background: linear-gradient(125deg, #1f2937 0%, #374151 100%); color: #fff; }
            .booking-detail-modal__title { color: #fff; font-weight: 700; letter-spacing: -.01em; }
            .booking-detail-modal__close { color: #fff; opacity: .85; text-shadow: none; }
            .booking-detail-modal__body { background: #f3f5f8; max-height: min(78vh, 720px); overflow-y: auto; padding: 16px; }
            .booking-card { background: #fff; border: 1px solid #e5ebf0; border-radius: 10px; padding: 16px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(15, 35, 52, 0.04); }
            .booking-card h6 { margin: 0 0 10px; text-transform: uppercase; letter-spacing: .08em; color: #8a939d; font-size: 11px; }
            .booking-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px 18px; }
            .booking-row-label { color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: .06em; }
            .booking-row-value { color: #1f2937; font-weight: 600; margin-top: 2px; word-break: break-word; }
            .booking-chip { display: inline-block; padding: 4px 10px; border-radius: 100px; font-size: 12px; font-weight: 600; }
            .booking-chip--ok { background: #d1fae5; color: #065f46; }
            .booking-chip--pending { background: #fef3c7; color: #92400e; }
            .booking-chip--failed { background: #fee2e2; color: #991b1b; }
            .booking-chip--neutral { background: #e5e7eb; color: #374151; }
        </style>

        <div class="row m-t-xs">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="m-b-sm text-muted">
                            Showing <strong>{{ $bookings->firstItem() ?? 0 }}</strong> to
                            <strong>{{ $bookings->lastItem() ?? 0 }}</strong> of
                            <strong>{{ $bookings->total() }}</strong> bookings.
                        </div>
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
                                        <th>Created</th>
                                        <th>Updated</th>
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
                                            <td>
                                                @if($b->created_at)
                                                    <div>{{ $b->created_at->format('d M Y, h:i A') }}</div>
                                                    <small class="text-muted">{{ $b->created_at->diffForHumans() }}</small>
                                                @else — @endif
                                            </td>
                                            <td>
                                                @if($b->updated_at)
                                                    <div>{{ $b->updated_at->format('d M Y, h:i A') }}</div>
                                                    <small class="text-muted">{{ $b->updated_at->diffForHumans() }}</small>
                                                @else — @endif
                                            </td>
                                            <td><a href="#" class="btn-view-booking text-navy" data-id="{{ $b->id }}"><i class="fa fa-eye"></i></a></td>
                                            <td><a href="#" class="btn-edit-booking text-navy" data-id="{{ $b->id }}"><i class="fa fa-pencil"></i></a></td>
                                            <td><a href="#" class="btn-delete-booking text-danger" data-id="{{ $b->id }}"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $bookings->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        $(function () {
            window.initUiEnhancements(document);

            function updateBookingFilterBtnText(isOpen) {
                $('#toggleBookingFiltersBtn').html(
                    '<i class="fa fa-filter"></i> ' + (isOpen ? 'Hide filters' : 'Show filters')
                );
            }

            updateBookingFilterBtnText($('#bookingFiltersCollapse').hasClass('in'));
            $('#bookingFiltersCollapse')
                .on('shown.bs.collapse', function () { updateBookingFilterBtnText(true); })
                .on('hidden.bs.collapse', function () { updateBookingFilterBtnText(false); });

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

            function asText(v) {
                return v == null || v === '' ? '—' : String(v);
            }

            function bookingStatusChip(status) {
                var s = String(status || '');
                if (s === '1') return $('<span class="booking-chip booking-chip--ok"/>').text('Confirmed');
                if (s === '0') return $('<span class="booking-chip booking-chip--failed"/>').text('Cancelled');
                return $('<span class="booking-chip booking-chip--pending"/>').text('Pending');
            }

            function paymentStatusChip(status) {
                var s = String(status || '').toLowerCase();
                if (s === 'paid' || s.indexOf('succeed') >= 0) return $('<span class="booking-chip booking-chip--ok"/>').text('Paid');
                if (s === 'failed' || s.indexOf('fail') >= 0 || s.indexOf('cancel') >= 0) return $('<span class="booking-chip booking-chip--failed"/>').text('Failed');
                return $('<span class="booking-chip booking-chip--pending"/>').text('Pending');
            }

            function renderRow(label, valueEl) {
                var $wrap = $('<div/>');
                $wrap.append($('<div class="booking-row-label"/>').text(label));
                var $val = $('<div class="booking-row-value"/>');
                if (valueEl && valueEl.jquery) {
                    $val.append(valueEl);
                } else {
                    $val.text(asText(valueEl));
                }
                $wrap.append($val);
                return $wrap;
            }

            $('#addBookingModal').on('shown.bs.modal', function () {
                window.initUiEnhancements(this);
            });

            $('#addBookingModal').on('hidden.bs.modal', function () {
                $('#addBookingForm')[0].reset();
                $(this).find('select.js-select2').val(null).trigger('change');
            });

            $('#editBookingModal').on('shown.bs.modal', function () {
                window.initUiEnhancements(this);
            });

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
                    var slot = (b.time_slot || '').split(' - ');
                    $('#edit_booking_start_time').val(slot[0] || '');
                    $('#edit_booking_end_time').val(slot[1] || '');
                    $('#edit_booking_status').val(String(b.status));
                    $('#edit_booking_payment_status').val(b.payment_status || 'pending');
                    $('#edit_booking_payment_id').val(b.payment_id || '');
                    $('#edit_booking_user_id, #edit_booking_session_id, #edit_booking_trainer_id, #edit_booking_gym_id, #edit_booking_status, #edit_booking_payment_status').trigger('change');
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
                    var b = res.booking;
                    $('#viewBookingModalTitle').text('Booking #' + b.id);

                    var customerName = b.user ? [b.user.first_name, b.user.last_name].join(' ').trim() : '—';
                    var trainerName = b.trainer ? [b.trainer.first_name, b.trainer.last_name].join(' ').trim() : '—';
                    var gymName = b.gym ? [b.gym.first_name, b.gym.last_name].join(' ').trim() : '—';
                    var sessionTitle = b.session ? b.session.session_title : '—';

                    var $root = $('<div/>');

                    var $overview = $('<div class="booking-card"/>');
                    $overview.append('<h6>Booking overview</h6>');
                    var $grid = $('<div class="booking-grid"/>');
                    $grid.append(renderRow('Booking ID', b.id));
                    $grid.append(renderRow('Date', b.booking_date));
                    $grid.append(renderRow('Time slot', b.time_slot));
                    $grid.append(renderRow('Status', bookingStatusChip(b.status)));
                    $grid.append(renderRow('Payment status', paymentStatusChip(b.payment_status)));
                    $grid.append(renderRow('Payment intent', b.payment_id));
                    $grid.append(renderRow('Created', b.created_at));
                    $grid.append(renderRow('Updated', b.updated_at));
                    $overview.append($grid);
                    $root.append($overview);

                    var $people = $('<div class="booking-card"/>');
                    $people.append('<h6>People</h6>');
                    var $pgrid = $('<div class="booking-grid"/>');
                    $pgrid.append(renderRow('Customer', customerName || '—'));
                    $pgrid.append(renderRow('Customer email', b.user ? b.user.email : '—'));
                    $pgrid.append(renderRow('Trainer', trainerName || '—'));
                    $pgrid.append(renderRow('Gym', gymName || '—'));
                    $people.append($pgrid);
                    $root.append($people);

                    var $session = $('<div class="booking-card"/>');
                    $session.append('<h6>Session</h6>');
                    var $sgrid = $('<div class="booking-grid"/>');
                    $sgrid.append(renderRow('Session ID', b.session_id));
                    $sgrid.append(renderRow('Session title', sessionTitle));
                    $session.append($sgrid);
                    $root.append($session);

                    if (b.payment) {
                        var $payment = $('<div class="booking-card"/>');
                        $payment.append('<h6>Payment record</h6>');
                        var $payGrid = $('<div class="booking-grid"/>');
                        $payGrid.append(renderRow('Payment row ID', b.payment.id));
                        $payGrid.append(renderRow('Gateway status', paymentStatusChip(b.payment.status)));
                        $payGrid.append(renderRow('Amount', (b.payment.amount != null ? b.payment.amount : '—') + ' ' + String(b.payment.currency || '').toUpperCase()));
                        $payGrid.append(renderRow('Email', b.payment.email));
                        $payment.append($payGrid);
                        $root.append($payment);
                    }

                    $('#viewBookingContent').empty().append($root);
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
