<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kasbon extends Model
{
    protected $table = 'kasbon';

    protected $fillable = [
        'idpeternak',
        'idlogistik',
        'nama_item',
        'qty',
        'harga_satuan',
        'total_rupiah',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function peternak()
    {
        return $this->belongsTo(Peternak::class, 'idpeternak', 'idpeternak');
    }

    public function logistik()
    {
        return $this->belongsTo(KatalogLogistik::class, 'idlogistik');
    }
}
