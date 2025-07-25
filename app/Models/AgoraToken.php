<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgoraToken extends Model
{
    use HasFactory;

    protected $table = 'agora_tokens';

    protected $fillable = [
        'user_id',
        'agora_uid',
        'token',
        'generated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
