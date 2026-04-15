@extends('layouts.admin')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Payments</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Payments</strong></li>
            </ol>
        </div>
    </div>
    @php
        $hasActivePaymentFilters = filled(request('q')) ||
            filled(request('user_id')) ||
            filled(request('status')) ||
            filled(request('currency')) ||
            filled(request('created_from')) ||
            filled(request('created_to')) ||
            filled(request('amount_min')) ||
            filled(request('amount_max'));
    @endphp
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row m-b-sm">
            <div class="col-sm-6">
                <p class="text-muted m-b-none">Read-only list of Stripe payment records. Payments are created by app API and webhooks.</p>
            </div>
            <div class="col-sm-6 text-right">
                <button
                    type="button"
                    class="btn btn-default {{ $hasActivePaymentFilters ? '' : 'collapsed' }}"
                    data-toggle="collapse"
                    data-target="#paymentFiltersCollapse"
                    aria-expanded="{{ $hasActivePaymentFilters ? 'true' : 'false' }}"
                    id="togglePaymentFiltersBtn"
                >
                    <i class="fa fa-filter"></i> {{ $hasActivePaymentFilters ? 'Hide filters' : 'Show filters' }}
                </button>
            </div>
        </div>

        <div class="m-t-sm m-b-sm collapse {{ $hasActivePaymentFilters ? 'in' : '' }}" id="paymentFiltersCollapse">
            <div class="ibox m-t-sm">
                <div class="ibox-title" style="min-height: 44px;">
                    <h5>Filters</h5>
                </div>
                <div class="ibox-content">
                    <form method="GET" action="{{ route('admin.payments.index') }}" class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Search</label>
                                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Intent / customer / email / name">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>User</label>
                                <select name="user_id" class="form-control js-select2" data-placeholder="All users">
                                    <option value="">All</option>
                                    @foreach ($users as $u)
                                        <option value="{{ $u->id }}" {{ (string) request('user_id') === (string) $u->id ? 'selected' : '' }}>
                                            {{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control js-select2" data-placeholder="All statuses">
                                    <option value="">All</option>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>{{ $status }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Currency</label>
                                <select name="currency" class="form-control js-select2" data-placeholder="All">
                                    <option value="">All</option>
                                    @foreach ($currencies as $currency)
                                        <option value="{{ $currency }}" {{ strtolower((string) request('currency')) === strtolower((string) $currency) ? 'selected' : '' }}>
                                            {{ strtoupper($currency) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Amount min</label>
                                <input type="number" name="amount_min" value="{{ request('amount_min') }}" class="form-control" min="0" placeholder="0">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Amount max</label>
                                <input type="number" name="amount_max" value="{{ request('amount_max') }}" class="form-control" min="0" placeholder="10000">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>From</label>
                                <input type="text" name="created_from" value="{{ request('created_from') }}" class="form-control js-flatpickr-date" placeholder="YYYY-MM-DD">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>To</label>
                                <input type="text" name="created_to" value="{{ request('created_to') }}" class="form-control js-flatpickr-date" placeholder="YYYY-MM-DD">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>Per page</label>
                                <select name="per_page" class="form-control js-select2">
                                    @foreach ([10, 25, 50, 100] as $n)
                                        <option value="{{ $n }}" {{ (int) request('per_page', $perPage ?? 25) === $n ? 'selected' : '' }}>{{ $n }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group" style="margin-top: 24px;">
                                <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Apply filters</button>
                                <a href="{{ route('admin.payments.index') }}" class="btn btn-white m-l-xs"><i class="fa fa-refresh"></i> Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div id="viewPaymentModal" class="modal fade payment-detail-modal" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content payment-detail-modal-content">
                    <div class="modal-header payment-detail-modal__header">
                        <button type="button" class="close payment-detail-modal__close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title payment-detail-modal__title" id="viewPaymentModalTitle">Payment details</h4>
                    </div>
                    <div class="modal-body payment-detail-modal__body">
                        <div id="viewPaymentContent"></div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .payment-detail-modal .modal-dialog { max-width: 860px; margin: 24px auto; }
            .payment-detail-modal-content { border: 0; border-radius: 12px; overflow: hidden; box-shadow: 0 14px 38px rgba(0,0,0,.2); }
            .payment-detail-modal__header { border: 0; background: linear-gradient(125deg, #1f2937 0%, #374151 100%); color: #fff; }
            .payment-detail-modal__title { color: #fff; font-weight: 700; letter-spacing: -.01em; }
            .payment-detail-modal__close { color: #fff; opacity: .85; text-shadow: none; }
            .payment-detail-modal__body { background: #f3f5f8; max-height: min(78vh, 720px); overflow-y: auto; padding: 16px; }
            .payment-card { background: #fff; border: 1px solid #e5ebf0; border-radius: 10px; padding: 16px; margin-bottom: 12px; box-shadow: 0 2px 8px rgba(15, 35, 52, 0.04); }
            .payment-card h6 { margin: 0 0 10px; text-transform: uppercase; letter-spacing: .08em; color: #8a939d; font-size: 11px; }
            .payment-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px 18px; }
            .payment-row-label { color: #6b7280; font-size: 12px; text-transform: uppercase; letter-spacing: .06em; }
            .payment-row-value { color: #1f2937; font-weight: 600; margin-top: 2px; word-break: break-word; }
            .payment-chip { display: inline-block; padding: 4px 10px; border-radius: 100px; font-size: 12px; font-weight: 600; }
            .payment-chip--ok { background: #d1fae5; color: #065f46; }
            .payment-chip--pending { background: #fef3c7; color: #92400e; }
            .payment-chip--failed { background: #fee2e2; color: #991b1b; }
            .payment-json { white-space: pre-wrap; font-size: 12px; color: #4b5563; background: #f9fafb; border: 1px solid #e5e7eb; border-radius: 8px; padding: 12px; }
        </style>

        <div class="row m-t-xs">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="m-b-sm text-muted">
                            Showing <strong>{{ $payments->firstItem() ?? 0 }}</strong> to
                            <strong>{{ $payments->lastItem() ?? 0 }}</strong> of
                            <strong>{{ $payments->total() }}</strong> payments.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Created</th>
                                        <th>Updated</th>
                                        <th>User</th>
                                        <th>Email</th>
                                        <th>Intent</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $p)
                                        <tr>
                                            <td>{{ $p->id }}</td>
                                            <td>
                                                @if($p->created_at)
                                                    <div>{{ $p->created_at->format('d M Y, h:i A') }}</div>
                                                    <small class="text-muted">{{ $p->created_at->diffForHumans() }}</small>
                                                @else — @endif
                                            </td>
                                            <td>
                                                @if($p->updated_at)
                                                    <div>{{ $p->updated_at->format('d M Y, h:i A') }}</div>
                                                    <small class="text-muted">{{ $p->updated_at->diffForHumans() }}</small>
                                                @else — @endif
                                            </td>
                                            <td>{{ optional($p->user)->first_name }} {{ optional($p->user)->last_name }}</td>
                                            <td>{{ $p->email }}</td>
                                            <td><small>{{ \Illuminate\Support\Str::limit($p->payment_intent_id, 24) }}</small></td>
                                            <td><span class="label label-default">{{ $p->status }}</span></td>
                                            <td>{{ $p->amount }} {{ strtoupper($p->currency) }}</td>
                                            <td><a href="#" class="btn-view-payment text-navy" data-id="{{ $p->id }}"><i class="fa fa-eye"></i></a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $payments->appends(request()->query())->links() }}
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

            function updatePaymentFilterBtnText(isOpen) {
                $('#togglePaymentFiltersBtn').html(
                    '<i class="fa fa-filter"></i> ' + (isOpen ? 'Hide filters' : 'Show filters')
                );
            }

            updatePaymentFilterBtnText($('#paymentFiltersCollapse').hasClass('in'));
            $('#paymentFiltersCollapse')
                .on('shown.bs.collapse', function () { updatePaymentFilterBtnText(true); })
                .on('hidden.bs.collapse', function () { updatePaymentFilterBtnText(false); });

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            function toastErrors(xhr) {
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error('Request failed.');
                }
            }

            function asText(v) {
                return v == null || v === '' ? '—' : String(v);
            }

            function statusChip(status) {
                var s = String(status || '').toLowerCase();
                var cls = 'payment-chip--pending';
                if (s.indexOf('succeed') >= 0 || s === 'paid') cls = 'payment-chip--ok';
                if (s.indexOf('fail') >= 0 || s.indexOf('cancel') >= 0) cls = 'payment-chip--failed';
                return $('<span class="payment-chip"/>').addClass(cls).text(s || 'unknown');
            }

            function renderRow(label, valueEl) {
                var $wrap = $('<div/>');
                $wrap.append($('<div class="payment-row-label"/>').text(label));
                var $val = $('<div class="payment-row-value"/>');
                if (valueEl && valueEl.jquery) {
                    $val.append(valueEl);
                } else {
                    $val.text(asText(valueEl));
                }
                $wrap.append($val);
                return $wrap;
            }

            $(document).on('click', '.btn-view-payment', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                $.get("{{ url('admin/payments') }}/" + id, function (res) {
                    var p = res.payment;
                    $('#viewPaymentModalTitle').text('Payment #' + p.id);

                    var $root = $('<div/>');
                    var $main = $('<div class="payment-card"/>');
                    $main.append('<h6>Payment overview</h6>');
                    var $grid = $('<div class="payment-grid"/>');
                    $grid.append(renderRow('Payment intent', p.payment_intent_id));
                    $grid.append(renderRow('Status', statusChip(p.status)));
                    $grid.append(renderRow('Amount', (p.amount != null ? p.amount : '—') + ' ' + String(p.currency || '').toUpperCase()));
                    $grid.append(renderRow('Customer ID', p.customer_id));
                    $grid.append(renderRow('Email', p.email));
                    $grid.append(renderRow('Name', p.name));
                    $grid.append(renderRow('Method', p.payment_method));
                    $grid.append(renderRow('Created', p.created_at));
                    $grid.append(renderRow('Updated', p.updated_at));
                    $main.append($grid);
                    $root.append($main);

                    var userName = p.user ? [p.user.first_name, p.user.last_name].join(' ').trim() : '—';
                    var userEmail = p.user ? p.user.email : '—';
                    var $usr = $('<div class="payment-card"/>');
                    $usr.append('<h6>User</h6>');
                    var $ugrid = $('<div class="payment-grid"/>');
                    $ugrid.append(renderRow('User ID', p.user_id));
                    $ugrid.append(renderRow('Name', userName || '—'));
                    $ugrid.append(renderRow('Email', userEmail));
                    $usr.append($ugrid);
                    $root.append($usr);

                    if (p.response_data) {
                        var $json = $('<div class="payment-card"/>');
                        $json.append('<h6>Gateway response</h6>');
                        $json.append($('<div class="payment-json"/>').text(JSON.stringify(p.response_data, null, 2)));
                        $root.append($json);
                    }

                    $('#viewPaymentContent').empty().append($root);
                    $('#viewPaymentModal').modal('show');
                }).fail(toastErrors);
            });
        });
    </script>
@endsection
