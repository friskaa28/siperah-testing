<?php

namespace App\Exports;

use App\Models\Peternak;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProduksiTemplateExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return Peternak::all();
    }

    public function headings(): array
    {
        return [
            'No. Mitra',
            'Nama Peternak',
            'Tanggal (YYYY-MM-DD)',
            'Waktu Setor (pagi/sore)',
            'Liter'
        ];
    }

    public function map($row): array
    {
        return [
            $row->no_peternak ?: 'MTR-' . str_pad($row->idpeternak, 3, '0', STR_PAD_LEFT),
            $row->nama_peternak,
            now()->format('Y-m-d'),
            'pagi',
            '10.5'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E0E7FF']]],
        ];
    }
}
