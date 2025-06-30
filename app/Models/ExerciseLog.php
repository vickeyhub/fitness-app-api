<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExerciseLog extends Model
{
    protected $fillable = [
        'workout_id',
        'exercise_name',
        'sets',
        'reps_per_set',
        'weight_kg',
        'duration_seconds',
        'distance_km',
        'notes',
    ];

    // public function workout()
    // {
    //     return $this->belongsTo(WorkoutLog::class);
    // }
}
