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
    @php
        $hasActiveSessionFilters = filled(request('q')) ||
            filled(request('trainer_id')) ||
            filled(request('is_publish')) ||
            filled(request('intensity')) ||
            filled(request('created_from')) ||
            filled(request('created_to'));
    @endphp
    <div class="wrapper wrapper-content animated fadeInRight">
        <div class="row m-b-sm">
            <div class="col-sm-6">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addClassModal">
                    <i class="fa fa-plus"></i> Add session
                </button>
                <button type="button" class="btn btn-white m-l-xs" data-toggle="modal" data-target="#manageCatalogModal">
                    <i class="fa fa-list"></i> Manage options
                </button>
            </div>
            <div class="col-sm-6 text-right">
                <button
                    type="button"
                    class="btn btn-default {{ $hasActiveSessionFilters ? '' : 'collapsed' }}"
                    data-toggle="collapse"
                    data-target="#sessionFiltersCollapse"
                    aria-expanded="{{ $hasActiveSessionFilters ? 'true' : 'false' }}"
                    aria-controls="sessionFiltersCollapse"
                    id="toggleSessionFiltersBtn"
                >
                    <i class="fa fa-filter"></i> {{ $hasActiveSessionFilters ? 'Hide filters' : 'Show filters' }}
                </button>
            </div>
        </div>

        <div class="m-t-sm m-b-sm collapse {{ $hasActiveSessionFilters ? 'in' : '' }}" id="sessionFiltersCollapse">
            <div class="ibox m-t-sm" id="sessionFiltersBox">
            <div class="ibox-title" style="min-height: 44px;">
                <h5>Filters</h5>
            </div>
            <div class="ibox-content">
                <form method="GET" action="{{ route('admin.classes.index') }}" class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Search</label>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Title or description">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Trainer</label>
                            <select name="trainer_id" class="form-control js-select2" data-placeholder="All trainers">
                                <option value="">All</option>
                                @foreach ($trainers as $trainer)
                                    <option value="{{ $trainer->id }}" {{ (string) request('trainer_id') === (string) $trainer->id ? 'selected' : '' }}>
                                        {{ $trainer->first_name }} {{ $trainer->last_name }} ({{ $trainer->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Published</label>
                            <select name="is_publish" class="form-control js-select2" data-placeholder="All">
                                <option value="">All</option>
                                <option value="1" {{ request('is_publish') === '1' ? 'selected' : '' }}>Published</option>
                                <option value="0" {{ request('is_publish') === '0' ? 'selected' : '' }}>Draft</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>Intensity</label>
                            <input type="text" name="intensity" value="{{ request('intensity') }}" class="form-control" placeholder="e.g. high">
                        </div>
                    </div>
                    <div class="col-md-2">
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
                            <label>Created from</label>
                            <input type="text" name="created_from" value="{{ request('created_from') }}" class="form-control js-flatpickr-date" placeholder="YYYY-MM-DD">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>Created to</label>
                            <input type="text" name="created_to" value="{{ request('created_to') }}" class="form-control js-flatpickr-date" placeholder="YYYY-MM-DD">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" style="margin-top: 24px;">
                            <button type="submit" class="btn btn-primary"><i class="fa fa-filter"></i> Apply filters</button>
                            <a href="{{ route('admin.classes.index') }}" class="btn btn-white m-l-xs"><i class="fa fa-refresh"></i> Reset</a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        </div>

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

        <div id="viewClassModal" class="modal fade session-detail-modal" role="dialog" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content session-detail-modal-content">
                    <div class="modal-header session-detail-modal__header">
                        <button type="button" class="close session-detail-modal__close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title session-detail-modal__title" id="viewClassModalTitle">Session details</h4>
                    </div>
                    <div class="modal-body session-detail-modal__body">
                        <div id="viewClassContent" class="session-detail-root"></div>
                    </div>
                    <div class="modal-footer session-detail-modal__footer">
                        <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
                        <button type="button" id="viewClassEditBtn" class="btn btn-primary" data-id="">
                            <i class="fa fa-pencil"></i> Edit session
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .session-detail-modal .modal-dialog { max-width: 920px; margin: 24px auto; }
            .session-detail-modal-content { border: 0; border-radius: 14px; overflow: hidden; box-shadow: 0 18px 45px rgba(0, 0, 0, 0.18); }
            .session-detail-modal__header { background: linear-gradient(130deg, #15947d 0%, #1ab394 40%, #23c6c8 100%); color: #fff; border-bottom: 0; }
            .session-detail-modal__title { color: #fff; font-weight: 700; letter-spacing: -0.02em; }
            .session-detail-modal__close { color: #fff; opacity: .9; text-shadow: none; }
            .session-detail-modal__close:hover { opacity: 1; color: #fff; }
            .session-detail-modal__body { padding: 0; max-height: min(78vh, 760px); overflow-y: auto; background: #eef2f6; }
            .session-detail-modal__footer { border-top: 1px solid #e5ebf0; background: #f7f9fc; }
            .session-detail-root { font-size: 14px; color: #3d4044; }
            .session-detail-hero { display: flex; flex-wrap: wrap; background: linear-gradient(165deg, #fff 0%, #f2f6fa 100%); border-bottom: 1px solid rgba(0,0,0,.05); }
            .session-detail-hero__media { flex: 0 0 240px; min-height: 200px; background: #dce4ec; }
            .session-detail-hero__media img { width: 100%; height: 100%; min-height: 200px; object-fit: cover; }
            .session-detail-hero__media--empty { display: flex; align-items: center; justify-content: center; font-size: 56px; color: #9aa8b8; }
            .session-detail-hero__main { flex: 1; min-width: 220px; padding: 24px 26px 28px; }
            .session-detail-hero__badges .label { margin-right: 8px; border-radius: 18px; padding: 5px 12px; }
            .session-detail-hero__name { margin: 8px 0 10px; font-size: 24px; font-weight: 800; line-height: 1.2; color: #1a1f26; }
            .session-detail-hero__sub { margin: 0; color: #5c6570; font-size: 13px; }
            .session-detail-hero__sub i { margin-right: 5px; color: #1ab394; }
            .session-detail-stats { display: flex; flex-wrap: wrap; gap: 12px; padding: 16px 18px 20px; background: #eef2f6; }
            .session-detail-stat { flex: 1 1 calc(25% - 12px); min-width: 130px; background: #fff; border-radius: 12px; padding: 14px 16px; display: flex; gap: 12px; border: 1px solid rgba(0,0,0,.05); box-shadow: 0 4px 12px rgba(15,35,52,.06); }
            .session-detail-stat__icon { width: 42px; height: 42px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; color: #fff; font-size: 16px; }
            .session-detail-stat--p .session-detail-stat__icon { background: linear-gradient(135deg, #1ab394, #17a589); }
            .session-detail-stat--d .session-detail-stat__icon { background: linear-gradient(135deg, #5dade2, #3498db); }
            .session-detail-stat--c .session-detail-stat__icon { background: linear-gradient(135deg, #f39c12, #e67e22); }
            .session-detail-stat--w .session-detail-stat__icon { background: linear-gradient(135deg, #9b59b6, #8e44ad); }
            .session-detail-stat__value { font-size: 20px; font-weight: 800; color: #1a1f26; line-height: 1.15; }
            .session-detail-stat__label { margin-top: 4px; font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: #8a939d; font-weight: 600; }
            .session-detail-sheet { padding: 6px 16px 18px; }
            .session-detail-section { margin-bottom: 12px; padding: 18px 20px; background: #fff; border-radius: 12px; border: 1px solid rgba(0,0,0,.05); box-shadow: 0 2px 10px rgba(15,35,52,.04); }
            .session-detail-section__title { margin: 0 0 14px; font-size: 12px; text-transform: uppercase; letter-spacing: .1em; color: #7a8490; font-weight: 700; display: flex; gap: 10px; align-items: center; }
            .session-detail-section__title i { width: 32px; height: 32px; border-radius: 9px; background: linear-gradient(135deg, #eef6f4 0%, #e0f0ec 100%); color: #1ab394; display: inline-flex; align-items: center; justify-content: center; font-size: 14px; }
            .session-detail-section__text { margin: 0; line-height: 1.7; color: #4a5058; }
            .session-detail-timing-highlight { display: inline-block; padding: 10px 14px; border-radius: 10px; border: 1px solid rgba(26,179,148,.2); background: linear-gradient(135deg, #f8fbfa 0%, #eef6f3 100%); font-weight: 600; color: #1a6b5c; }
            .session-detail-steps-wrap { margin: 0; padding: 0; list-style: none; }
            .session-detail-step { display: flex; gap: 14px; margin-bottom: 12px; }
            .session-detail-step__num { width: 30px; height: 30px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #1ab394, #15947d); color: #fff; font-weight: 700; font-size: 13px; }
            .session-detail-step__text { padding-top: 4px; color: #4a5058; line-height: 1.55; }
            .session-detail-tags { line-height: 1.85; }
            .session-detail-pill { display: inline-block; margin: 0 6px 8px 0; padding: 6px 13px; border-radius: 999px; font-size: 12px; font-weight: 600; box-shadow: 0 2px 6px rgba(0,0,0,.06); }
            .session-detail-pill--a { background: linear-gradient(135deg, #e8f4fc, #d6ebf7); color: #1f6fa8; }
            .session-detail-pill--b { background: linear-gradient(135deg, #eaf6f3, #d4eee8); color: #148f77; }
            .session-detail-pill--c { background: linear-gradient(135deg, #fef5e8, #fdebd0); color: #b9770e; }
            .session-detail-pill--d { background: linear-gradient(135deg, #f4ecf8, #e8daf2); color: #7d3c98; }
            .session-detail-pill--e { background: linear-gradient(135deg, #eef2f7, #dfe6ef); color: #4a5568; }
            .session-detail-meta { margin: 4px 16px 0; padding: 14px 18px 18px; border-radius: 0 0 10px 10px; background: linear-gradient(180deg, #e9edf3 0%, #e2e8f0 100%); display: flex; flex-wrap: wrap; gap: 8px 18px; color: #6b7280; font-size: 12px; }
            .session-detail-meta__chip { display: inline-flex; align-items: center; gap: 6px; padding: 4px 10px; border-radius: 8px; background: rgba(255,255,255,.65); }
            .session-detail-meta__chip i { color: #1ab394; }
            .session-detail-empty { color: #a0a8b2; font-style: italic; }
        </style>

        <div class="row m-t-xs">
            <div class="col-sm-12">
                <div class="ibox">
                    <div class="ibox-content">
                        <div class="m-b-sm text-muted">
                            Showing <strong>{{ $classes->firstItem() ?? 0 }}</strong> to
                            <strong>{{ $classes->lastItem() ?? 0 }}</strong> of
                            <strong>{{ $classes->total() }}</strong> sessions.
                        </div>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Trainer</th>
                                        <th>Price</th>
                                        <th>Published</th>
                                        <th>Created</th>
                                        <th>Updated</th>
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
                                            <td>
                                                @if($row->created_at)
                                                    <div>{{ $row->created_at->format('d M Y, h:i A') }}</div>
                                                    <small class="text-muted">{{ $row->created_at->diffForHumans() }}</small>
                                                @else — @endif
                                            </td>
                                            <td>
                                                @if($row->updated_at)
                                                    <div>{{ $row->updated_at->format('d M Y, h:i A') }}</div>
                                                    <small class="text-muted">{{ $row->updated_at->diffForHumans() }}</small>
                                                @else — @endif
                                            </td>
                                            <td><a href="#" class="btn-view-class text-navy" data-id="{{ $row->id }}"><i class="fa fa-eye"></i></a></td>
                                            <td><a href="#" class="btn-edit-class text-navy" data-id="{{ $row->id }}"><i class="fa fa-pencil"></i></a></td>
                                            <td><a href="#" class="btn-delete-class text-danger" data-id="{{ $row->id }}"><i class="fa fa-trash"></i></a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{ $classes->appends(request()->query())->links() }}
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

            function updateSessionFilterBtnText(isOpen) {
                $('#toggleSessionFiltersBtn').html(
                    '<i class="fa fa-filter"></i> ' + (isOpen ? 'Hide filters' : 'Show filters')
                );
            }

            updateSessionFilterBtnText($('#sessionFiltersCollapse').hasClass('in'));

            $('#sessionFiltersCollapse')
                .on('shown.bs.collapse', function () {
                    updateSessionFilterBtnText(true);
                })
                .on('hidden.bs.collapse', function () {
                    updateSessionFilterBtnText(false);
                });
        });
    </script>
    @include('admin.classes._scripts')
@endsection
