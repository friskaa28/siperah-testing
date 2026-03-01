<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiError extends Model
{
    protected $table = 'kpi_errors';

    protected $fillable = [
        'error_type',
        'description',
        'reported_by',
        'period',
        'resolved',
        'resolved_at',
    ];

    protected $casts = [
        'resolved'    => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reported_by', 'iduser');
    }

    /**
     * Label for error_type
     */
    public function getErrorTypeLabelAttribute(): string
    {
        return match ($this->error_type) {
            'salary_calc' => 'Perhitungan Gaji',
            'data_entry'  => 'Pencatatan Data',
            default       => 'Lainnya',
        };
    }
}
