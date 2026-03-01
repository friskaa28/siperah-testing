<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model
{
    protected $table = 'user_sessions_kpi';

    protected $fillable = [
        'user_id',
        'login_at',
        'logout_at',
        'duration_seconds',
        'ip_address',
        'user_agent',
        'session_token',
    ];

    protected $casts = [
        'login_at'  => 'datetime',
        'logout_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'iduser');
    }

    /**
     * Duration in minutes (rounded)
     */
    public function getDurationMinutesAttribute(): float
    {
        return round(($this->duration_seconds ?? 0) / 60, 1);
    }
}
