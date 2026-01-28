<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduksiHarian extends Model
{
    protected $table = 'produksi_harian';
    protected $primaryKey = 'idproduksi';
    public $timestamps = true;

    protected $fillable = [
        'idpeternak',
        'tanggal',
        'jumlah_susu_liter',
        'biaya_pakan',
        'biaya_tenaga',
        'biaya_operasional',
        'foto_bukti',
        'catatan',
        'waktu_setor',
    ];

    protected $casts = [
        'jumlah_susu_liter' => 'decimal:3',
        'biaya_pakan' => 'decimal:2',
        'biaya_tenaga' => 'decimal:2',
        'biaya_operasional' => 'decimal:2',
        'total_biaya' => 'decimal:2',
        'tanggal' => 'date',
    ];

    // Relationships
    public function peternak()
    {
        return $this->belongsTo(Peternak::class, 'idpeternak', 'idpeternak');
    }

    public function bagiHasil()
    {
        return $this->hasOne(BagiHasil::class, 'idproduksi', 'idproduksi');
    }

    // Accessors
    public function getHargaPerLiterAttribute()
    {
        if ($this->jumlah_susu_liter == 0) return 0;
        $distribusi = Distribusi::where('idpeternak', $this->idpeternak)
            ->whereDate('tanggal_kirim', $this->tanggal)
            ->first();
        return $distribusi ? $distribusi->harga_per_liter : 0;
    }

    public function getTotalPendapatanAttribute()
    {
        return $this->jumlah_susu_liter * $this->getHargaPerLiterAttribute();
    }

    // No manual calculation for total_biaya because it is a generated column in DB
}
