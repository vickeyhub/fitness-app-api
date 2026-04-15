<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'email',
        'name',
        'payment_intent_id',
        'status',
        'amount',
        'currency',
        'payment_method',
        'response_data'
    ];

    protected $casts = [
        'response_data' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking() {
        return $this->belongsTo(Booking::class, 'payment_intent_id', 'payment_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'payment_row_id');
    }
}
