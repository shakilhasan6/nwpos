<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EngineerLog extends Model
{
    protected $fillable = [
        'engineer_name',
        'entries',
        'grand_total',
        'status',
        'verify',
        'completed',
        'submitted_at',
        'user_id',
        'log_month'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
        'entries' => 'array',
        'submitted_at' => 'datetime',
    ];
}
