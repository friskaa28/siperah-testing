<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peternak extends Model
{
    protected $table = 'peternak';
    protected $primaryKey = 'idpeternak';
    public $timestamps = true;

    protected $fillable = [
        'iduser',
        'nama_peternak',
        'no_peternak',
        'kelompok',
        'jumlah_sapi',
        'lokasi',
        'koperasi_id',
        'status_mitra',
        'id_sub_penampung',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'iduser', 'iduser');
    }

    public function produksi()
    {
        return $this->hasMany(ProduksiHarian::class, 'idpeternak', 'idpeternak');
    }

    public function distribusi()
    {
        return $this->hasMany(Distribusi::class, 'idpeternak', 'idpeternak');
    }

    public function kasbon()
    {
        return $this->hasMany(Kasbon::class, 'idpeternak', 'idpeternak');
    }

    public function subPenampung()
    {
        return $this->belongsTo(Peternak::class, 'id_sub_penampung', 'idpeternak');
    }

    public function anggota()
    {
        return $this->hasMany(Peternak::class, 'id_sub_penampung', 'idpeternak');
    }

    // Scopes
    public function scopeBulanIni($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    protected static function booted()
    {
        // Generate no_peternak automatically if empty
        static::created(function ($peternak) {
            if (empty($peternak->no_peternak)) {
                $prefix = 'MTR-';
                if ($peternak->status_mitra === 'sub_penampung_tr') {
                    $prefix = 'TR-';
                } elseif ($peternak->status_mitra === 'sub_penampung_p') {
                    $prefix = 'P-';
                } elseif ($peternak->status_mitra === 'sub_penampung') {
                    $prefix = 'SUB-';
                }
                
                $peternak->no_peternak = $prefix . str_pad($peternak->idpeternak, 3, '0', STR_PAD_LEFT);
                $peternak->saveQuietly();
            }
        });
    }

    // Methods
    public function getTotalProduksiBulanan()
    {
        return $this->produksi()
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('jumlah_susu_liter');
    }

    public function getTotalPendapatanBulanan()
    {
        // Join to bagi_hasil via produksi
        return BagiHasil::whereHas('produksi', function($q) {
                $q->where('idpeternak', $this->idpeternak);
            })
            ->whereMonth('tanggal', now()->month)
            ->whereYear('tanggal', now()->year)
            ->sum('hasil_pemilik');
    }

    public function getRataRataHargaBulan()
    {
        $total = $this->distribusi()
            ->whereMonth('tanggal_kirim', now()->month)
            ->whereYear('tanggal_kirim', now()->year)
            ->sum('total_penjualan');

        $count = $this->distribusi()
            ->whereMonth('tanggal_kirim', now()->month)
            ->whereYear('tanggal_kirim', now()->year)
            ->count();

        return $count > 0 ? $total / $count : 0;
    }
}
