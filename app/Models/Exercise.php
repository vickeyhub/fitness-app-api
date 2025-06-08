<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    public function category()
    {
        return $this->belongsTo(ExerciseCategory::class, 'exercise_category_id');
    }
}
