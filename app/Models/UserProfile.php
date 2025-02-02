<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserProfile extends Model
{
    use HasFactory;

    protected $table = 'user_profile';
    protected $fillable = [
        'user_id',
        'profile_picture',
        'age',
        'weight',
        'weight_parameter',
        'height',
        'height_parameter',
    ];

    /**
     * Define relationship: A UserProfile belongs to one User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
