<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SlipPembayaran extends Model
{
    protected $table = 'slip_pembayaran';
    protected $primaryKey = 'idslip';

    protected $fillable = [
        'idpeternak',
        'bulan',
        'tahun',
        'jumlah_susu',
        'harga_satuan',
        'total_pembayaran',
        'potongan_shr',
        'potongan_hutang_bl_ll',
        'potongan_pakan_a',
        'potongan_pakan_b',
        'potongan_vitamix',
        'potongan_konsentrat',
        'potongan_skim',
        'potongan_ib_keswan',
        'potongan_susu_a',
        'potongan_kas_bon',
        'potongan_pakan_b_2',
        'potongan_sp',
        'potongan_karpet',
        'potongan_vaksin',
        'potongan_lain_lain',
        'total_potongan',
        'sisa_pembayaran',
        'status', // pending, dibayar
        'tanggal_bayar',
        'signed_by',
        'signed_at',
        'signature_token',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah_susu' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'total_pembayaran' => 'decimal:2',
        'total_potongan' => 'decimal:2',
        'sisa_pembayaran' => 'decimal:2',
        'signed_at' => 'datetime',
    ];

    public function peternak()
    {
        return $this->belongsTo(Peternak::class, 'idpeternak', 'idpeternak');
    }

    public function signer()
    {
        return $this->belongsTo(User::class, 'signed_by', 'iduser');
    }

    public function isSigned()
    {
        return !empty($this->signature_token);
    }

    protected static function booted()
    {
        static::saving(function ($model) {
            $model->total_potongan = 
                ($model->potongan_shr ?? 0) +
                ($model->potongan_hutang_bl_ll ?? 0) +
                ($model->potongan_pakan_a ?? 0) +
                ($model->potongan_pakan_b ?? 0) +
                ($model->potongan_vitamix ?? 0) +
                ($model->potongan_konsentrat ?? 0) +
                ($model->potongan_skim ?? 0) +
                ($model->potongan_ib_keswan ?? 0) +
                ($model->potongan_susu_a ?? 0) +
                ($model->potongan_kas_bon ?? 0) +
                ($model->potongan_pakan_b_2 ?? 0) +
                ($model->potongan_sp ?? 0) +
                ($model->potongan_karpet ?? 0) +
                ($model->potongan_vaksin ?? 0) +
                ($model->potongan_lain_lain ?? 0);
            
            $model->sisa_pembayaran = $model->total_pembayaran - $model->total_potongan;
        });
    }
}
