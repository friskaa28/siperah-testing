<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distribusi extends Model
{
    protected $table = 'distribusi';
    protected $primaryKey = 'iddistribusi';
    public $timestamps = true;

    protected $fillable = [
        'idpeternak',
        'tujuan',
        'volume',
        'harga_per_liter',
        'tanggal_kirim',
        'status',
        'catatan',
    ];

    protected $casts = [
        'volume' => 'decimal:2',
        'harga_per_liter' => 'decimal:2',
        'total_penjualan' => 'decimal:2',
        'tanggal_kirim' => 'date',
    ];

    // Relationships
    public function peternak()
    {
        return $this->belongsTo(Peternak::class, 'idpeternak', 'idpeternak');
    }

    // Scopes
    public function scopeBulanIni($query)
    {
        return $query->whereMonth('tanggal_kirim', now()->month)
                     ->whereYear('tanggal_kirim', now()->year);
    }

    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_kirim', now());
    }

    // Methods
    public static function totalPerHari($tanggal = null)
    {
        $tanggal = $tanggal ?? now()->format('Y-m-d');
        return self::whereDate('tanggal_kirim', $tanggal)->sum('total_penjualan');
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->volume && $model->harga_per_liter) {
                $model->total_penjualan = $model->volume * $model->harga_per_liter;
            }
        });

        static::saved(function ($model) {
            $produksi = ProduksiHarian::where('idpeternak', $model->idpeternak)
                ->whereDate('tanggal', $model->tanggal_kirim)
                ->first();

            if ($produksi) {
                BagiHasil::hitungBagiHasil($produksi, 60, 40);
            }
        });
    }
}
