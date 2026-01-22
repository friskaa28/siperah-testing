<?php

namespace App\Exports;

use App\Models\Peternak;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GajiTemplateExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return Peternak::all();
    }

    public function headings(): array
    {
        return [
            'Nama Mitra/Peternak',
            'Kategori (Peternak/Sub Penampung TR/Sub Penampung P)',
            'Bulan (1-12)',
            'Tahun',
            'Tanggal Setor & Slip (DD/MM/YYYY)',
            'Total Liter',
            'Harga Satuan',
            '1. SHR',
            '2. HUT. BL LL',
            '3. PAKAN A',
            '4. PAKAN B',
            '5. VITAMIX',
            '6. KONSENTRAT',
            '7. SKIM',
            '8. IB/KESWAN',
            '9. SUSU A',
            '10. KAS BON',
            '11. PAKAN B (2)',
            '12. SP',
            '13. KARPET',
            '14. VAKSIN',
            '15. LAIN-LAIN'
        ];
    }

    public function map($row): array
    {
        $statusMap = [
            'peternak' => 'Peternak',
            'sub_penampung' => 'Sub Penampung',
            'sub_penampung_tr' => 'Sub Penampung TR',
            'sub_penampung_p' => 'Sub Penampung P',
        ];

        return [
            $row->nama_peternak,
            $statusMap[$row->status_mitra] ?? 'Peternak',
            now()->month,
            now()->year,
            now()->format('d/m/Y'),
            '0.00',
            '0',
            '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 11], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E0E7FF']]],
        ];
    }
}
