<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'contract_number',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function pubaliData()
    {
        return $this->hasMany(PubaliData::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function engineerLogs()
    {
        return $this->hasMany(EngineerLog::class);
    }

    public function isAdmin()
    {
        return strtolower($this->role) === 'admin';
    }

    public function isChecker()
    {
        return $this->role === 'checker';
    }

    public function isVerify()
    {
        return $this->role === 'verify';
    }

    public function isEngineer()
    {
        return $this->role === 'engineer';
    }

    public function isAccounts()
    {
        return $this->role === 'accounts';
    }

    public function isPblManager()
    {
        return $this->role === 'pbl_manager';
    }

    public function isMtbManager()
    {
        return $this->role === 'mtb_manager';
    }

    public function isEblManager()
    {
        return $this->role === 'ebl_manager';
    }

    public function isIbblManager()
    {
        return $this->role === 'ibbl_manager';
    }

    public function isCtManager()
    {
        return $this->role === 'ct_manager';
    }
}
