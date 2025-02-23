<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Bookmark extends Model
{
    use hasFactory;

    protected $table = 'bookmarks';

    protected $fillable = ["user_id", "session_id"];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function session() {
        return  $this->belongsTo(Classes::class);
    }
}
