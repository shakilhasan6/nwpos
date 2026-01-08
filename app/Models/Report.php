<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'pubali_id',
        'tid',
        'mid',
        'merchent',
        'address',
        'officer',
        'number',
        'pos_s',
        'engineer_name',
        'engineer_contact',
        'assignment_date',
        'status',
        'bank',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
