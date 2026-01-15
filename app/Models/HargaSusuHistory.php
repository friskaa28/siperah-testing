<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HargaSusuHistory extends Model
{
    protected $table = 'harga_susu_history';

    protected $fillable = [
        'harga',
        'tanggal_berlaku',
    ];

    protected $casts = [
        'tanggal_berlaku' => 'date',
    ];

    public static function getHargaAktif($tanggal = null)
    {
        $tanggal = $tanggal ?: now();
        return self::where('tanggal_berlaku', '<=', $tanggal)
            ->orderBy('tanggal_berlaku', 'desc')
            ->first()?->harga ?? 0;
    }
}
