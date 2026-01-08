<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EblData extends Model
{
    use HasFactory;

    protected $table = 'ebl_data';

    protected $fillable = [
        'tid',
        'mid',
        'merchent',
        'address',
        'officer',
        'number',
        'pos_s',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}