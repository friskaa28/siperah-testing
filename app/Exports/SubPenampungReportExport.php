<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class SubPenampungReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
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
            'ID MITRA',
            'NAMA',
            'KATEGORI',
            'PAGI (L)',
            'SORE (L)',
            'TOTAL (L)'
        ];
    }

    public function map($row): array
    {
        $kategori = [
            'peternak' => 'Peternak',
            'sub_penampung_tr' => 'Sub-TR',
            'sub_penampung_p' => 'Sub-P',
            'sub_penampung' => 'Sub-Penampung'
        ];

        return [
            Carbon::parse($row->tanggal)->format('d/m/Y'),
            $row->peternak->no_peternak,
            $row->peternak->nama_peternak,
            $kategori[$row->peternak->status_mitra] ?? ucfirst($row->peternak->status_mitra),
            number_format($row->pagi, 1, ',', '.'),
            number_format($row->sore, 1, ',', '.'),
            number_format($row->total, 1, ',', '.')
        ];
    }
}
