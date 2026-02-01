<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProduksiTemplateExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new ProduksiListTemplateExport(),
            new ProduksiMatrixTemplateExport(),
        ];
    }
}
