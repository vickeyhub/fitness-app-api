@extends('layouts.app')

@section('title', 'My profile')
@section('heading', 'My profile')

@section('content')
    @php
        $p = $user->profile;
        $pic = $p?->profile_picture;
        $picUrl = $pic ? (str_starts_with($pic, 'http') ? $pic : asset('storage/'.ltrim($pic, '/'))) : null;
        $isTrainerOrGym = in_array($user->user_type, ['trainer', 'gym'], true);
    @endphp

    <div class="fx-glass-strong mx-auto max-w-3xl rounded-3xl p-6 sm:p-8">
        <form method="post" action="{{ route('app.profile.update') }}" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <div class="flex flex-col gap-6 sm:flex-row sm:items-start">
                <div class="flex h-28 w-28 shrink-0 overflow-hidden rounded-2xl border border-white/10 bg-fx-850">
                    @if ($picUrl)
                        <img src="{{ $picUrl }}" alt="" class="h-full w-full object-cover">
                    @else
                        <div class="flex h-full w-full items-center justify-center font-display text-3xl font-bold text-zinc-600">{{ substr($user->first_name ?? '?', 0, 1) }}</div>
                    @endif
                </div>
                <div class="flex-1">
                    <label class="fx-label" for="file">Profile photo</label>
                    <input class="fx-input cursor-pointer text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-teal-500/20 file:px-4 file:py-2 file:text-sm file:font-medium file:text-teal-200" type="file" name="file" id="file" accept="image/jpeg,image/png,image/jpg">
                    <p class="mt-2 text-xs text-zinc-600">JPEG, PNG up to 5 MB.</p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="fx-label" for="first_name">First name</label>
                    <input class="fx-input" type="text" name="first_name" id="first_name" value="{{ old('first_name', $user->first_name) }}" required>
                    @error('first_name')
                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="fx-label" for="last_name">Last name</label>
                    <input class="fx-input" type="text" name="last_name" id="last_name" value="{{ old('last_name', $user->last_name) }}" required>
                    @error('last_name')
                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="fx-label" for="age">Age</label>
                    <input class="fx-input" type="number" name="age" id="age" value="{{ old('age', $p?->age) }}" min="1" max="150">
                    @error('age')
                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="fx-label" for="dob">Date of birth</label>
                    <input class="fx-input" type="date" name="dob" id="dob" value="{{ old('dob', $p?->dob ? \Illuminate\Support\Str::substr($p->dob, 0, 10) : '') }}">
                    @error('dob')
                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="fx-label" for="gender">Gender</label>
                <select class="fx-input" name="gender" id="gender">
                    <option value="">—</option>
                    @foreach (['male' => 'Male', 'female' => 'Female', 'other' => 'Other'] as $val => $label)
                        <option value="{{ $val }}" @selected(old('gender', $p?->gender) === $val)>{{ $label }}</option>
                    @endforeach
                </select>
                @error('gender')
                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="fx-label" for="height">Height</label>
                    <input class="fx-input" type="number" step="0.01" name="height" id="height" value="{{ old('height', $p?->height) }}">
                    @error('height')
                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="fx-label" for="height_parameter">Height unit</label>
                    <select class="fx-input" name="height_parameter" id="height_parameter">
                        <option value="">—</option>
                        @foreach (['cm' => 'cm', 'inch' => 'inch'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('height_parameter', $p?->height_parameter) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="fx-label" for="weight">Weight</label>
                    <input class="fx-input" type="number" step="0.01" name="weight" id="weight" value="{{ old('weight', $p?->weight) }}">
                    @error('weight')
                        <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="fx-label" for="weight_parameter">Weight unit</label>
                    <select class="fx-input" name="weight_parameter" id="weight_parameter">
                        <option value="">—</option>
                        @foreach (['kg' => 'kg', 'lb' => 'lb'] as $val => $label)
                            <option value="{{ $val }}" @selected(old('weight_parameter', $p?->weight_parameter) === $val)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="fx-label" for="location">Location</label>
                <input class="fx-input" type="text" name="location" id="location" value="{{ old('location', $p?->location) }}" maxlength="255">
                @error('location')
                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="fx-label" for="user_description">About you</label>
                <textarea class="fx-input min-h-[100px]" name="user_description" id="user_description" maxlength="500">{{ old('user_description', $p?->user_description) }}</textarea>
                @error('user_description')
                    <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                @enderror
            </div>

            @if ($isTrainerOrGym)
                <div class="rounded-2xl border border-white/10 bg-white/[0.02] p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-teal-500/90">Trainer / gym profile</p>
                    <div class="mt-4 space-y-4">
                        <div>
                            <label class="fx-label" for="specialties">Specialties</label>
                            <input class="fx-input" type="text" name="specialties" id="specialties" value="{{ old('specialties', $p?->specialties) }}" placeholder="e.g. strength, mobility">
                            @error('specialties')
                                <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="fx-label" for="trainer_services">Services offered</label>
                            <input class="fx-input" type="text" name="trainer_services" id="trainer_services" value="{{ old('trainer_services', $p?->trainer_services) }}">
                            @error('trainer_services')
                                <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="fx-label" for="experience_level">Experience level</label>
                            <select class="fx-input" name="experience_level" id="experience_level">
                                <option value="">—</option>
                                @foreach (['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'] as $val => $label)
                                    <option value="{{ $val }}" @selected(old('experience_level', $p?->experience_level) === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('experience_level')
                                <p class="mt-2 text-sm text-rose-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            @endif

            <div class="flex flex-wrap gap-3">
                <button type="submit" class="fx-btn-primary px-8">Save changes</button>
                <a href="{{ route('app.dashboard') }}" class="fx-btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
@endsection
