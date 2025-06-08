<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExerciseCategory extends Model
{
    public function exercises()
    {
        return $this->hasMany(Exercise::class, 'exercise_category_id');
    }
}
