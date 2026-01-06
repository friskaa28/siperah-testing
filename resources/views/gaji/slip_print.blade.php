<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Slip Pembayaran - {{ $slip->peternak->nama_peternak }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .container { width: 18cm; margin: 0 auto; border: 1px solid #ccc; padding: 20px; }
        .header { display: flex; align-items: center; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .logo { width: 80px; height: 60px; object-fit: cover; margin-right: 15px; border: 1px solid #eee; }
        .header-text h1 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header-text p { margin: 2px 0; color: #666; }
        
        .title { text-align: center; font-size: 16px; font-weight: bold; text-decoration: underline; margin-bottom: 20px; }
        
        .info-grid { display: grid; grid-template-columns: 120px 10px auto; gap: 5px; margin-bottom: 20px; }
        
        .section-title { font-weight: bold; margin-bottom: 10px; text-transform: uppercase; border-bottom: 1px solid #eee; }
        
        .table-split { display: flex; gap: 40px; }
        .table-split > div { flex: 1; }
        
        .deduction-item { display: flex; justify-content: space-between; margin-bottom: 4px; border-bottom: 1px dotted #ccc; }
        
        .totals { margin-top: 20px; border-top: 2px solid #000; padding-top: 10px; }
        .total-row { display: flex; justify-content: space-between; font-weight: bold; font-size: 14px; margin-top: 5px; }

        .footer { margin-top: 40px; display: flex; justify-content: space-between; text-align: center; }
        .signature-box { width: 200px; }
        .signature-line { margin-top: 60px; border-top: 1px solid #000; }

        @media print {
            .container { border: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2180D3; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Print Sekarang</button>
    </div>

    <div class="container">
        <div class="header">
            <img src="{{ asset('img/logo-siperah.png') }}" class="logo" alt="Logo">
            <div class="header-text">
                <h1>Peternak Margo Mulyo Abadi</h1>
                <p>Jl. Raya Kradinan Tulungagung</p>
            </div>
        </div>

        <div class="title">SLIP PEMBAYARAN SUSU</div>

        <div class="info-grid">
            <div>NAMA</div><div>:</div><div>{{ strtoupper($slip->peternak->nama_peternak) }}</div>
            <div>NO.</div><div>:</div><div>{{ $slip->peternak->no_peternak ?? '-' }}</div>
            <div>KELOMPOK</div><div>:</div><div>{{ strtoupper($slip->peternak->kelompok ?? '-') }}</div>
            <div>PERIODE / BULAN</div><div>:</div><div>{{ strtoupper(date('F Y', mktime(0, 0, 0, $slip->bulan, 1, $slip->tahun))) }}</div>
            <div>TS</div><div>:</div><div>-</div>
            <div>JUMLAH SUSU (Liter)</div><div>:</div><div style="text-align: right; width: 100px;">{{ number_format($slip->jumlah_susu, 2) }}</div>
            <div>Harga @ LITER</div><div>:</div><div style="text-align: right; width: 100px;">Rp {{ number_format($slip->harga_satuan, 0, ',', '.') }}</div>
            <div style="font-weight: bold;">JUMLAH PEMBAYARAN</div><div>:</div><div style="text-align: right; width: 100px; border-bottom: 1px solid #000; font-weight: bold;">Rp {{ number_format($slip->total_pembayaran, 0, ',', '.') }}</div>
        </div>

        <div class="section-title">Potongan</div>
        
        <div class="table-split">
            <div>
                @php
                    $leftPot = [
                        'potongan_shr' => '1 SHR',
                        'potongan_hutang_bl_ll' => '2 HUT. BL LL',
                        'potongan_pakan_a' => '3 PAKAN A',
                        'potongan_pakan_b' => '4 PAKAN B',
                        'potongan_vitamix' => '5 VITAMIX',
                        'potongan_konsentrat' => '6 KONSENTRAT',
                        'potongan_skim' => '7 SKIM',
                        'potongan_ib_keswan' => '8 IB/KESWAN',
                    ];
                @endphp
                @foreach($leftPot as $key => $label)
                <div class="deduction-item">
                    <span>{{ $label }}</span>
                    <span>Rp {{ number_format($slip->$key, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            <div>
                @php
                    $rightPot = [
                        'potongan_susu_a' => '9 SUSU A',
                        'potongan_kas_bon' => '10 KAS BON',
                        'potongan_pakan_b_2' => '11 PAKAN B (2)',
                        'potongan_sp' => '12 SP',
                        'potongan_karpet' => '13 KARPET',
                        'potongan_vaksin' => '14 VAKSIN',
                        'potongan_lain_lain' => '15 LAIN-LAIN',
                    ];
                @endphp
                @foreach($rightPot as $key => $label)
                <div class="deduction-item">
                    <span>{{ $label }}</span>
                    <span>Rp {{ number_format($slip->$key, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="totals">
            <div class="total-row">
                <span>TOTAL POTONGAN</span>
                <span>: Rp {{ number_format($slip->total_potongan, 0, ',', '.') }}</span>
            </div>
            <div class="total-row" style="font-size: 16px;">
                <span>SISA PEMBAYARAN</span>
                <span>: Rp {{ number_format($slip->sisa_pembayaran, 0, ',', '.') }}</span>
            </div>
        </div>

        <div style="text-align: right; margin-top: 20px; font-style: italic;">
            Dibayarkan Tgl: {{ $slip->tanggal_bayar ? $slip->tanggal_bayar->format('d/m/Y') : '' }}
        </div>

        <div class="footer">
            <div class="signature-box">
                <div>Diserahkan Oleh,</div>
                <div class="signature-line">Admin Kantor</div>
            </div>
            <div class="signature-box">
                <div>Penerima,</div>
                <div class="signature-line">{{ $slip->peternak->nama_peternak }}</div>
            </div>
        </div>
    </div>
</body>
</html>
