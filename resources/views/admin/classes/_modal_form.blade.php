@php
    $pf = $isEdit ? 'edit_class_' : 'add_class_';
    $muscles = $catalogByType['muscle'] ?? collect();
    $goals = $catalogByType['fitness_goal'] ?? collect();
    $types = $catalogByType['session_type'] ?? collect();
    $keywords = $catalogByType['keyword'] ?? collect();
    $days = ['MON' => 'Mon', 'TUE' => 'Tue', 'WED' => 'Wed', 'THU' => 'Thu', 'FRI' => 'Fri', 'SAT' => 'Sat', 'SUN' => 'Sun'];
@endphp
<div id="{{ $modalId }}" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{{ $title }}</h4>
            </div>
            <div class="modal-body" style="max-height:70vh;overflow-y:auto;">
                <form id="{{ $formId }}" class="m-t class-session-form" role="form" data-prefix="{{ $pf }}">
                    @if ($isEdit)
                        <input type="hidden" name="class_id" id="{{ $pf }}id">
                    @endif
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                <label>Session title <span class="text-danger">*</span></label>
                                <input type="text" name="session_title" id="{{ $pf }}session_title" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Trainer <span class="text-danger">*</span></label>
                                <select name="user_id" id="{{ $pf }}user_id" class="form-control" required>
                                    <option value="">—</option>
                                    @foreach ($trainers as $t)
                                        <option value="{{ $t->id }}">{{ $t->first_name }} {{ $t->last_name }} ({{ $t->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Description <span class="text-danger">*</span></label>
                        <textarea name="description" id="{{ $pf }}description" class="form-control" rows="3" required></textarea>
                    </div>

                    <h4 class="m-t-md">Steps</h4>
                    <p class="text-muted small">Add each part of the workout in order (e.g. warm-up, main block, cool-down).</p>
                    <div id="{{ $pf }}step_rows" class="step-rows well well-sm">
                        <div class="form-group step-row m-b-xs">
                            <div class="input-group">
                                <input type="text" name="step_lines[]" class="form-control" placeholder="e.g. 5 min warm-up" maxlength="500">
                                <span class="input-group-btn">
                                    <button type="button" class="btn btn-default btn-remove-step" tabindex="-1"><i class="fa fa-times"></i></button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-white btn-sm m-b-md btn-add-step" data-prefix="{{ $pf }}"><i class="fa fa-plus"></i> Add step</button>

                    <h4 class="m-t-md">Schedule</h4>
                    <p class="text-muted small">Which days does this session run?</p>
                    <div class="well well-sm m-b-md">
                        @foreach ($days as $val => $label)
                            <label class="checkbox-inline m-r-sm">
                                <input type="checkbox" name="schedule_days[]" value="{{ $val }}" class="{{ $pf }}schedule-day"> {{ $label }}
                            </label>
                        @endforeach
                    </div>

                    <h4 class="m-t-md">Muscles involved</h4>
                    <div class="well well-sm m-b-md" style="max-height:140px;overflow-y:auto;">
                        @forelse ($muscles as $item)
                            <label class="checkbox-inline m-r-sm m-b-xs">
                                <input type="checkbox" name="muscle_names[]" value="{{ $item->name }}" class="{{ $pf }}cat-muscle"> {{ $item->name }}
                            </label>
                        @empty
                            <p class="text-warning">No muscles in catalog. Open <strong>Manage options</strong> to add some.</p>
                        @endforelse
                    </div>

                    <h4 class="m-t-md">Fitness goals</h4>
                    <div class="well well-sm m-b-md" style="max-height:140px;overflow-y:auto;">
                        @forelse ($goals as $item)
                            <label class="checkbox-inline m-r-sm m-b-xs">
                                <input type="checkbox" name="fitness_goal_names[]" value="{{ $item->name }}" class="{{ $pf }}cat-goal"> {{ $item->name }}
                            </label>
                        @empty
                            <p class="text-warning">No goals in catalog. Use <strong>Manage options</strong>.</p>
                        @endforelse
                    </div>

                    <h4 class="m-t-md">Session types</h4>
                    <div class="well well-sm m-b-md" style="max-height:140px;overflow-y:auto;">
                        @forelse ($types as $item)
                            <label class="checkbox-inline m-r-sm m-b-xs">
                                <input type="checkbox" name="session_type_names[]" value="{{ $item->name }}" class="{{ $pf }}cat-type"> {{ $item->name }}
                            </label>
                        @empty
                            <p class="text-warning">No session types in catalog.</p>
                        @endforelse
                    </div>

                    <h4 class="m-t-md">Keywords</h4>
                    <p class="text-muted small">Tick common tags and/or add your own (comma separated).</p>
                    <div class="well well-sm m-b-sm" style="max-height:120px;overflow-y:auto;">
                        @forelse ($keywords as $item)
                            <label class="checkbox-inline m-r-sm m-b-xs">
                                <input type="checkbox" name="keyword_names[]" value="{{ $item->name }}" class="{{ $pf }}cat-keyword"> {{ $item->name }}
                            </label>
                        @empty
                            <p class="text-muted">No keyword presets.</p>
                        @endforelse
                    </div>
                    <div class="form-group">
                        <label>Custom keywords</label>
                        <input type="text" name="keyword_custom" id="{{ $pf }}keyword_custom" class="form-control" placeholder="e.g. morning only, women only">
                    </div>

                    <hr>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Total duration (weeks) <span class="text-danger">*</span></label>
                                <input type="number" name="total_duration" id="{{ $pf }}total_duration" class="form-control" min="1" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Calories <span class="text-danger">*</span></label>
                                <input type="number" name="calories" id="{{ $pf }}calories" class="form-control" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Price <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" name="price" id="{{ $pf }}price" class="form-control" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Rating (0–5)</label>
                                <input type="number" step="0.1" name="session_avrage_rating" id="{{ $pf }}session_avrage_rating" class="form-control" min="0" max="5">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Start time <span class="text-danger">*</span></label>
                                <input type="time" name="start_time" id="{{ $pf }}start_time" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>End time <span class="text-danger">*</span></label>
                                <input type="time" name="end_time" id="{{ $pf }}end_time" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Session timing preview</label>
                        <input type="text" id="{{ $pf }}session_timing_preview" class="form-control" placeholder="e.g. 07:00am - 08:30am" readonly>
                        <input type="hidden" name="session_timing" id="{{ $pf }}session_timing" required>
                        <span class="help-block m-b-none">Stored as start/end times and displayed as a formatted range.</span>
                    </div>
                    <div class="form-group">
                        <label>Duration (minutes)</label>
                        <input type="number" name="duration" id="{{ $pf }}duration" class="form-control" min="1" placeholder="Optional — overrides timing math">
                    </div>
                    <div class="form-group">
                        <label>Thumbnail URL / path <span class="text-danger">*</span></label>
                        <input type="text" name="session_thumbnail" id="{{ $pf }}session_thumbnail" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Intensity <span class="text-danger">*</span></label>
                                <input type="text" name="intensity" id="{{ $pf }}intensity" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Published <span class="text-danger">*</span></label>
                                <select name="is_publish" id="{{ $pf }}is_publish" class="form-control" required>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Latitude</label>
                                <input type="text" name="latitude" id="{{ $pf }}latitude" class="form-control" placeholder="Optional">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Longitude</label>
                                <input type="text" name="longitude" id="{{ $pf }}longitude" class="form-control" placeholder="Optional">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Radius (km)</label>
                                <input type="number" step="1" name="radius" id="{{ $pf }}radius" class="form-control" min="0" placeholder="Optional">
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> {{ $submitLabel }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
