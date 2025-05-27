<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NutritionTarget extends Model
{
    protected $fillable = ['user_id', 'calories', 'proteins', 'fats', 'carbs'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
