<?php

namespace App\Imports;

use App\Models\ProduksiHarian;
use App\Models\Peternak;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProduksiImport implements ToCollection, WithHeadingRow
{
    public $imported = 0;
    public $failedNames = [];
    public $unrecognizedDates = [];
    public $invalidWaktu = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $peternakID = trim($row['no_mitra'] ?? '');
            $namaMitra = trim($row['nama_peternak'] ?? '');
            $tanggalLabel = trim($row['tanggal_yyyy_mm_dd'] ?? '');
            $waktuSetor = strtolower(trim($row['waktu_setor_pagi_sore'] ?? 'pagi'));
            $liter = (float)str_replace(',', '.', trim($row['liter'] ?? 0));

            if (empty($peternakID) && empty($namaMitra)) continue;
            if (empty($tanggalLabel)) continue;
            if ($liter <= 0) continue;

            // Validate waktu_setor
            if (!in_array($waktuSetor, ['pagi', 'sore'])) {
                $this->invalidWaktu[] = $peternakID ?: $namaMitra;
                $waktuSetor = 'pagi'; // Default to pagi
            }

            $peternak = Peternak::where('no_peternak', $peternakID)
                ->orWhere(DB::raw('LOWER(nama_peternak)'), strtolower($namaMitra))
                ->first();

            if ($peternak) {
                try {
                    // Handle Excel numeric date or string date
                    if (is_numeric($tanggalLabel)) {
                        $dt = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tanggalLabel));
                    } else {
                        $dt = Carbon::parse($tanggalLabel);
                    }

                    ProduksiHarian::updateOrCreate(
                        [
                            'idpeternak' => $peternak->idpeternak, 
                            'tanggal' => $dt->format('Y-m-d'), 
                            'waktu_setor' => $waktuSetor
                        ],
                        [
                            'jumlah_susu_liter' => $liter, 
                            'biaya_pakan' => 0, 
                            'biaya_tenaga' => 0, 
                            'biaya_operasional' => 0
                        ]
                    );

                    $this->imported++;
                } catch (\Exception $e) {
                    $this->unrecognizedDates[] = $tanggalLabel;
                }
            } else {
                $this->failedNames[] = $peternakID ?: $namaMitra;
            }
        }
    }
}
