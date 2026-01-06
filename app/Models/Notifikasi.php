<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'idnotif';
    public $timestamps = true;

    protected $fillable = [
        'iduser',
        'judul',
        'pesan',
        'tipe',
        'kategori',
        'status_baca',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'iduser', 'iduser');
    }

    // Scopes
    public function scopeBelumDibaca($query)
    {
        return $query->where('status_baca', 'belum_baca');
    }

    public function scopeByKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    // Methods
    public function markAsRead()
    {
        $this->update(['status_baca' => 'sudah_baca']);
        return $this;
    }
}
