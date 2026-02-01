<?php

namespace App\Imports;

use App\Models\ProduksiHarian;
use App\Models\Peternak;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Carbon\Carbon;

class ProduksiImport implements ToCollection
{
    public $bulan;
    public $tahun;
    
    public $imported = 0;
    public $failedNames = [];
    public $unrecognizedDates = [];
    public $invalidWaktu = [];

    public function __construct($bulan = null, $tahun = null)
    {
        $this->bulan = $bulan;
        $this->tahun = $tahun;
    }

    public function collection(Collection $rows)
    {
        // Convert to array to easily access by index
        $rows = $rows->toArray(); 
        if (empty($rows)) return;

        // Detect Mode based on first few rows
        $headerRow = $rows[0] ?? [];
        $headerString = implode(' ', array_map('strtolower', array_filter($headerRow, 'is_string')));
        
        // List Mode usually has "Tanggal" or "Date" in first row
        $isListMode = str_contains($headerString, 'tanggal') || str_contains($headerString, 'date');
        
        if ($isListMode) {
            $this->processListMode($rows);
        } else {
            // Assume Matrix Mode if not List Mode, especially if month/year are provided
            if ($this->bulan && $this->tahun) {
                $this->processMatrixMode($rows);
            }
        }
    }

    private function processListMode(array $rows)
    {
        // 1. Identify Column Indices from Header Row (Index 0)
        $header = array_map('strtolower', array_map('trim', $rows[0]));
        
        $idxNama = $this->findHeaderIndex($header, ['nama_peternak', 'nama', 'peternak', 'mitra']);
        $idxTgl = $this->findHeaderIndex($header, ['tanggal', 'date', 'tgl']);
        $idxWaktu = $this->findHeaderIndex($header, ['waktu', 'sesi', 'jam']);
        $idxLiter = $this->findHeaderIndex($header, ['liter', 'jumlah', 'total', 'volume']);

        if ($idxNama === false || $idxTgl === false || $idxLiter === false) return; // Cannot proceed without mandatory cols

        // 2. Iterate Data (Start from Index 1)
        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];
            
            // Nama
            $nama = trim($row[$idxNama] ?? '');
            if (empty($nama)) continue;
            
            $peternak = $this->findPeternak($nama);
            if (!$peternak) {
                $this->failedNames[] = $nama;
                continue;
            }

            // Tanggal
            $valTgl = $row[$idxTgl] ?? null;
            $tanggal = $this->parseDate($valTgl);
            if (!$tanggal) continue;

            // Waktu
            $valWaktu = isset($idxWaktu) ? strtolower(trim($row[$idxWaktu] ?? '')) : 'pagi';
            if ($idxWaktu !== false && !in_array($valWaktu, ['pagi', 'sore'])) {
                $this->invalidWaktu[] = "$nama ($valWaktu)";
                continue;
            }
            if ($idxWaktu === false) $valWaktu = 'pagi'; // Fallback

            // Liter
            $liter = (float)($row[$idxLiter] ?? 0);
            if ($liter <= 0) continue;

            $this->saveProduksi($peternak, $tanggal, $valWaktu, $liter);
        }
    }

    private function processMatrixMode(array $rows)
    {
        // Matrix usually:
        // Row 0: No, Nama, 1,   , 2,   , 3... 
        // Row 1:   ,     , P, S, P, S...
        
        $rowDates = $rows[0];
        $rowSessions = $rows[1] ?? []; // Sub-header
        
        $colMap = []; // [colIndex => ['day' => 1, 'session' => 'pagi']]
        $currentDay = null;
        
        // Start scanning from column 2 (assuming Col 0=No, Col 1=Nama)
        for ($c = 0; $c < count($rowDates); $c++) {
            $valTop = $rowDates[$c] ?? null;
            $valSub = strtolower(trim($rowSessions[$c] ?? ''));
            
            // Validate Top Value (Day)
            if (is_numeric($valTop) && $valTop > 0 && $valTop <= 31) {
                $currentDay = (int)$valTop;
            }
            
            // Validate Session
            $session = null;
            if (in_array($valSub, ['p', 'pagi', 'pg', 'morning'])) $session = 'pagi';
            if (in_array($valSub, ['s', 'sore', 'sr', 'afternoon'])) $session = 'sore';
            
            if ($currentDay && $session) {
                $colMap[$c] = ['day' => $currentDay, 'session' => $session];
            }
        }
        
        // Iterate Peternak Rows (Start from Row 2)
        for ($r = 2; $r < count($rows); $r++) {
            $row = $rows[$r];
            
            // Find Name (Check Col 1 then Col 0)
            $nama = trim($row[1] ?? '');
            if (empty($nama) || is_numeric($nama)) $nama = trim($row[0] ?? ''); // Fallback
            
            if (empty($nama)) continue;

            $peternak = $this->findPeternak($nama);
            if (!$peternak) {
                $this->failedNames[] = $nama;
                continue;
            }

            foreach ($colMap as $colIdx => $meta) {
                $day = $meta['day'];
                $session = $meta['session'];
                
                $liter = (float)($row[$colIdx] ?? 0);
                
                if ($liter > 0) {
                    if (!checkdate($this->bulan, $day, $this->tahun)) continue;
                    $date = Carbon::createFromDate($this->tahun, $this->bulan, $day)->format('Y-m-d');
                    
                    $this->saveProduksi($peternak, $date, $session, $liter);
                }
            }
        }
    }

    private function findHeaderIndex($row, $keywords)
    {
        foreach ($row as $idx => $val) {
            if (in_array(strtolower($val), $keywords)) return $idx;
        }
        return false;
    }

    private function findPeternak($nama)
    {
        $peternak = Peternak::where('nama_peternak', $nama)->first();
        if (!$peternak) {
            $peternak = Peternak::whereRaw('LOWER(nama_peternak) = ?', [strtolower($nama)])->first();
        }
        return $peternak;
    }

    private function parseDate($val)
    {
        try {
            if (!$val) return null;
            if (is_numeric($val)) {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val)->format('Y-m-d');
            } else {
                return Carbon::parse($val)->format('Y-m-d');
            }
        } catch (\Exception $e) {
            $this->unrecognizedDates[] = $val;
            return null;
        }
    }

    private function saveProduksi($peternak, $tanggal, $waktu, $liter)
    {
        ProduksiHarian::updateOrCreate(
            [
                'idpeternak' => $peternak->idpeternak,
                'tanggal' => $tanggal,
                'waktu_setor' => $waktu
            ],
            [
                'jumlah_susu_liter' => $liter,
                'biaya_pakan' => 0,
                'biaya_operasional' => 0,
                'biaya_tenaga' => 0
            ]
        );
        $this->imported++;
    }
}
