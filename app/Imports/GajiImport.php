<?php

namespace App\Imports;

use App\Models\SlipPembayaran;
use App\Models\Peternak;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class GajiImport implements ToCollection, WithHeadingRow
{
    public $data = [];
    public $failedNames = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $rowArray = $row->toArray();
            
            // Robust Name Detection
            $namaMitra = trim($row['nama_mitra_peternak'] ?? $row['nama_peternak'] ?? $row['nama'] ?? '');
            if (empty($namaMitra)) {
                // Try fuzzy matching for name
                foreach ($rowArray as $k => $v) {
                    if (str_contains($k, 'nama') && str_contains($k, 'mitra')) { $namaMitra = trim($v); break; }
                }
            }

            // Robust Category Detection
            $kategoriRaw = strtolower(trim($row['kategori_peternak_sub_penampung_tr_sub_penampung_p'] ?? $row['kategori'] ?? ''));
            if (empty($kategoriRaw)) {
                foreach ($rowArray as $k => $v) {
                    if (str_contains($k, 'kategori')) { $kategoriRaw = strtolower(trim($v)); break; }
                }
            }

            $peternak = Peternak::where(DB::raw('LOWER(nama_peternak)'), strtolower($namaMitra))->first();
            
            // Default status: use DB if exists, otherwise default to peternak
            $statusMitra = $peternak ? $peternak->status_mitra : 'peternak';
            
            // Override with Excel if possible (more specific matching)
            if (!empty($kategoriRaw)) {
                if (str_contains($kategoriRaw, 'tr')) {
                    $statusMitra = 'sub_penampung_tr';
                } elseif (str_contains($kategoriRaw, 'sub') && (str_contains($kategoriRaw, ' p') || str_ends_with($kategoriRaw, ' p') || str_ends_with($kategoriRaw, '_p'))) {
                    $statusMitra = 'sub_penampung_p';
                } elseif (str_contains($kategoriRaw, 'sub')) {
                    $statusMitra = 'sub_penampung';
                } elseif (str_contains($kategoriRaw, 'peternak')) {
                    $statusMitra = 'peternak';
                }
            }

            $bulan = (int)($row['bulan_1_12'] ?? $row['bulan'] ?? 0);
            $tahun = (int)($row['tahun'] ?? 0);
            
            // Robust Liter Detection
            $liter = (float)str_replace(',', '.', trim($row['total_liter'] ?? $row['liter'] ?? 0));
            if ($liter == 0) {
                 foreach ($rowArray as $k => $v) {
                    if (str_contains($k, 'liter')) { $liter = (float)str_replace(',', '.', trim($v)); break; }
                }
            }

            // Robust Harga Detection
            $harga = (float)str_replace(',', '.', trim($row['harga_satuan'] ?? $row['harga'] ?? 0));
             if ($harga == 0) {
                 foreach ($rowArray as $k => $v) {
                    if (str_contains($k, 'harga')) { $harga = (float)str_replace(',', '.', trim($v)); break; }
                }
            }

            $tanggalLabel = 'tanggal_setor_slip_ddmmyyyy'; 
            $tanggalInput = trim($row[$tanggalLabel] ?? $row['tanggal_slip_ddmmyyyy'] ?? $row['tanggal'] ?? '');

            if (empty($namaMitra) || ($liter <= 0 && $harga <= 0)) {
                \Illuminate\Support\Facades\Log::warning('Skipping GajiImport row due to missing data:', ['name' => $namaMitra, 'liter' => $liter, 'harga' => $harga]);
                continue;
            }

            $this->data[] = [
                'nama_peternak' => $namaMitra,
                'status_mitra' => $statusMitra,
                'idpeternak' => $peternak ? $peternak->idpeternak : null,
                'no_peternak' => $peternak ? $peternak->no_peternak : null,
                'bulan' => $bulan,
                'tahun' => $tahun,
                'jumlah_susu' => $liter,
                'harga_satuan' => $harga,
                'total_pembayaran' => $liter * $harga,
                'tanggal_input' => $tanggalInput,
                'is_new' => $peternak ? false : true,
                'exists' => true, // We allow "import" even if not exists, as it will auto-create
                'potongan' => [
                    'potongan_shr' => (float)($row['1_shr'] ?? 0),
                    'potongan_hutang_bl_ll' => (float)($row['2_hut_bl_ll'] ?? 0),
                    'potongan_pakan_a' => (float)($row['3_pakan_a'] ?? 0),
                    'potongan_pakan_b' => (float)($row['4_pakan_b'] ?? 0),
                    'potongan_vitamix' => (float)($row['5_vitamix'] ?? 0),
                    'potongan_konsentrat' => (float)($row['6_konsentrat'] ?? 0),
                    'potongan_skim' => (float)($row['7_skim'] ?? 0),
                    'potongan_ib_keswan' => (float)($row['8_ib_keswan'] ?? $row['8_ibkeswan'] ?? 0),
                    'potongan_susu_a' => (float)($row['9_susu_a'] ?? 0),
                    'potongan_kas_bon' => (float)($row['10_kas_bon'] ?? 0),
                    'potongan_pakan_b_2' => (float)($row['11_pakan_b_2'] ?? 0),
                    'potongan_sp' => (float)($row['12_sp'] ?? 0),
                    'potongan_karpet' => (float)($row['13_karpet'] ?? 0),
                    'potongan_vaksin' => (float)($row['14_vaksin'] ?? 0),
                    'potongan_lain_lain' => (float)($row['15_lain_lain'] ?? 0),
                ]
            ];

            if (!$peternak) {
                $this->failedNames[] = $namaMitra;
            }
        }
    }
}
