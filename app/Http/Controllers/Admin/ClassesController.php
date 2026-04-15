<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classes;
use App\Models\SessionCatalogItem;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ClassesController extends Controller
{
    /** @var list<string> */
    private const SCHEDULE_DAY_ORDER = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];

    public function index()
    {
        $classes = Classes::with('user')->orderByDesc('id')->paginate(20);
        $trainers = User::query()
            ->where('user_type', 'trainer')
            ->orderBy('first_name')
            ->get(['id', 'first_name', 'last_name', 'email']);

        $catalogByType = SessionCatalogItem::query()
            ->active()
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get()
            ->groupBy('type');

        return view('admin.classes.index', compact('classes', 'trainers', 'catalogByType'));
    }

    public function show(Classes $classes)
    {
        $classes->load('user');

        return response()->json(['class' => $classes]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedClassPayload($request);
        $class = Classes::create($data);

        return response()->json([
            'message' => 'Session created successfully.',
            'class' => $class->load('user'),
        ], 201);
    }

    public function update(Request $request, Classes $classes)
    {
        $data = $this->validatedClassPayload($request);
        $classes->update($data);

        return response()->json([
            'message' => 'Session updated successfully.',
            'class' => $classes->fresh()->load('user'),
        ]);
    }

    public function destroy(Classes $classes)
    {
        $classes->delete();

        return response()->json(['message' => 'Session deleted successfully.']);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedClassPayload(Request $request): array
    {
        $request->merge([
            'duration' => $request->input('duration') === '' || $request->input('duration') === null
                ? null
                : $request->input('duration'),
            'latitude' => $request->input('latitude') === '' || $request->input('latitude') === null
                ? null
                : $request->input('latitude'),
            'longitude' => $request->input('longitude') === '' || $request->input('longitude') === null
                ? null
                : $request->input('longitude'),
            'radius' => $request->input('radius') === '' || $request->input('radius') === null
                ? null
                : $request->input('radius'),
        ]);

        $muscles = $this->allowedCatalogNames(SessionCatalogItem::TYPE_MUSCLE);
        $goals = $this->allowedCatalogNames(SessionCatalogItem::TYPE_FITNESS_GOAL);
        $types = $this->allowedCatalogNames(SessionCatalogItem::TYPE_SESSION_TYPE);
        $keywords = $this->allowedCatalogNames(SessionCatalogItem::TYPE_KEYWORD);

        $validator = Validator::make($request->all(), [
            'session_title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'total_duration' => ['required', 'integer', 'min:1'],
            'calories' => ['required', 'integer', 'min:0'],
            'step_lines' => ['required', 'array', 'min:1'],
            'step_lines.*' => ['nullable', 'string', 'max:500'],
            'schedule_days' => ['required', 'array', 'min:1'],
            'schedule_days.*' => ['required', 'string', Rule::in(self::SCHEDULE_DAY_ORDER)],
            'muscle_names' => ['required', 'array', 'min:1'],
            'muscle_names.*' => ['required', 'string', Rule::in($muscles)],
            'fitness_goal_names' => ['required', 'array', 'min:1'],
            'fitness_goal_names.*' => ['required', 'string', Rule::in($goals)],
            'session_type_names' => ['required', 'array', 'min:1'],
            'session_type_names.*' => ['required', 'string', Rule::in($types)],
            'keyword_names' => ['nullable', 'array'],
            'keyword_names.*' => ['string', Rule::in($keywords)],
            'keyword_custom' => ['nullable', 'string', 'max:1000'],
            'user_id' => ['required', 'exists:users,id'],
            'price' => ['required', 'numeric', 'min:0'],
            'session_thumbnail' => ['required', 'string', 'max:2048'],
            'session_avrage_rating' => ['nullable', 'numeric', 'min:0', 'max:5'],
            'session_timing' => ['required', 'string', 'max:255'],
            'duration' => ['nullable', 'integer', 'min:1'],
            'intensity' => ['required', 'string', 'max:255'],
            'is_publish' => ['required', 'in:0,1'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
            'radius' => ['nullable', 'numeric', 'min:0'],
        ]);

        $validator->after(function ($validator) use ($request) {
            $fromCatalog = $request->input('keyword_names', []);
            $fromCatalog = is_array($fromCatalog) ? $fromCatalog : [];
            $custom = collect(preg_split('/[,;]/', (string) $request->input('keyword_custom', '')))
                ->map(fn ($s) => trim($s))
                ->filter()
                ->values()
                ->all();
            if (count($fromCatalog) + count($custom) < 1) {
                $validator->errors()->add('keyword_names', 'Pick at least one keyword or add custom keywords (comma separated).');
            }

        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $validated = $validator->validated();

        $trainer = User::where('id', $validated['user_id'])->where('user_type', 'trainer')->first();
        if (! $trainer) {
            throw ValidationException::withMessages([
                'user_id' => ['Selected user must be a trainer.'],
            ]);
        }

        $steps = array_values(array_filter(array_map('trim', $validated['step_lines']), fn ($s) => $s !== ''));
        if ($steps === []) {
            throw ValidationException::withMessages([
                'step_lines' => ['Add at least one step with text.'],
            ]);
        }

        $schedule = $this->orderScheduleDays($validated['schedule_days']);

        $catalogKeywords = array_values(array_unique($validated['keyword_names'] ?? []));
        $customKeywords = collect(preg_split('/[,;]/', (string) ($validated['keyword_custom'] ?? '')))
            ->map(fn ($s) => trim($s))
            ->filter()
            ->values()
            ->all();
        $sessionKeywords = array_values(array_unique(array_merge($catalogKeywords, $customKeywords)));

        $duration = ! empty($validated['duration'])
            ? (int) $validated['duration']
            : $this->computeDurationMinutes($validated['session_timing']);

        return [
            'session_title' => $validated['session_title'],
            'description' => $validated['description'],
            'total_duration' => $validated['total_duration'],
            'calories' => $validated['calories'],
            'steps' => $steps,
            'muscles_involved' => array_values(array_unique($validated['muscle_names'])),
            'schedule' => $schedule,
            'user_id' => (string) $validated['user_id'],
            'price' => $validated['price'],
            'session_thumbnail' => $validated['session_thumbnail'],
            'session_avrage_rating' => $validated['session_avrage_rating'] ?? 0,
            'session_timing' => $validated['session_timing'],
            'session_type' => array_values(array_unique($validated['session_type_names'])),
            'session_keywords' => $sessionKeywords,
            'intensity' => $validated['intensity'],
            'fitness_goal' => array_values(array_unique($validated['fitness_goal_names'])),
            'is_publish' => (string) $validated['is_publish'],
            'latitude' => $validated['latitude'] ?? null,
            'longitude' => $validated['longitude'] ?? null,
            'radius' => isset($validated['radius']) && $validated['radius'] !== null && $validated['radius'] !== ''
                ? (int) $validated['radius']
                : null,
            'duration' => $duration,
        ];
    }

    /**
     * @return list<string>
     */
    private function allowedCatalogNames(string $type): array
    {
        return SessionCatalogItem::query()
            ->active()
            ->ofType($type)
            ->pluck('name')
            ->all();
    }

    /**
     * @param  list<string>  $days
     * @return list<string>
     */
    private function orderScheduleDays(array $days): array
    {
        $order = array_flip(self::SCHEDULE_DAY_ORDER);
        $unique = array_values(array_unique($days));
        usort($unique, fn ($a, $b) => ($order[$a] ?? 99) <=> ($order[$b] ?? 99));

        return $unique;
    }

    private function computeDurationMinutes(string $sessionTiming): int
    {
        $parts = explode(' - ', $sessionTiming);
        if (count($parts) !== 2) {
            throw ValidationException::withMessages([
                'session_timing' => ['Use format like "04:00am - 05:00am".'],
            ]);
        }

        try {
            $start = Carbon::createFromFormat('h:ia', strtolower(trim($parts[0])));
            $end = Carbon::createFromFormat('h:ia', strtolower(trim($parts[1])));
            return (int) $start->diffInMinutes($end);
        } catch (\Throwable) {
            throw ValidationException::withMessages([
                'session_timing' => ['Could not parse times. Use format like "04:00am - 05:00am".'],
            ]);
        }
    }
}
