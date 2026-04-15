@extends('layouts.admin')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Nutrition Adherence (Target vs Consumed)</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Nutrition Adherence</strong></li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="ibox"><div class="ibox-content">
            <form method="GET" action="{{ route('admin.nutrition.adherence.index') }}" class="row">
                <div class="col-md-3"><div class="form-group"><label>User</label>
                    <select name="user_id" class="form-control js-select2" data-placeholder="All users">
                        <option value="">All users</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}" {{ (string) request('user_id') === (string) $u->id ? 'selected' : '' }}>{{ $u->first_name }} {{ $u->last_name }} — {{ $u->email }}</option>
                        @endforeach
                    </select>
                </div></div>
                <div class="col-md-2"><div class="form-group"><label>From</label><input class="form-control js-flatpickr-date" name="from_date" value="{{ $filters['from_date'] ?? '' }}"></div></div>
                <div class="col-md-2"><div class="form-group"><label>To</label><input class="form-control js-flatpickr-date" name="to_date" value="{{ $filters['to_date'] ?? '' }}"></div></div>
                <div class="col-md-3"><div class="form-group"><label>Search user</label><input class="form-control" name="q" value="{{ request('q') }}" placeholder="Name or email"></div></div>
                <div class="col-md-1"><div class="form-group"><label>Per page</label><select class="form-control" name="per_page">@foreach ([10,15,25,50,100] as $pp)<option value="{{ $pp }}" {{ (int) request('per_page', 15) === $pp ? 'selected' : '' }}>{{ $pp }}</option>@endforeach</select></div></div>
                <div class="col-md-1 text-right" style="padding-top:25px;">
                    <button class="btn btn-primary">Apply</button>
                </div>
            </form>
        </div></div>

        <div class="ibox"><div class="ibox-content table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Calories</th>
                        <th>Proteins</th>
                        <th>Fats</th>
                        <th>Carbs</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($rows as $row)
                    @php
                        $a = $row->adherence;
                    @endphp
                    <tr>
                        <td>
                            <strong>{{ $row->first_name }} {{ $row->last_name }}</strong>
                            <div class="small text-muted">{{ $row->email }}</div>
                        </td>
                        <td>
                            <div><strong>{{ $a['consumed']['calories'] }}</strong> / {{ $a['target']['calories'] }}</div>
                            <div class="small text-muted">{{ $a['percent']['calories'] }}% achieved</div>
                        </td>
                        <td>
                            <div><strong>{{ $a['consumed']['proteins'] }}</strong> / {{ $a['target']['proteins'] }}</div>
                            <div class="small text-muted">{{ $a['percent']['proteins'] }}% achieved</div>
                        </td>
                        <td>
                            <div><strong>{{ $a['consumed']['fats'] }}</strong> / {{ $a['target']['fats'] }}</div>
                            <div class="small text-muted">{{ $a['percent']['fats'] }}% achieved</div>
                        </td>
                        <td>
                            <div><strong>{{ $a['consumed']['carbs'] }}</strong> / {{ $a['target']['carbs'] }}</div>
                            <div class="small text-muted">{{ $a['percent']['carbs'] }}% achieved</div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted">No adherence data found for selected filters.</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="text-right">{{ $rows->links() }}</div>
        </div></div>
    </div>
@endsection
