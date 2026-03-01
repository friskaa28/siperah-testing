<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>E-Statement - {{ $peternak->nama_peternak }}</title>
    <style>
        @page { margin: 1cm; }
        body { font-family: 'Helvetica', sans-serif; color: #334155; line-height: 1.5; font-size: 10pt; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #0F172A; padding-bottom: 15px; }
        .brand { font-size: 24pt; font-weight: bold; color: #0F172A; margin: 0; }
        .brand span { color: #2180D3; }
        .document-title { font-size: 14pt; font-weight: bold; text-transform: uppercase; margin-top: 5px; color: #64748b; }
        
        .info-section { width: 100%; margin-bottom: 30px; }
        .info-table { width: 100%; border-collapse: collapse; }
        .info-table td { vertical-align: top; width: 50%; }
        
        .peternak-info h2 { margin: 0; font-size: 12pt; color: #0F172A; }
        .peternak-info p { margin: 2px 0; color: #64748b; }
        
        .statement-info { text-align: right; }
        .statement-info p { margin: 2px 0; font-weight: bold; }
        .statement-info span { font-weight: normal; color: #64748b; }

        .summary-box { background: #f8fafc; border-radius: 8px; padding: 15px; margin-bottom: 30px; border: 1px solid #e2e8f0; }
        .summary-title { font-weight: bold; text-transform: uppercase; font-size: 9pt; color: #475569; margin-bottom: 10px; border-bottom: 1px solid #e2e8f0; padding-bottom: 5px; }
        .summary-table { width: 100%; border-collapse: collapse; }
        .summary-table td { padding: 5px 0; }
        .summary-value { font-size: 14pt; font-weight: bold; color: #0F172A; }
        .summary-label { color: #64748b; font-size: 8pt; text-transform: uppercase; }

        .section-title { font-weight: bold; text-transform: uppercase; font-size: 10pt; color: #0F172A; margin: 20px 0 10px; border-left: 4px solid #2180D3; padding-left: 10px; }
        
        .transaction-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .transaction-table th { background: #f1f5f9; color: #0F172A; text-align: left; padding: 8px 10px; font-size: 8pt; text-transform: uppercase; border-bottom: 2px solid #e2e8f0; }
        .transaction-table td { padding: 6px 10px; border-bottom: 1px solid #e2e8f0; font-size: 9pt; }
        
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .text-danger { color: #ef4444; }
        .text-success { color: #16a34a; font-weight: bold; }
        
        .signature-section { width: 100%; margin-top: 40px; }
        .signature-box { width: 250px; float: right; text-align: center; }
        .signature-placeholder { height: 60px; margin: 10px 0; }
        .signature-font { font-family: 'Courier', monospace; font-style: italic; font-weight: bold; font-size: 12pt; color: #2180D3; }
        
        /* Stylized Signature Styles */
        .auto-signature {
            font-family: 'Times New Roman', 'Georgia', serif;
            font-size: 20pt;
            font-weight: bold;
            font-style: italic;
            color: #1e40af;
            letter-spacing: 1px;
            margin: 15px 0;
            display: inline-block;
        }
        .signature-underline {
            width: 180px;
            height: 1px;
            background: #334155;
            margin: 5px auto;
        }
        
        .footer { position: fixed; bottom: 0; width: 100%; font-size: 8pt; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%;">
            <tr>
                <td>
                    <h1 class="brand">SIPE<span>RAH</span></h1>
                    <div class="document-title">E-Statement & Rekening Koran</div>
                </td>
                <td style="text-align: right; vertical-align: middle;">
                    @if($qrBase64)
                        <img src="{{ $qrBase64 }}" alt="QR Verification" style="width: 70px; height: 70px;">
                    @else
                        <div style="width: 70px; height: 70px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; font-size: 6pt;">QR N/A</div>
                    @endif
                    <div style="font-size: 7pt; color: #64748b; margin-top: 5px;">ID: {{ $peternakId }}<br>Scan untuk verifikasi digital</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="info-section">
        <table class="info-table">
            <tr>
                <td>
                    <div class="peternak-info">
                        <h2>{{ $peternak->nama_peternak }}</h2>
                        <p>No. Mitra: {{ $peternakId }}</p>
                        <p>Alamat: {{ $peternak->lokasi }}</p>
                        <p>Status: {{ ucfirst($peternak->status_mitra) }}</p>
                    </div>
                </td>
                <td>
                    <div class="statement-info">
                        <p>Tanggal Cetak: <span>{{ now()->format('d M Y') }}</span></p>
                        <p>Periode: <span>{{ $startDate->format('d/m/Y') }} - {{ $endDate->format('d/m/Y') }}</span></p>
                        <p>Sistem: <span style="color: #2180D3;">SIPERAH DIGITAL</span></p>
                    </div>
                </td>
            </tr>
        </table>
    </div>


    <div class="section-title">Detail Setoran Harian</div>
    <table class="transaction-table">
        <thead>
            <tr>
                <th style="width: 25%;">Tanggal</th>
                <th style="width: 25%;">Waktu</th>
                <th class="text-right" style="width: 50%;">Jumlah (Liter)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produksi as $p)
            <tr>
                <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                <td>{{ ucfirst($p->waktu_setor) }}</td>
                <td class="text-right fw-bold">{{ number_format($p->jumlah_susu_liter, 1, ',', '.') }} L</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Detail Potongan Kasbon</div>
    <table class="transaction-table">
        <thead>
            <tr>
                <th style="width: 20%;">Tanggal</th>
                <th style="width: 40%;">Item / Barang</th>
                <th style="width: 15%;">Qty</th>
                <th class="text-right" style="width: 25%;">Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($kasbonHistory as $k)
            <tr>
                <td>{{ $k->tanggal->format('d/m/Y') }}</td>
                <td>{{ $k->nama_item }}</td>
                <td>{{ $k->qty }}</td>
                <td class="text-right text-danger">-{{ number_format($k->total_rupiah, 0, ',', '.') }}</td>
            </tr>
            @endforeach
            @if($kasbonHistory->isEmpty())
                <tr><td colspan="4" class="text-center italic text-muted">Tidak ada potongan kasbon periode ini.</td></tr>
            @endif
        </tbody>
    </table>

    <!-- Summary Section -->
    <div style="margin-top: 30px; padding: 15px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; padding: 8px; border-right: 1px solid #e2e8f0;">
                    <p style="margin: 0; font-size: 8pt; color: #64748b; text-transform: uppercase;">Total Setoran Susu</p>
                    <p style="margin: 5px 0 0 0; font-size: 16pt; font-weight: bold; color: #0F172A;">{{ number_format($totalLiter, 1, ',', '.') }} L</p>
                    <p style="margin: 3px 0 0 0; font-size: 7pt; color: #64748b;">@ Rp {{ number_format($currentPrice, 0, ',', '.') }} = Rp {{ number_format($totalGross, 0, ',', '.') }}</p>
                </td>
                <td style="width: 50%; padding: 8px;">
                    <p style="margin: 0; font-size: 8pt; color: #64748b; text-transform: uppercase;">Total Kasbon/Pakan</p>
                    <p style="margin: 5px 0 0 0; font-size: 16pt; font-weight: bold; color: #ef4444;">Rp {{ number_format($totalKasbon, 0, ',', '.') }}</p>
                    <p style="margin: 3px 0 0 0; font-size: 7pt; color: #64748b;">{{ $kasbonHistory->count() }} transaksi</p>
                </td>
            </tr>
        </table>
        <div style="margin-top: 15px; padding-top: 15px; border-top: 2px solid #e2e8f0; text-align: right;">
            <span style="font-size: 9pt; color: #64748b; font-weight: bold;">ESTIMASI PEMBAYARAN BERSIH: </span>
            <span style="font-size: 18pt; color: #16a34a; font-weight: bold;">Rp {{ number_format($netSalary, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="signature-section">
        <div class="signature-box">
            <p style="margin-bottom: 5px;">Jawa Timur, {{ now()->format('d M Y') }}</p>
            <p style="font-weight: bold; margin-bottom: 20px;">Pengelola SIPERAH,</p>
            <div style="text-align: center; margin: 10px 0; min-height: 60px;">
                <!-- Stylized Auto Signature -->
                <div class="auto-signature">
                    Admin SIPERAH
                </div>
                <div class="signature-underline"></div>
            </div>
            <div style="margin-top: 5px; border-top: 1px solid #334155; padding-top: 5px; font-weight: bold;">
                ( ____________________ )
            </div>
            <p style="font-size: 7pt; color: #64748b; margin-top: 10px;">Generated from SIPERAH Digital Ecosystem</p>
        </div>
    </div>

    <div class="footer">
        Dicetak otomatis pada {{ now()->format('d/m/Y H:i') }}. Dokumen ini telah terverifikasi secara digital melalui sistem SIPERAH.
    </div>
</body>
</html>
