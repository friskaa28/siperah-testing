<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class PusatReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'TANGGAL',
            'POS / KATEGORI',
            'PAGI (L)',
            'SORE (L)',
            'TOTAL (L)'
        ];
    }

    public function map($row): array
    {
        return [
            Carbon::parse($row->tanggal)->format('d/m/Y'),
            strtoupper($row->pos),
            number_format($row->pagi, 1, ',', '.'),
            number_format($row->sore, 1, ',', '.'),
            number_format($row->total, 1, ',', '.')
        ];
    }
}
