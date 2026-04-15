<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\NutritionTarget;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class NutritionAdherenceController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'q' => ['nullable', 'string', 'max:255'],
            'from_date' => ['nullable', 'date'],
            'to_date' => ['nullable', 'date'],
            'per_page' => ['nullable', 'integer', 'in:10,15,25,50,100'],
        ]);

        $fromDate = $filters['from_date'] ?? Carbon::now()->startOfMonth()->toDateString();
        $toDate = $filters['to_date'] ?? Carbon::now()->toDateString();

        $usersQuery = User::query()->orderBy('first_name');
        if (!empty($filters['user_id'])) {
            $usersQuery->where('id', (int) $filters['user_id']);
        }
        if (!empty($filters['q'])) {
            $q = trim((string) $filters['q']);
            $usersQuery->where(function ($inner) use ($q) {
                $inner->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        $users = $usersQuery->paginate((int) ($filters['per_page'] ?? 15))->appends($request->query());
        $userIds = $users->pluck('id')->all();

        $consumedByUser = Meal::query()
            ->selectRaw('user_id, SUM(calories) as calories, SUM(proteins) as proteins, SUM(fats) as fats, SUM(carbs) as carbs')
            ->whereIn('user_id', $userIds)
            ->whereBetween('date', [$fromDate, $toDate])
            ->groupBy('user_id')
            ->get()
            ->keyBy('user_id');

        $targetRows = NutritionTarget::query()
            ->whereIn('user_id', $userIds)
            ->orderByDesc('id')
            ->get()
            ->groupBy('user_id')
            ->map(fn ($rows) => $rows->first());

        $users->setCollection($users->getCollection()->map(function ($user) use ($consumedByUser, $targetRows) {
            $consumed = $consumedByUser->get($user->id);
            $target = $targetRows->get($user->id);

            $targetCalories = (int) ($target->calories ?? 2000);
            $targetProteins = (int) ($target->proteins ?? 100);
            $targetFats = (int) ($target->fats ?? 70);
            $targetCarbs = (int) ($target->carbs ?? 250);

            $consumedCalories = (int) ($consumed->calories ?? 0);
            $consumedProteins = (int) ($consumed->proteins ?? 0);
            $consumedFats = (int) ($consumed->fats ?? 0);
            $consumedCarbs = (int) ($consumed->carbs ?? 0);

            $percent = fn (int $consumedValue, int $targetValue) => $targetValue > 0 ? round(($consumedValue / $targetValue) * 100, 1) : 0;

            $user->adherence = [
                'target' => [
                    'calories' => $targetCalories,
                    'proteins' => $targetProteins,
                    'fats' => $targetFats,
                    'carbs' => $targetCarbs,
                ],
                'consumed' => [
                    'calories' => $consumedCalories,
                    'proteins' => $consumedProteins,
                    'fats' => $consumedFats,
                    'carbs' => $consumedCarbs,
                ],
                'percent' => [
                    'calories' => $percent($consumedCalories, $targetCalories),
                    'proteins' => $percent($consumedProteins, $targetProteins),
                    'fats' => $percent($consumedFats, $targetFats),
                    'carbs' => $percent($consumedCarbs, $targetCarbs),
                ],
            ];

            return $user;
        }));

        return view('admin.nutrition.adherence', [
            'rows' => $users,
            'users' => User::query()->orderBy('first_name')->get(['id', 'first_name', 'last_name', 'email']),
            'filters' => array_merge($filters, ['from_date' => $fromDate, 'to_date' => $toDate]),
        ]);
    }
}
