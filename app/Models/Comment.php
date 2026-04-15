<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected  $fillable = ['post_id','user_id','comment', 'is_hidden'];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];

    public function post() {
        return $this->belongsTo(Post::class);
    }

    public function user() {
        return $this->belongsTo(User::class);
    }
}
