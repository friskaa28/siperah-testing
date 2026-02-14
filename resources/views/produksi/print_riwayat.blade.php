<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Riwayat Setor Susu</title>
    <style>
        @page { size: A4 portrait; margin: 0.8cm; }
        body { font-family: Arial, sans-serif; font-size: 9pt; color: #000; -webkit-print-color-adjust: exact; }
        .header { display: flex; align-items: center; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 5px; }
        .header img { height: 60px; margin-right: 15px; }
        .header-text { text-align: left; }
        .header-text h1 { margin: 0; font-size: 14pt; text-transform: uppercase; font-weight: bold; }
        .header-text p { margin: 2px 0 0; font-size: 9pt; }
        .report-title { text-align: center; margin-bottom: 10px; }
        .report-title h2 { margin: 0; font-size: 12pt; text-transform: uppercase; }
        
        .section-title { margin-top: 10px; font-weight: bold; font-size: 10pt; margin-bottom: 5px; border-bottom: 1px solid #ccc; padding-bottom: 2px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; page-break-inside: auto; }
        tr { page-break-inside: avoid; page-break-after: auto; }
        th, td { border: 1px solid #000; padding: 3px 5px; font-size: 8pt; }
        th { background-color: #fff; text-align: center; font-weight: bold; }
        td.num { text-align: right; }
        td.center { text-align: center; }
        
        .footer-total { font-weight: bold; background-color: #f9f9f9; }
        
        .peternak-info { margin-bottom: 8px; font-size: 9pt; }
        
        .no-print { display: none; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="header">
        <img src="{{ asset('img/logo-siperah.png') }}" alt="Logo">
        <div class="header-text">
            <h1>PETERNAK MARGO MULYO ABADI</h1>
            <p>Jl. Raya Kradinan Tulungagung</p>
        </div>
    </div>

    <div class="report-title">
        <h2>Laporan Riwayat Setoran Susu</h2>
        <p style="font-size: 8pt; font-weight: normal; margin-top: 5px;">
            Periode: 
            {{ $startDate ? \Carbon\Carbon::parse($startDate)->format('d/m/Y') : 'Awal' }} 
            s/d 
            {{ $endDate ? \Carbon\Carbon::parse($endDate)->format('d/m/Y') : 'Akhir' }}
        </p>
    </div>

    @forelse($groupedData as $peternakName => $months)
        <div style="page-break-after: always; break-after: page;">
            <div class="peternak-info">
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
                                <td class="num">{{ $row->pagi > 0 ? number_format($row->pagi, 2, ',', '.') : '-' }}</td>
                                <td class="num">{{ $row->sore > 0 ? number_format($row->sore, 2, ',', '.') : '-' }}</td>
                                <td class="num">{{ number_format($row->total, 2, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="footer-total">
                            <td class="center">TOTAL</td>
                            <td class="num">{{ number_format($totalPagi, 2, ',', '.') }}</td>
                            <td class="num">{{ number_format($totalSore, 2, ',', '.') }}</td>
                            <td class="num">{{ number_format($totalAll, 2, ',', '.') }}</td>
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
