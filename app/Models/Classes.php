<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classes extends Model
{
    protected $table = 'classes';

    protected $guarded = [];

    protected $casts = [
        'steps' => 'array',
        'muscles_involved' => 'array',
        'schedule' => 'array',
    ];

}
