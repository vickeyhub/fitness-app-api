<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;

    protected $fillable = ['title', 'description','thumbnail','user_id', 'is_hidden'];

    protected $casts = [
        'is_hidden' => 'boolean',
    ];
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function tags() {
        return $this->belongsToMany(Tag::class);
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function likes() {
        return $this->hasMany(Like::class);
    }
    public function likedByUser($user_id){
        return $this->likes()->where('user_id', $user_id);
    }
}
