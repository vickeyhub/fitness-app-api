<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id', 'trainer_id', 'gym_id', 'session_id', 'payment_id', 'booking_date', 'time_slot', 'status','payment_status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function trainer()
    {
        return $this->belongsTo(User::class, 'trainer_id')->where('user_type', 'trainer');
    }

    public function gym()
    {
        return $this->belongsTo(User::class, 'gym_id')->where('user_type', 'gym');
    }
}
