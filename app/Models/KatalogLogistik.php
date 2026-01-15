<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KatalogLogistik extends Model
{
    protected $table = 'katalog_logistik';

    protected $fillable = [
        'nama_barang',
        'harga_satuan',
    ];

    public function kasbon()
    {
        return $this->hasMany(Kasbon::class, 'idlogistik');
    }
}
