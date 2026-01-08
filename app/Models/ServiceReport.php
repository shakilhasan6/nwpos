<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone_name',
        'bank_name',
        'engineer_name',
        'tid',
        'pos_serial',
        'merchant_address',
        'service_type',
        'remarks',
        'service_report_image_path',
    ];
}