@extends('layouts.admin')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Reports</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Reports</strong></li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox">
            <div class="ibox-content">
                <form method="GET" action="{{ route('admin.reports.index') }}" class="form-inline">
                    <label>From</label>
                    <input name="from_date" class="form-control js-flatpickr-date" value="{{ $from->toDateString() }}">
                    <label>To</label>
                    <input name="to_date" class="form-control js-flatpickr-date" value="{{ $to->toDateString() }}">
                    <button class="btn btn-primary"><i class="fa fa-filter"></i> Apply</button>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-md-3"><div class="ibox"><div class="ibox-content"><h5>Bookings</h5><h2>{{ number_format($kpis['bookings_count']) }}</h2></div></div></div>
            <div class="col-md-3"><div class="ibox"><div class="ibox-content"><h5>Paid bookings</h5><h2>{{ number_format($kpis['paid_bookings']) }}</h2></div></div></div>
            <div class="col-md-3"><div class="ibox"><div class="ibox-content"><h5>Revenue</h5><h2>{{ number_format($kpis['revenue'], 2) }}</h2></div></div></div>
            <div class="col-md-3"><div class="ibox"><div class="ibox-content"><h5>Invoices issued</h5><h2>{{ number_format($kpis['invoices_issued']) }}</h2></div></div></div>
        </div>
        <div class="row">
            <div class="col-md-12"><div class="ibox"><div class="ibox-content"><h5>Daily booking report</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead><tr><th>Date</th><th>Total bookings</th><th>Paid bookings</th></tr></thead>
                        <tbody>
                        @forelse($daily as $d)
                            <tr><td>{{ $d->d }}</td><td>{{ $d->bookings }}</td><td>{{ $d->paid_bookings }}</td></tr>
                        @empty
                            <tr><td colspan="3" class="text-center text-muted">No report data for selected range.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div></div></div>
        </div>
    </div>
@endsection
