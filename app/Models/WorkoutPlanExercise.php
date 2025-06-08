<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutPlanExercise extends Model
{
    protected $fillable = [
        'workout_plan_id',
        'exercise_id',
        'sets',
        'reps',
        'rest_seconds',
        'weight'
    ];
    public function plan()
    {
        return $this->belongsTo(WorkoutPlan::class, 'workout_plan_id');
    }

    public function exercise()
    {
        return $this->belongsTo(Exercise::class);
    }
}
