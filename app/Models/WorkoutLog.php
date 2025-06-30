<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutLog extends Model
{
    protected $fillable = [
        'user_id',
        'workout_type',
        'workout_id',
        'start_time',
        'end_time',
        'duration_minutes',
        'calories_burned',
        'notes',
    ];
}
