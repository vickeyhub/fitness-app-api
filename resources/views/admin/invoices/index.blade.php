@extends('layouts.admin')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Invoices</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Invoices</strong></li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-sm-12 text-right m-b-sm">
                <a href="{{ route('admin.invoices.settings') }}" class="btn btn-default"><i class="fa fa-cog"></i> Invoice settings</a>
            </div>
        </div>
        <div class="ibox">
            <div class="ibox-content">
                <form method="GET" action="{{ route('admin.invoices.index') }}" class="row">
                    <div class="col-md-3"><div class="form-group"><label>Search</label><input name="q" value="{{ request('q') }}" class="form-control" placeholder="Invoice # / payment intent"></div></div>
                    <div class="col-md-2"><div class="form-group"><label>Status</label><select name="status" class="form-control js-select2"><option value="">All</option>@foreach($statuses as $s)<option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>@endforeach</select></div></div>
                    <div class="col-md-3"><div class="form-group"><label>User</label><select name="user_id" class="form-control js-select2"><option value="">All</option>@foreach($users as $u)<option value="{{ $u->id }}" {{ (string)request('user_id') === (string)$u->id ? 'selected' : '' }}>{{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}</option>@endforeach</select></div></div>
                    <div class="col-md-1"><div class="form-group"><label>Currency</label><select name="currency" class="form-control js-select2"><option value="">All</option>@foreach($currencies as $c)<option value="{{ $c }}" {{ strtolower((string)request('currency')) === strtolower((string)$c) ? 'selected' : '' }}>{{ strtoupper($c) }}</option>@endforeach</select></div></div>
                    <div class="col-md-1"><div class="form-group"><label>From</label><input name="created_from" value="{{ request('created_from') }}" class="form-control js-flatpickr-date"></div></div>
                    <div class="col-md-1"><div class="form-group"><label>To</label><input name="created_to" value="{{ request('created_to') }}" class="form-control js-flatpickr-date"></div></div>
                    <div class="col-md-1"><div class="form-group"><label>Per</label><select name="per_page" class="form-control">@foreach([10,15,25,50,100] as $pp)<option value="{{ $pp }}" {{ (int)request('per_page', 15) === $pp ? 'selected' : '' }}>{{ $pp }}</option>@endforeach</select></div></div>
                    <div class="col-md-12 text-right"><button class="btn btn-primary"><i class="fa fa-search"></i> Apply</button> <a href="{{ route('admin.invoices.index') }}" class="btn btn-white">Reset</a></div>
                </form>
            </div>
        </div>

        <div class="ibox">
            <div class="ibox-content table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                    <tr><th>#</th><th>Invoice #</th><th>User</th><th>Booking</th><th>Payment Intent</th><th>Status</th><th>Total</th><th>Issued</th><th>Created</th><th>Updated</th><th></th></tr>
                    </thead>
                    <tbody>
                    @forelse($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->id }}</td>
                            <td><strong>{{ $invoice->invoice_number }}</strong></td>
                            <td>{{ optional($invoice->user)->first_name }} {{ optional($invoice->user)->last_name }}</td>
                            <td>{{ $invoice->booking_id ? '#'.$invoice->booking_id : '—' }}</td>
                            <td>{{ $invoice->payment_intent_id ?: '—' }}</td>
                            <td><span class="label label-default">{{ ucfirst($invoice->status) }}</span></td>
                            <td>{{ number_format((float)$invoice->total, 2) }} {{ strtoupper((string)$invoice->currency) }}</td>
                            <td>{{ $invoice->issued_at ? $invoice->issued_at->format('d M Y, h:i A') : '—' }}</td>
                            <td>@if($invoice->created_at)<div>{{ $invoice->created_at->format('d M Y, h:i A') }}</div><small class="text-muted">{{ $invoice->created_at->diffForHumans() }}</small>@else — @endif</td>
                            <td>@if($invoice->updated_at)<div>{{ $invoice->updated_at->format('d M Y, h:i A') }}</div><small class="text-muted">{{ $invoice->updated_at->diffForHumans() }}</small>@else — @endif</td>
                            <td><button class="btn btn-xs btn-info js-view-invoice" data-id="{{ $invoice->id }}"><i class="fa fa-eye"></i> View</button></td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="text-center text-muted">No invoices found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
                <div class="text-right">{{ $invoices->links() }}</div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="invoiceViewModal" tabindex="-1">
        <div class="modal-dialog modal-lg"><div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title" id="invoiceViewTitle">Invoice</h4>
            </div>
            <div class="modal-body" id="invoiceViewBody"></div>
        </div></div>
    </div>
@endsection

@section('scripts')
<script>
$(function () {
    window.initUiEnhancements(document);

    function esc(value) {
        if (value === null || value === undefined || value === '') return '—';
        return $('<div/>').text(String(value)).html();
    }

    function money(value, currency) {
        var amount = Number(value || 0).toFixed(2);
        return esc(amount + ' ' + String(currency || '').toUpperCase());
    }

    function fmtDate(value) {
        if (!value) return '—';
        var d = new Date(value);
        if (isNaN(d.getTime())) return esc(value);
        return esc(d.toLocaleString());
    }

    $(document).on('click', '.js-view-invoice', function () {
        var id = $(this).data('id');
        $.get("{{ url('admin/invoices') }}/" + id, function (res) {
            var i = res.invoice || {};
            var snap = i.snapshot || {};
            var snapBooking = snap.booking || {};
            var snapSession = snap.session || {};
            var snapUser = snap.user || {};
            var snapPayment = snap.payment || {};
            var userName = i.user ? ((i.user.first_name || '') + ' ' + (i.user.last_name || '')).trim() : (snapUser.name || '');

            $('#invoiceViewTitle').text((i.invoice_number || 'Invoice') + ' (ID #' + i.id + ')');
            var html = ''
                + '<div class="row">'
                + '  <div class="col-sm-7">'
                + '    <h4 style="margin-top:0;">' + esc(i.invoice_number) + '</h4>'
                + '    <div class="text-muted">Issued: ' + fmtDate(i.issued_at || i.created_at) + '</div>'
                + '    <div class="text-muted">Due: ' + fmtDate(i.due_at) + '</div>'
                + '    <div class="text-muted">Status: <span class="label label-default">' + esc(i.status || 'issued') + '</span></div>'
                + '  </div>'
                + '  <div class="col-sm-5 text-right">'
                + '    <h3 style="margin-top:0;margin-bottom:4px;">' + money(i.total, i.currency) + '</h3>'
                + '    <small class="text-muted">Invoice Total</small>'
                + '  </div>'
                + '</div>'
                + '<hr>'
                + '<div class="row">'
                + '  <div class="col-sm-6">'
                + '    <h5>Bill To</h5>'
                + '    <p style="margin-bottom:4px;"><strong>' + esc(userName) + '</strong></p>'
                + '    <p style="margin-bottom:0;">' + esc((i.user && i.user.email) || snapUser.email) + '</p>'
                + '  </div>'
                + '  <div class="col-sm-6">'
                + '    <h5>Reference</h5>'
                + '    <p style="margin-bottom:4px;"><strong>Booking:</strong> ' + esc(i.booking_id ? ('#' + i.booking_id) : snapBooking.id ? ('#' + snapBooking.id) : '—') + '</p>'
                + '    <p style="margin-bottom:4px;"><strong>Payment Intent:</strong> ' + esc(i.payment_intent_id || snapPayment.payment_intent_id) + '</p>'
                + '    <p style="margin-bottom:0;"><strong>Paid At:</strong> ' + fmtDate(i.paid_at) + '</p>'
                + '  </div>'
                + '</div>'
                + '<hr>'
                + '<h5 style="margin-top:0;">Items</h5>'
                + '<div class="table-responsive"><table class="table table-bordered table-striped">'
                + '  <thead><tr><th>Description</th><th class="text-right">Amount</th></tr></thead>'
                + '  <tbody>'
                + '    <tr><td>' + esc((snapSession.title ? ('Session: ' + snapSession.title) : 'Booking / Payment charge')) + '</td><td class="text-right">' + money(i.subtotal, i.currency) + '</td></tr>'
                + '    <tr><td>Tax</td><td class="text-right">' + money(i.tax_amount, i.currency) + '</td></tr>'
                + '    <tr><td>Discount</td><td class="text-right">- ' + money(i.discount_amount, i.currency) + '</td></tr>'
                + '  </tbody>'
                + '  <tfoot><tr><th class="text-right">Total</th><th class="text-right">' + money(i.total, i.currency) + '</th></tr></tfoot>'
                + '</table></div>'
                + '<div class="row">'
                + '  <div class="col-sm-6"><h5>Booking Details</h5><p><strong>Date:</strong> ' + esc(snapBooking.booking_date) + ' <strong>Time:</strong> ' + esc(snapBooking.time_slot) + '</p></div>'
                + '  <div class="col-sm-6"><h5>Payment Details</h5><p><strong>Status:</strong> ' + esc(snapPayment.status || i.status) + ' <strong>Amount:</strong> ' + money((snapPayment.amount || i.total), (snapPayment.currency || i.currency)) + '</p></div>'
                + '</div>'
                + '<hr>'
                + '<div class="text-right">'
                + '  <a target="_blank" class="btn btn-default m-r-xs" href="{{ url('admin/invoices') }}/' + i.id + '/print"><i class="fa fa-print"></i> Print</a>'
                + '  <a class="btn btn-primary" href="{{ url('admin/invoices') }}/' + i.id + '/pdf"><i class="fa fa-download"></i> Download PDF</a>'
                + '</div>';
            $('#invoiceViewBody').html(html);
            $('#invoiceViewModal').modal('show');
        }).fail(function (xhr) {
            toastr.error((xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Unable to load invoice');
        });
    });
});
</script>
@endsection
