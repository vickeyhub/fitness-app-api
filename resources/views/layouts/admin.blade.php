<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Fitness | Admin</title>

    <link href="{{ asset('assets/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/font-awesome/css/font-awesome.css') }}" rel="stylesheet">

    <!-- Toastr style -->
    <link href="{{ asset('assets/css/plugins/toastr/toastr.min.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-theme@0.1.0-beta.10/dist/select2-bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

    <link href="{{ asset('assets/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.css') }}" rel="stylesheet">


    <style>
        .cimg {
            width: 140px;
        }
        .select2-container {
            width: 100% !important;
        }
        .select2-container--open,
        .select2-dropdown,
        .select2-container--default.select2-container--open {
            z-index: 999999 !important;
        }
        .modal-open .select2-container--open,
        .modal-open .select2-dropdown {
            z-index: 2065 !important;
        }
        .flatpickr-calendar {
            z-index: 2066 !important;
        }
    </style>
</head>

<body>

    <div id="wrapper">
        @include('layouts.nav')
        <div id="page-wrapper" class="gray-bg">
            <div class="row border-bottom">
                <nav class="navbar navbar-static-top" role="navigation" style="margin-bottom: 0">
                    <div class="navbar-header">
                        <a class="navbar-minimalize minimalize-styl-2 btn btn-primary " href="#"><i
                                class="fa fa-bars"></i> </a>
                        <form role="search" class="navbar-form-custom" action="search_results.html">
                            <div class="form-group">
                                <input type="text" placeholder="Search for something..." class="form-control"
                                    name="top-search" id="top-search">
                            </div>
                        </form>
                    </div>
                </nav>
            </div>
            @yield('content')
        </div>
    </div>
    <!-- Mainly scripts -->
    <script src="{{ asset('assets/js/jquery-3.1.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/metisMenu/jquery.metisMenu.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/slimscroll/jquery.slimscroll.min.js') }}"></script>

    {{-- <!-- Flot -->
    <script src="{{ asset('assets/js/plugins/flot/jquery.flot.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/flot/jquery.flot.tooltip.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/flot/jquery.flot.spline.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/flot/jquery.flot.resize.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/flot/jquery.flot.pie.js') }}"></script>

    <!-- Peity -->
    <script src="{{ asset('assets/js/plugins/peity/jquery.peity.min.js') }}"></script>
    <script src="{{ asset('assets/js/demo/peity-demo.js') }}"></script>

    <!-- Custom and plugin javascript -->
    <script src="{{ asset('assets/js/inspinia.js') }}"></script>
    <script src="{{ asset('assets/js/plugins/pace/pace.min.js') }}"></script>

    <!-- jQuery UI -->
    <script src="{{ asset('assets/js/plugins/jquery-ui/jquery-ui.min.js') }}"></script>

    <!-- GITTER -->
    <script src="{{ asset('assets/js/plugins/gritter/jquery.gritter.min.js') }}"></script>

    <!-- Sparkline -->
    <script src="{{ asset('assets/js/plugins/sparkline/jquery.sparkline.min.js') }}"></script> --}}

    <!-- Sparkline demo data  -->
    <script src="{{ asset('assets/js/demo/sparkline-demo.js') }}"></script>

    <!-- ChartJS-->
    <script src="{{ asset('assets/js/plugins/chartJs/Chart.min.js') }}"></script>

    <!-- Toastr -->
    <script src="{{ asset('assets/js/plugins/toastr/toastr.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        window.initUiEnhancements = function(scope) {
            var $scope = scope ? $(scope) : $(document);

            $scope.find('select.js-select2').each(function() {
                var $select = $(this);
                if ($select.hasClass('select2-hidden-accessible')) return;
                var config = {
                    width: '100%',
                    theme: 'bootstrap',
                    placeholder: $select.attr('data-placeholder') || '',
                    allowClear: !$select.prop('required'),
                    dropdownParent: $(document.body)
                };
                $select.select2(config);
            });

            var formatAmPmRange = function(selectedDates, instance) {
                if (!selectedDates || selectedDates.length === 0) {
                    instance.input.value = '';
                    return;
                }
                var parts = selectedDates.map(function(dt) {
                    return instance.formatDate(dt, 'h:iK').toLowerCase();
                });
                instance.input.value = parts.join(' - ');
            };

            $scope.find('input.js-flatpickr-date').each(function() {
                if (this._flatpickr) return;
                flatpickr(this, {
                    dateFormat: 'Y-m-d',
                    allowInput: false,
                    disableMobile: true
                });
            });

            $scope.find('input.js-flatpickr-time').each(function() {
                if (this._flatpickr) return;
                flatpickr(this, {
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: 'h:iK',
                    minuteIncrement: 5,
                    time_24hr: false,
                    allowInput: false,
                    disableMobile: true
                });
            });

            $scope.find('input.js-flatpickr-time-range').each(function() {
                if (this._flatpickr) return;
                flatpickr(this, {
                    mode: 'range',
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: 'h:iK',
                    conjunction: ' - ',
                    minuteIncrement: 5,
                    time_24hr: false,
                    allowInput: false,
                    disableMobile: true,
                    onChange: function(selectedDates, dateStr, instance) {
                        formatAmPmRange(selectedDates, instance);
                    }
                });
            });

            $scope.find('input.js-flatpickr-session-timing').each(function() {
                if (this._flatpickr) return;
                flatpickr(this, {
                    mode: 'range',
                    enableTime: true,
                    noCalendar: true,
                    dateFormat: 'h:iK',
                    conjunction: ' - ',
                    minuteIncrement: 5,
                    time_24hr: false,
                    allowInput: false,
                    disableMobile: true,
                    onChange: function(selectedDates, dateStr, instance) {
                        formatAmPmRange(selectedDates, instance);
                    },
                    onClose: function(selectedDates, dateStr, instance) {
                        if (selectedDates.length === 1) {
                            instance.open();
                        }
                    }
                });
            });
        };

        $(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            window.initUiEnhancements(document);
        });
    </script>

    @yield('script')
    @yield('scripts')
</body>

</html>
