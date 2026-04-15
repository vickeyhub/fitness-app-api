<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classes extends Model
{
    use SoftDeletes;
    protected $table = 'classes';

    protected $guarded = [];

    protected $casts = [
        'steps' => 'array',
        'muscles_involved' => 'array',
        'schedule' => 'array',
        "session_type" => "array",
        "session_keywords" => "array",
        "fitness_goal" => "array",
        'price' => 'float',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }
    public function user_profile(){
        // return $this->belongsTo(UserProfile::class);
        return $this->belongsTo(UserProfile::class, 'user_id', 'user_id');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'session_id');
    }
}
