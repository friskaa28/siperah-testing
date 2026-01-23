<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Setor Susu</title>
    <style>
        @page { size: A4 portrait; margin: 1cm; }
        body { font-family: Arial, sans-serif; font-size: 11pt; color: #000; -webkit-print-color-adjust: exact; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px double #000; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 18pt; text-transform: uppercase; }
        .header p { margin: 5px 0 0; font-size: 12pt; }
        
        .section-title { margin-top: 20px; font-weight: bold; font-size: 12pt; margin-bottom: 10px; border-bottom: 1px solid #ccc; padding-bottom: 2px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 15px; page-break-inside: auto; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        th, td { border: 1px solid #000; padding: 5px 8px; font-size: 10pt; }
        th { background-color: #f0f0f0; text-align: center; font-weight: bold; }
        td.num { text-align: right; }
        td.center { text-align: center; }
        
        .footer-total { font-weight: bold; background-color: #f9f9f9; }
        
        .no-print { display: none; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <h1>KOPERASI PRODUSEN SI PENGOLAHAN RAHAYU</h1>
        <p>Laporan Riwayat Setoran Susu</p>
        <p style="font-size: 10pt; font-weight: normal; margin-top: 5px;">
            Periode: 
            {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }} 
            s/d 
            {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Akhir' }}
        </p>
    </div>

    @forelse($groupedData as $peternakName => $months)
        <div style="page-break-after: always; break-after: page;">
            <div style="margin-bottom: 15px;">
                <strong>Nama Peternak:</strong> {{ $peternakName }}
            </div>

            @foreach($months as $monthName => $records)
                <div class="section-title">Bulan: {{ $monthName }}</div>
                <table>
                    <thead>
                        <tr>
                            <th width="20%">Tanggal</th>
                            <th width="20%">Pagi (Liter)</th>
                            <th width="20%">Sore (Liter)</th>
                            <th width="20%">Total (Liter)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalPagi = 0;
                            $totalSore = 0;
                            $totalAll = 0;
                        @endphp
                        @foreach($records as $row)
                            @php
                                $totalPagi += $row->pagi;
                                $totalSore += $row->sore;
                                $totalAll += $row->total;
                            @endphp
                            <tr>
                                <td class="center">{{ \Carbon\Carbon::parse($row->tanggal)->format('d/m/Y') }}</td>
                                <td class="num">{{ $row->pagi > 0 ? number_format($row->pagi, 1, ',', '.') : '-' }}</td>
                                <td class="num">{{ $row->sore > 0 ? number_format($row->sore, 1, ',', '.') : '-' }}</td>
                                <td class="num">{{ number_format($row->total, 1, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="footer-total">
                            <td class="center">TOTAL</td>
                            <td class="num">{{ number_format($totalPagi, 1, ',', '.') }}</td>
                            <td class="num">{{ number_format($totalSore, 1, ',', '.') }}</td>
                            <td class="num">{{ number_format($totalAll, 1, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            @endforeach
        </div>
    @empty
        <div style="text-align: center; padding: 50px;">
            <p>Tidak ada data produksi yang ditemukan untuk periode ini.</p>
        </div>
    @endforelse

</body>
</html>
