<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';

    protected $fillable = [
        'isi',
        'id_admin',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'id_admin', 'iduser');
    }
}
