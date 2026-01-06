<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BagiHasil extends Model
{
    protected $table = 'bagi_hasil';
    protected $primaryKey = 'idbagi_hasil';
    public $timestamps = true;

    protected $fillable = [
        'idproduksi',
        'tanggal',
        'persentase_pemilik',
        'persentase_pengelola',
        'total_pendapatan',
        'status',
    ];

    protected $casts = [
        'persentase_pemilik' => 'decimal:2',
        'persentase_pengelola' => 'decimal:2',
        'total_pendapatan' => 'decimal:2',
        'hasil_pemilik' => 'decimal:2',
        'hasil_pengelola' => 'decimal:2',
        'tanggal' => 'date',
    ];

    // Relationships
    public function produksi()
    {
        return $this->belongsTo(ProduksiHarian::class, 'idproduksi', 'idproduksi');
    }

    // Scopes
    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal', now()->month)
                     ->whereYear('tanggal', now()->year);
    }

    // Static method untuk hitung bagi hasil
    public static function hitungBagiHasil(ProduksiHarian $produksi, $persentasePemilik = 60, $persentasePengelola = 40)
    {
        $distribusi = Distribusi::where('idpeternak', $produksi->idpeternak)
            ->whereDate('tanggal_kirim', $produksi->tanggal)
            ->first();

        if (!$distribusi) {
            return null;
        }

        $totalPendapatan = $distribusi->total_penjualan;

        $bagiHasil = self::updateOrCreate(
            ['idproduksi' => $produksi->idproduksi],
            [
                'tanggal' => $produksi->tanggal,
                'persentase_pemilik' => $persentasePemilik,
                'persentase_pengelola' => $persentasePengelola,
                'total_pendapatan' => $totalPendapatan,
                'status' => 'pending',
            ]
        );

        return $bagiHasil;
    }
}