@extends('layouts.admin')
@section('content')
    <div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>Sessions (Classes)</h2>
            <ol class="breadcrumb">
                <li><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="active"><strong>Sessions</strong></li>
            </ol>
        </div>
    </div>
    <div class="wrapper wrapper-content animated fadeInRight">
        <button type="button" class="btn btn-primary m-b-sm" data-toggle="modal" data-target="#addClassModal">
            <i class="fa fa-plus"></i> Add session
        </button>
        <button type="button" class="btn btn-white m-b-sm m-l-xs" data-toggle="modal" data-target="#manageCatalogModal">
            <i class="fa fa-list"></i> Manage options
        </button>

        @include('admin.classes._modal_form', [
            'modalId' => 'addClassModal',
            'formId' => 'addClassForm',
            'title' => 'Add session',
            'submitLabel' => 'Save',
            'isEdit' => false,
            'catalogByType' => $catalogByType,
            'trainers' => $trainers,
        ])

        @include('admin.classes._modal_form', [
            'modalId' => 'editClassModal',
            'formId' => 'editClassForm',
            'title' => 'Edit session',
            'submitLabel' => 'Update',
            'isEdit' => true,
            'catalogByType' => $catalogByType,
            'trainers' => $trainers,
        ])

        @include('admin.classes._catalog_modal')

        <div id="viewClassModal" class="modal fade" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Session details</h4>
                    </div>
                    <div class="modal-body">
                        <pre id="viewClassContent" class="small" style="white-space:pre-wrap;max-height:420px;overflow:auto;"></pre>
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
                                        <th>Title</th>
                                        <th>Trainer</th>
                                        <th>Price</th>
                                        <th>Published</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($classes as $row)
                                        <tr>
                                            <td>{{ $row->id }}</td>
                                            <td>{{ $row->session_title }}</td>
                                            <td>{{ optional($row->user)->first_name }} {{ optional($row->user)->last_name }}</td>
                                            <td>{{ number_format((float) $row->price, 2) }}</td>
                                            <td>
                                                @if ($row->is_publish === '1' || $row->is_publish === 1)
                                                    <span class="label label-primary">Yes</span>
                                                @else
                                                    <span class="label label-default">No</span>
                                                @endif
                                            </td>
                                            <td><a href="#" class="btn-view-class text-navy" data-id="{{ $row->id }}"><i class="fa fa-eye"></i></a></td>
                                            <td><a href="#" class="btn-edit-class text-navy" data-id="{{ $row->id }}"><i class="fa fa-pencil"></i></a></td>
                                            <td><a href="#" class="btn-delete-class text-danger" data-id="{{ $row->id }}"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $classes->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    @include('admin.classes._scripts')
@endsection
