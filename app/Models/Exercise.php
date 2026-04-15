<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $fillable = ['name', 'exercise_category_id', 'description'];

    public function category()
    {
        return $this->belongsTo(ExerciseCategory::class, 'exercise_category_id');
    }
}
