<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkoutLog extends Model
{
    public const TYPE_PLAN = 'plan';
    public const TYPE_FREE = 'free';
    public const TYPE_CARDIO = 'cardio';

    public const ALLOWED_TYPES = [
        self::TYPE_PLAN,
        self::TYPE_FREE,
        self::TYPE_CARDIO,
    ];

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return array<int, string>
     */
    public static function allowedTypes(): array
    {
        return self::ALLOWED_TYPES;
    }
}
