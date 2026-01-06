<!DOCTYPE html>
<html>
<head>
    <title>Rekap Distribusi Susu</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .summary { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #f0f0f0; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .footer { margin-top: 30px; font-size: 9px; text-align: right; color: #777; }
    </style>
</head>
<body>
    <div class="header">
        <h2 style="margin: 0; text-transform: uppercase;">Rekap Manajemen Distribusi Susu</h2>
        <p style="margin: 5px 0;">Periode: {{ $namaBulan }} {{ $tahun }}</p>
    </div>

    <div class="summary">
        <table style="border: none;">
            <tr style="border: none;">
                <td style="border: none; width: 50%;"><strong>Total Volume:</strong> {{ number_format($totalVolume, 1) }} Liter</td>
                <td style="border: none; width: 50%; text-align: right;"><strong>Tanggal Cetak:</strong> {{ date('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Tanggal</th>
                <th width="25%">Peternak</th>
                <th width="25%">Tujuan / Buyer</th>
                <th width="15%" class="text-center">Volume (L)</th>
                <th width="15%" class="text-center">Harga (Rp)</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($distribusi as $d)
            <tr>
                <td>{{ $d->tanggal_kirim->format('d/m/Y') }}</td>
                <td>{{ $d->peternak->nama_peternak }}</td>
                <td>{{ $d->tujuan }}</td>
                <td class="text-center">{{ number_format($d->volume, 1) }}</td>
                <td class="text-right">{{ number_format($d->harga_per_liter, 0) }}</td>
                <td>{{ strtoupper($d->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Sistem Informasi Peternakan & Hasil (SIPERAH)</p>
    </div>
</body>
</html>
