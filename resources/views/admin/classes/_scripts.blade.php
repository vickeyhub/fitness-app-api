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

        function stepRowHtml(value) {
            var v = value == null ? '' : $('<div/>').text(value).html();
            return '<div class="form-group step-row m-b-xs">' +
                '<div class="input-group">' +
                '<input type="text" name="step_lines[]" class="form-control" placeholder="Describe this step" maxlength="500" value="' + v + '">' +
                '<span class="input-group-btn">' +
                '<button type="button" class="btn btn-default btn-remove-step" tabindex="-1"><i class="fa fa-times"></i></button>' +
                '</span></div></div>';
        }

        function to12HourLabel(hhmm) {
            if (!hhmm || hhmm.indexOf(':') === -1) return '';
            var parts = hhmm.split(':');
            var hour = parseInt(parts[0], 10);
            var minute = parts[1];
            if (isNaN(hour) || minute == null) return '';
            var suffix = hour >= 12 ? 'pm' : 'am';
            var hour12 = hour % 12;
            if (hour12 === 0) hour12 = 12;
            var paddedHour = hour12 < 10 ? '0' + hour12 : String(hour12);
            return paddedHour + ':' + minute + suffix;
        }

        function updateTimingPreview(prefix) {
            var start = $('#' + prefix + 'start_time').val();
            var end = $('#' + prefix + 'end_time').val();
            var txt = '';
            if (start && end) {
                txt = to12HourLabel(start) + ' - ' + to12HourLabel(end);
            }
            $('#' + prefix + 'session_timing_preview').val(txt);
            $('#' + prefix + 'session_timing').val(txt);
        }

        function parseTimingRangeTo24h(timingText) {
            var text = $.trim((timingText || '').toLowerCase());
            if (!text) return null;
            var parts = text.split(/\s*-\s*/);
            if (parts.length !== 2) return null;

            function parsePiece(piece) {
                var clean = piece.replace(/\s+/g, '');
                var m = clean.match(/^(\d{1,2}):(\d{2})(am|pm)$/);
                if (!m) return null;
                var h = parseInt(m[1], 10);
                var mm = m[2];
                var suffix = m[3];
                if (h < 1 || h > 12) return null;
                if (suffix === 'am') {
                    if (h === 12) h = 0;
                } else if (h !== 12) {
                    h += 12;
                }
                var hh = h < 10 ? '0' + h : String(h);
                return hh + ':' + mm;
            }

            var start = parsePiece(parts[0]);
            var end = parsePiece(parts[1]);
            if (!start || !end) return null;
            return { start: start, end: end };
        }

        $(document).on('change input', '#add_class_start_time, #add_class_end_time, #edit_class_start_time, #edit_class_end_time', function () {
            var id = $(this).attr('id') || '';
            var prefix = id.indexOf('edit_class_') === 0 ? 'edit_class_' : 'add_class_';
            updateTimingPreview(prefix);
        });

        $(document).on('click', '.btn-add-step', function () {
            var prefix = $(this).data('prefix');
            $('#' + prefix + 'step_rows').append(stepRowHtml(''));
        });

        $(document).on('click', '.btn-remove-step', function () {
            var $rows = $(this).closest('.step-rows').find('.step-row');
            if ($rows.length <= 1) {
                $(this).closest('.step-row').find('input').val('');
                return;
            }
            $(this).closest('.step-row').remove();
        });

        function clearClassForm(prefix) {
            var $form = $('#' + (prefix === 'add_class_' ? 'addClassForm' : 'editClassForm'));
            $form.find('input[type=text],input[type=number],textarea,select').not('[type=hidden]').val('');
            $form.find('input[type=time]').val('');
            $('#' + prefix + 'session_timing').val('');
            $('#' + prefix + 'session_timing_preview').val('');
            $form.find('input[type=checkbox]').prop('checked', false);
            var $steps = $('#' + prefix + 'step_rows');
            $steps.empty().append(stepRowHtml(''));
        }

        $('#addClassModal').on('hidden.bs.modal', function () {
            clearClassForm('add_class_');
        });

        $('#addClassForm, #editClassForm').on('submit', function (e) {
            var $form = $(this);
            var $steps = $form.find('[name="step_lines[]"]');
            var anyStep = false;
            $steps.each(function () { if ($.trim($(this).val()) !== '') anyStep = true; });
            if (!anyStep) {
                e.preventDefault();
                toastr.error('Add at least one step with text.');
                return;
            }
            if ($form.find('input[name="schedule_days[]"]:checked').length === 0) {
                e.preventDefault();
                toastr.error('Select at least one day for the schedule.');
                return;
            }
            if ($form.find('input[name="muscle_names[]"]:checked').length === 0) {
                e.preventDefault();
                toastr.error('Select at least one muscle group.');
                return;
            }
            if ($form.find('input[name="fitness_goal_names[]"]:checked').length === 0) {
                e.preventDefault();
                toastr.error('Select at least one fitness goal.');
                return;
            }
            if ($form.find('input[name="session_type_names[]"]:checked').length === 0) {
                e.preventDefault();
                toastr.error('Select at least one session type.');
                return;
            }
            var kwChecked = $form.find('input[name="keyword_names[]"]:checked').length;
            var kwCustom = $.trim($form.find('[name="keyword_custom"]').val() || '');
            if (kwChecked === 0 && kwCustom === '') {
                e.preventDefault();
                toastr.error('Select a keyword or enter custom keywords.');
                return;
            }

            var start = $.trim($form.find('input[name="start_time"]').val() || '');
            var end = $.trim($form.find('input[name="end_time"]').val() || '');
            if (!start || !end) {
                e.preventDefault();
                toastr.error('Please select both start and end time.');
                return;
            }
            if (start && end && end <= start) {
                e.preventDefault();
                toastr.error('End time must be after start time.');
                return;
            }
            var timing = to12HourLabel(start) + ' - ' + to12HourLabel(end);
            $form.find('input[name="session_timing"]').val(timing);
        });

        $('#addClassForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.classes.store') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function (res) {
                    toastr.success(res.message || 'Saved.');
                    $('#addClassModal').modal('hide');
                    window.location.reload();
                },
                error: toastErrors
            });
        });

        function normDay(d) {
            var u = (d || '').toString().toUpperCase().trim();
            var allowed = ['MON','TUE','WED','THU','FRI','SAT','SUN'];
            if (allowed.indexOf(u) >= 0) return u;
            if (u.length >= 3) return u.substring(0, 3);
            return u;
        }

        function setCheckboxGroup($form, name, values) {
            $form.find('input[name="' + name + '"]').each(function () {
                var val = $(this).val();
                var hit = false;
                $.each(values || [], function (_, v) {
                    if (String(val).toLowerCase() === String(v).toLowerCase()) hit = true;
                });
                $(this).prop('checked', hit);
            });
        }

        function fillClassForm(prefix, c) {
            $('#' + prefix + 'id').val(c.id);
            $('#' + prefix + 'session_title').val(c.session_title || '');
            $('#' + prefix + 'description').val(c.description || '');
            $('#' + prefix + 'total_duration').val(c.total_duration || '');
            $('#' + prefix + 'calories').val(c.calories || '');
            $('#' + prefix + 'user_id').val(String(c.user_id));
            $('#' + prefix + 'price').val(c.price || '');
            $('#' + prefix + 'session_avrage_rating').val(c.session_avrage_rating || '');
            var startTime = '';
            var endTime = '';
            if (c.start_time) {
                startTime = String(c.start_time).substring(0, 5);
            }
            if (c.end_time) {
                endTime = String(c.end_time).substring(0, 5);
            }
            if ((!startTime || !endTime) && c.session_timing) {
                var parsedTiming = parseTimingRangeTo24h(c.session_timing);
                if (parsedTiming) {
                    startTime = startTime || parsedTiming.start;
                    endTime = endTime || parsedTiming.end;
                }
            }
            $('#' + prefix + 'start_time').val(startTime);
            $('#' + prefix + 'end_time').val(endTime);
            updateTimingPreview(prefix);
            $('#' + prefix + 'duration').val(c.duration != null && c.duration !== '' ? c.duration : '');
            $('#' + prefix + 'session_thumbnail').val(c.session_thumbnail || '');
            $('#' + prefix + 'intensity').val(c.intensity || '');
            $('#' + prefix + 'is_publish').val(String(c.is_publish) === '1' || c.is_publish === 1 || c.is_publish === true ? '1' : '0');
            $('#' + prefix + 'latitude').val(c.latitude || '');
            $('#' + prefix + 'longitude').val(c.longitude || '');
            $('#' + prefix + 'radius').val(c.radius != null ? c.radius : '');

            var $form = $('#' + (prefix === 'edit_class_' ? 'editClassForm' : 'addClassForm'));

            var steps = c.steps || [];
            var $stepHost = $('#' + prefix + 'step_rows');
            $stepHost.empty();
            if (!steps.length) {
                $stepHost.append(stepRowHtml(''));
            } else {
                $.each(steps, function (_, s) { $stepHost.append(stepRowHtml(s)); });
            }

            var sched = $.map(c.schedule || [], normDay);
            $form.find('input[name="schedule_days[]"]').prop('checked', false);
            $form.find('input[name="schedule_days[]"]').each(function () {
                if (sched.indexOf($(this).val()) >= 0) $(this).prop('checked', true);
            });

            setCheckboxGroup($form, 'muscle_names[]', c.muscles_involved || []);
            setCheckboxGroup($form, 'fitness_goal_names[]', c.fitness_goal || []);
            setCheckboxGroup($form, 'session_type_names[]', c.session_type || []);

            var kws = c.session_keywords || [];
            var catalogVals = [];
            $form.find('input[name="keyword_names[]"]').each(function () { catalogVals.push($(this).val()); });
            var customParts = [];
            $.each(kws, function (_, kw) {
                var found = false;
                $.each(catalogVals, function (_, cv) {
                    if (String(cv).toLowerCase() === String(kw).toLowerCase()) found = true;
                });
                if (!found && String(kw).trim() !== '') customParts.push(kw);
            });
            setCheckboxGroup($form, 'keyword_names[]', kws);
            $('#' + prefix + 'keyword_custom').val(customParts.join(', '));
        }

        $(document).on('click', '.btn-edit-class', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $.get("{{ url('admin/classes') }}/" + id, function (res) {
                fillClassForm('edit_class_', res.class);
                $('#editClassModal').modal('show');
            }).fail(toastErrors);
        });

        $('#editClassForm').on('submit', function (e) {
            e.preventDefault();
            var id = $('#edit_class_id').val();
            $.ajax({
                url: "{{ url('admin/classes') }}/" + id,
                method: 'POST',
                data: $(this).serialize() + '&_method=PUT',
                success: function (res) {
                    toastr.success(res.message || 'Updated.');
                    $('#editClassModal').modal('hide');
                    window.location.reload();
                },
                error: toastErrors
            });
        });

        function detailTxt(v) {
            return v == null ? '' : String(v);
        }

        function renderSessionDetailView(c) {
            var $root = $('<div/>');

            var published = c.is_publish === '1' || c.is_publish === 1 || c.is_publish === true;
            var trainer = c.user;
            var trainerName = trainer
                ? $.trim(detailTxt(trainer.first_name) + ' ' + detailTxt(trainer.last_name))
                : '';
            if (!trainerName && trainer && trainer.email) {
                trainerName = detailTxt(trainer.email);
            }

            var $hero = $('<div class="session-detail-hero"/>');
            var thumb = detailTxt(c.session_thumbnail);
            var $media = $('<div class="session-detail-hero__media"/>');
            if (thumb) {
                $media.append(
                    $('<img alt=""/>').attr('src', thumb).on('error', function () {
                        $(this).replaceWith(
                            $('<div class="session-detail-hero__media session-detail-hero__media--empty"/>')
                                .html('<i class="fa fa-image"></i>')
                        );
                    })
                );
            } else {
                $media.addClass('session-detail-hero__media--empty').html('<i class="fa fa-image"></i>');
            }
            $hero.append($media);

            var $main = $('<div class="session-detail-hero__main"/>');
            var $badges = $('<div class="session-detail-hero__badges"/>');
            $badges.append(
                $('<span class="label"/>')
                    .addClass(published ? 'label-primary' : 'label-default')
                    .text(published ? 'Published' : 'Draft')
            );
            if (c.intensity) {
                $badges.append($('<span class="label label-warning"/>').text(detailTxt(c.intensity)));
            }
            $main.append($badges);
            $main.append($('<h3 class="session-detail-hero__name"/>').text(detailTxt(c.session_title) || 'Untitled session'));
            var $sub = $('<p class="session-detail-hero__sub"/>');
            if (trainerName) {
                $sub.append(
                    $('<span/>').append($('<i class="fa fa-user"/>')).append(document.createTextNode(' ' + trainerName))
                );
            }
            if (trainer && trainer.email) {
                if (trainerName) {
                    $sub.append(document.createTextNode(' · '));
                }
                $sub.append(
                    $('<span/>')
                        .append($('<i class="fa fa-envelope-o"/>'))
                        .append(document.createTextNode(' ' + detailTxt(trainer.email)))
                );
            }
            if (!trainerName && !(trainer && trainer.email)) {
                $sub.text('No trainer linked');
            }
            $main.append($sub);
            $hero.append($main);
            $root.append($hero);

            var price = c.price != null ? parseFloat(c.price, 10) : 0;
            var $stats = $('<div class="session-detail-stats"/>');
            function addStat(modClass, iconFa, label, val) {
                var $s = $('<div class="session-detail-stat"/>').addClass(modClass);
                var $ic = $('<div class="session-detail-stat__icon"/>');
                $ic.append($('<i/>').addClass(iconFa));
                $s.append($ic);
                var $body = $('<div class="session-detail-stat__body"/>');
                $body.append($('<div class="session-detail-stat__value"/>').text(val));
                $body.append($('<div class="session-detail-stat__label"/>').text(label));
                $s.append($body);
                $stats.append($s);
            }
            addStat('session-detail-stat--p', 'fa fa-usd', 'Price', isNaN(price) ? '—' : price.toFixed(2));
            addStat('session-detail-stat--d', 'fa fa-clock-o', 'Duration', c.duration != null ? detailTxt(c.duration) + ' min' : '—');
            addStat('session-detail-stat--c', 'fa fa-fire', 'Calories', c.calories != null ? detailTxt(c.calories) : '—');
            addStat('session-detail-stat--w', 'fa fa-calendar', 'Program', c.total_duration != null ? detailTxt(c.total_duration) + ' wks' : '—');
            $root.append($stats);

            var $sheet = $('<div class="session-detail-sheet"/>');

            function section(title, iconClass) {
                var $sec = $('<div class="session-detail-section"/>');
                var $h = $('<h5 class="session-detail-section__title"/>');
                $h.append($('<i/>').attr('class', iconClass || 'fa fa-circle-o'));
                $h.append($('<span/>').text(title));
                $sec.append($h);
                return $sec;
            }

            var pillClasses = ['session-detail-pill--a', 'session-detail-pill--b', 'session-detail-pill--c', 'session-detail-pill--d', 'session-detail-pill--e'];

            var $timing = section('Schedule & timing', 'fa fa-clock-o');
            $timing.append(
                $('<div/>').append(
                    $('<span class="session-detail-timing-highlight"/>').text(
                        c.session_timing || '—'
                    )
                )
            );
            $sheet.append($timing);

            var $desc = section('About this session', 'fa fa-align-left');
            $desc.append(
                $('<p class="session-detail-section__text"/>').text(c.description || 'No description.')
            );
            $sheet.append($desc);

            var steps = c.steps || [];
            var $stSec = section('Workout steps', 'fa fa-list-ol');
            if (steps.length) {
                var $ul = $('<ul class="session-detail-steps-wrap"/>');
                $.each(steps, function (i, s) {
                    var $row = $('<li class="session-detail-step"/>');
                    $row.append($('<span class="session-detail-step__num"/>').text(String(i + 1)));
                    $row.append($('<span class="session-detail-step__text"/>').text(detailTxt(s)));
                    $ul.append($row);
                });
                $stSec.append($ul);
            } else {
                $stSec.append($('<p class="session-detail-empty"/>').text('No steps listed.'));
            }
            $sheet.append($stSec);

            var sched = c.schedule || [];
            var $schedSec = section('Weekly schedule', 'fa fa-calendar-check-o');
            var $pills = $('<div class="session-detail-tags"/>');
            if (sched.length) {
                $.each(sched, function (i, d) {
                    $pills.append(
                        $('<span class="session-detail-pill"/>')
                            .addClass(pillClasses[i % pillClasses.length])
                            .text(detailTxt(d))
                    );
                });
            } else {
                $pills.append($('<span class="session-detail-empty"/>').text('No days selected.'));
            }
            $schedSec.append($pills);
            $sheet.append($schedSec);

            function tagSection(title, icon, arr) {
                var $sec = section(title, icon);
                var $wrap = $('<div class="session-detail-tags"/>');
                arr = arr || [];
                if (!arr.length) {
                    $wrap.append($('<span class="session-detail-empty"/>').text('None'));
                } else {
                    $.each(arr, function (i, x) {
                        $wrap.append(
                            $('<span class="session-detail-pill"/>')
                                .addClass(pillClasses[i % pillClasses.length])
                                .text(detailTxt(x))
                        );
                    });
                }
                $sec.append($wrap);
                return $sec;
            }

            $sheet.append(tagSection('Muscles involved', 'fa fa-child', c.muscles_involved));
            $sheet.append(tagSection('Fitness goals', 'fa fa-bullseye', c.fitness_goal));
            $sheet.append(tagSection('Session types', 'fa fa-tags', c.session_type));
            $sheet.append(tagSection('Keywords', 'fa fa-hashtag', c.session_keywords));

            var lat = c.latitude;
            var lng = c.longitude;
            var rad = c.radius;
            if (lat != null && lat !== '' || lng != null && lng !== '' || rad != null && rad !== '') {
                var $loc = section('Location', 'fa fa-map-marker');
                var locTxt = [];
                if (lat != null && lat !== '') locTxt.push('Lat ' + detailTxt(lat));
                if (lng != null && lng !== '') locTxt.push('Lng ' + detailTxt(lng));
                if (rad != null && rad !== '') locTxt.push('Radius ' + detailTxt(rad) + ' km');
                $loc.append($('<p class="session-detail-section__text"/>').text(locTxt.join(' · ') || '—'));
                $sheet.append($loc);
            }

            $root.append($sheet);

            var $meta = $('<div class="session-detail-meta"/>');
            function metaChip(icon, text) {
                var $c = $('<span class="session-detail-meta__chip"/>');
                $c.append($('<i/>').attr('class', icon));
                $c.append(document.createTextNode(text));
                return $c;
            }
            $meta.append(metaChip('fa fa-hashtag', 'ID ' + detailTxt(c.id)));
            if (c.session_avrage_rating != null && String(c.session_avrage_rating) !== '') {
                $meta.append(metaChip('fa fa-star', 'Rating ' + detailTxt(c.session_avrage_rating) + ' / 5'));
            }
            if (c.created_at) {
                $meta.append(metaChip('fa fa-plus-circle', 'Created ' + detailTxt(c.created_at)));
            }
            if (c.updated_at) {
                $meta.append(metaChip('fa fa-refresh', 'Updated ' + detailTxt(c.updated_at)));
            }
            $root.append($meta);

            return $root;
        }

        $(document).on('click', '.btn-view-class', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            $.get("{{ url('admin/classes') }}/" + id, function (res) {
                var c = res.class;
                $('#viewClassModalTitle').text(c.session_title ? detailTxt(c.session_title) : 'Session details');
                $('#viewClassContent').empty().append(renderSessionDetailView(c));
                $('#viewClassModal').modal('show');
            }).fail(toastErrors);
        });

        $(document).on('click', '.btn-delete-class', function (e) {
            e.preventDefault();
            var id = $(this).data('id');
            if (!confirm('Delete this session?')) return;
            $.ajax({
                url: "{{ url('admin/classes') }}/" + id,
                method: 'POST',
                data: {_method: 'DELETE'},
                success: function (res) {
                    toastr.success(res.message || 'Deleted.');
                    window.location.reload();
                },
                error: toastErrors
            });
        });

        function renderCatalogRows(items) {
            var $tb = $('#catalogItemsTable tbody');
            $tb.empty();
            if (!items || !items.length) {
                $tb.append('<tr><td colspan="3" class="text-muted">No items yet.</td></tr>');
                return;
            }
            $.each(items, function (_, it) {
                var row = $('<tr/>');
                row.append($('<td/>').text(it.name));
                row.append($('<td/>').text(it.sort_order));
                var $btn = $('<button type="button" class="btn btn-xs btn-danger btn-catalog-del">Remove</button>');
                $btn.data('id', it.id);
                row.append($('<td/>').append($btn));
                $tb.append(row);
            });
        }

        function loadCatalogList(type) {
            $.get("{{ route('admin.session-catalog.index') }}", {type: type}, function (res) {
                renderCatalogRows(res.items || []);
            }).fail(toastErrors);
        }

        $('#manageCatalogModal').on('shown.bs.modal', function () {
            loadCatalogList($('#catalog_list_type').val());
        });

        $('#catalog_list_type').on('change', function () {
            loadCatalogList($(this).val());
        });

        $('#catalogAddForm').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: "{{ route('admin.session-catalog.store') }}",
                method: 'POST',
                data: $(this).serialize(),
                success: function (res) {
                    toastr.success(res.message || 'Added.');
                    $('#catalog_new_name').val('');
                    window.location.reload();
                },
                error: toastErrors
            });
        });

        $(document).on('click', '.btn-catalog-del', function () {
            var id = $(this).data('id');
            if (!confirm('Remove this option? Existing sessions keep their saved JSON values.')) return;
            $.ajax({
                url: "{{ url('admin/session-catalog') }}/" + id,
                method: 'POST',
                data: {_method: 'DELETE'},
                success: function () {
                    toastr.success('Removed.');
                    window.location.reload();
                },
                error: toastErrors
            });
        });
    });
</script>
