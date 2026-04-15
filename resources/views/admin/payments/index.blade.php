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
    <div class="wrapper wrapper-content animated fadeInRight">
        <p class="text-muted m-b-md">Read-only list of Stripe payment records. New payments are created by the mobile API and webhooks.</p>

        <div id="viewPaymentModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Payment details</h4>
                    </div>
                    <div class="modal-body">
                        <pre id="viewPaymentContent" class="small" style="white-space:pre-wrap;max-height:480px;overflow:auto;"></pre>
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
                                        <th>When</th>
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
                                            <td>{{ $p->created_at?->format('d/m/Y H:i') }}</td>
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
                        {{ $payments->links() }}
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
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    toastr.error(xhr.responseJSON.message);
                } else {
                    toastr.error('Request failed.');
                }
            }

            $(document).on('click', '.btn-view-payment', function (e) {
                e.preventDefault();
                var id = $(this).data('id');
                $.get("{{ url('admin/payments') }}/" + id, function (res) {
                    $('#viewPaymentContent').text(JSON.stringify(res.payment, null, 2));
                    $('#viewPaymentModal').modal('show');
                }).fail(toastErrors);
            });
        });
    </script>
@endsection
