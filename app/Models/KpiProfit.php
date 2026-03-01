<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KpiProfit extends Model
{
    protected $table = 'kpi_profits';

    protected $fillable = [
        'period',
        'revenue_before',
        'revenue_after',
        'cost_before',
        'cost_after',
        'milk_volume_before',
        'milk_volume_after',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'revenue_before'     => 'decimal:2',
        'revenue_after'      => 'decimal:2',
        'cost_before'        => 'decimal:2',
        'cost_after'         => 'decimal:2',
        'milk_volume_before' => 'decimal:2',
        'milk_volume_after'  => 'decimal:2',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by', 'iduser');
    }

    public function getProfitBeforeAttribute(): float
    {
        return (float)($this->revenue_before ?? 0) - (float)($this->cost_before ?? 0);
    }

    public function getProfitAfterAttribute(): float
    {
        return (float)($this->revenue_after ?? 0) - (float)($this->cost_after ?? 0);
    }
}
