<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Laporan extends Model
{
    protected $table = 'laporan';
    protected $primaryKey = 'idlaporan';
    public $timestamps = true;

    protected $fillable = [
        'iduser',
        'periode',
        'jenis_laporan',
        'file_path',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'iduser', 'iduser');
    }

    // Scopes
    public function scopeByJenis($query, $jenis)
    {
        return $query->where('jenis_laporan', $jenis);
    }

    public function scopeTerbaru($query)
    {
        return $query->latest('tanggal_generate');
    }
}