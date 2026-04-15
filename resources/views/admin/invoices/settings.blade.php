@extends('layouts.admin')

@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Invoice Settings</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li><a href="{{ route('admin.invoices.index') }}">Invoices</a></li>
                <li class="active"><strong>Settings</strong></li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="ibox">
                    <div class="ibox-title">
                        <h5>Branding & Defaults</h5>
                    </div>
                    <div class="ibox-content">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        <form method="POST" action="{{ route('admin.invoices.settings.update') }}">
                            @csrf
                            <div class="form-group">
                                <label>Company name</label>
                                <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $settings['company_name'] ?? '') }}" required>
                            </div>
                            <div class="form-group">
                                <label>Company email</label>
                                <input type="text" name="company_email" class="form-control" value="{{ old('company_email', $settings['company_email'] ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label>Company phone</label>
                                <input type="text" name="company_phone" class="form-control" value="{{ old('company_phone', $settings['company_phone'] ?? '') }}">
                            </div>
                            <div class="form-group">
                                <label>Company address</label>
                                <textarea name="company_address" class="form-control" rows="3">{{ old('company_address', $settings['company_address'] ?? '') }}</textarea>
                            </div>
                            <div class="form-group">
                                <label>Logo URL</label>
                                <input type="text" name="company_logo_url" class="form-control" value="{{ old('company_logo_url', $settings['company_logo_url'] ?? '') }}">
                            </div>
                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Tax (%)</label>
                                        <input type="number" step="0.01" min="0" max="100" name="tax_percent" class="form-control" value="{{ old('tax_percent', $settings['tax_percent'] ?? 0) }}">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label>Default discount amount</label>
                                        <input type="number" step="0.01" min="0" name="default_discount_amount" class="form-control" value="{{ old('default_discount_amount', $settings['default_discount_amount'] ?? 0) }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Footer notes</label>
                                <textarea name="footer_notes" class="form-control" rows="3">{{ old('footer_notes', $settings['footer_notes'] ?? '') }}</textarea>
                            </div>
                            <button class="btn btn-primary"><i class="fa fa-save"></i> Save settings</button>
                            <a href="{{ route('admin.invoices.index') }}" class="btn btn-white">Back to invoices</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
