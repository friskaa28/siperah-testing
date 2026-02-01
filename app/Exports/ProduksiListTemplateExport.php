<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProduksiListTemplateExport implements FromArray, WithHeadings, WithTitle
{
    public function headings(): array
    {
        return [
            'tanggal',
            'waktu',
            'nama_peternak',
            'liter',
        ];
    }

    public function array(): array
    {
        return [
            [date('Y-m-d'), 'Pagi', 'Contoh Nama', '15.5'],
            [date('Y-m-d'), 'Sore', 'Contoh Nama', '10.0'],
        ];
    }

    public function title(): string
    {
        return 'Format List (Standar)';
    }
}
