<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ProduksiMatrixTemplateExport implements FromArray, WithHeadings, WithTitle
{
    public function headings(): array
    {
        $headers = ['No', 'Nama Peternak'];
        for ($i = 1; $i <= 31; $i++) {
            $headers[] = $i; // Pagi
            // Note: In manual excel, users merge headers. 
            // But for import logic, we need unique keys if possible, or we rely on index.
            // Let's output "1", "1" (for sore) and hope Maatwebsite handles it as 1, 1_1?
            // Actually, let's make it clear for the user AND the parser.
            // If we output "1", "1" visible header, Maatwebsite might rename.
            // Let's Output headers that match the View the user liked:
            // Row 1: 1, 1, 2, 2...
            // Row 2: P, S, P, S...
            // But Maatwebsite WithHeadingRow uses strict keys.
            // Let's stick to a single header row for simplicity if possible, OR
            // Just use 1_pagi, 1_sore for clarity?
            // "1 (Pagi)", "1 (Sore)"
            $headers[] = $i . "_sore"; // Placeholder?
        }
        return [];
    }
    
    // We need custom header construction for the "Handwritten" look.
    // Row 1: Headers (1, 2, 3...)
    // Row 2: Sub-headers (P, S, P, S...)
    
    public function array(): array
    {
        $h1 = ['No', 'Nama Peternak'];
        $h2 = ['', ''];
        
        for ($i = 1; $i <= 31; $i++) {
            $h1[] = $i;
            $h1[] = ''; // Merged usually
            
            $h2[] = 'P';
            $h2[] = 'S';
        }
        
        return [
            $h1,
            $h2,
            ['1', 'Contoh Nama', '5', '10', '6', '12'], // Example Data
        ];
    }

    public function title(): string
    {
        return 'Format Matriks (Manual)';
    }
}
