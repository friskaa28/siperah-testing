<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Carbon\Carbon;

class HarianReportExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $peternaks;
    protected $produksis;
    protected $tanggal;

    public function __construct($peternaks, $produksis, $tanggal)
    {
        $this->peternaks = $peternaks;
        $this->produksis = $produksis;
        $this->tanggal = $tanggal;
    }

    public function collection()
    {
        return $this->peternaks;
    }

    public function headings(): array
    {
        return [
            'TANGGAL',
            'ID MITRA',
            'NAMA',
            'PAGI (L)',
            'KATEGORI',
            'SORE (L)',
            'SETOR',
            'TOTAL (L)'
        ];
    }

    public function map($row): array
    {
        $prodGroup = $this->produksis->get($row->idpeternak, collect());
        $pagi = $prodGroup->where('waktu_setor', 'pagi')->sum('jumlah_susu_liter');
        $sore = $prodGroup->where('waktu_setor', 'sore')->sum('jumlah_susu_liter');
        $total = $pagi + $sore;

        $kategori = [
            'peternak' => 'Peternak',
            'sub_penampung_tr' => 'Sub-TR',
            'sub_penampung_p' => 'Sub-P',
            'sub_penampung' => 'Sub-Penampung'
        ];

        return [
            Carbon::parse($this->tanggal)->format('d/m/Y'),
            $row->no_peternak,
            $row->nama_peternak,
            number_format($pagi, 1, ',', '.'),
            $kategori[$row->status_mitra] ?? ucfirst($row->status_mitra),
            number_format($sore, 1, ',', '.'),
            $prodGroup->isNotEmpty() ? 'Sudah' : 'Belum',
            number_format($total, 1, ',', '.')
        ];
    }
}
