<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Advance extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'user_id',
        'purpose',
        'payment_method',
        'payment_number',
        'status',
        'amount',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
