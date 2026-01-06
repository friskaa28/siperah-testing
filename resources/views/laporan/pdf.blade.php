<!DOCTYPE html>
<html>
<head>
    <title>Laporan Pendapatan</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        h1 { margin: 0; font-size: 24px; }
        p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .total-row { font-weight: bold; background-color: #eee; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()">Cetak</button>
        <button onclick="window.close()">Tutup</button>
    </div>

    <div class="header">
        <h1>SIPERAH - Laporan Pendapatan</h1>
        <p>Peternak: {{ $peternak->nama_peternak }}</p>
        <p>Periode: 
            @if($filter == 'harian')
                {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}
            @elseif($filter == 'bulanan')
                {{ \Carbon\Carbon::parse($date)->translatedFormat('F Y') }}
            @elseif($filter == 'tahunan')
                {{ \Carbon\Carbon::parse($date)->translatedFormat('Y') }}
            @endif
        </p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Liter Susu</th>
                <th>Pendapatan (Rp)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporan as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td>{{ number_format($item->produksi->jumlah_susu_liter, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($item->hasil_pemilik, 0, ',', '.') }}</td>
                <td>{{ ucfirst($item->status) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2">TOTAL</td>
                <td>{{ number_format($totalLiter, 2, ',', '.') }}</td>
                <td class="text-right">{{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 50px; text-align: right;">
        <p>Dicetak pada: {{ now()->translatedFormat('d F Y H:i') }}</p>
    </div>

</body>
</html>
