<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'iduser';
    public $timestamps = true;
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    protected $fillable = [
        'nama',
        'email',
        'password',
        'role',
        'nohp',
        'alamat',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // Relationships
    public function peternak()
    {
        return $this->hasOne(Peternak::class, 'iduser', 'iduser');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'iduser', 'iduser');
    }

    public function laporan()
    {
        return $this->hasMany(Laporan::class, 'iduser', 'iduser');
    }

    // Role checkers
    public function isPeternak()
    {
        return $this->role === 'peternak';
    }

    public function isPengelola()
    {
        return $this->role === 'pengelola';
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isAnalytics()
    {
        return $this->role === 'tim_analytics';
    }

    public function isSubPenampung()
    {
        return $this->peternak && 
               in_array($this->peternak->status_mitra, ['sub_penampung', 'sub_penampung_tr', 'sub_penampung_p']);
    }
}
