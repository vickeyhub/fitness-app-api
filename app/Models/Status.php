<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['user_id', 'type', 'media', 'caption', 'is_hidden'];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->with('profile');
    }
}
