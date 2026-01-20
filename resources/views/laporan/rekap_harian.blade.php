@extends('layouts.app')

@section('title', 'Rekap Harian Pengiriman Susu - SIPERAH')

@section('content')
<div class="row align-items-center mb-4 no-print">
    <div class="col-md-6">
        <h1 class="h3 fw-bold mb-0">📅 Rekap Harian</h1>
        <p class="text-muted mb-0">Laporan detail pengiriman susu harian</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <button onclick="window.print()" class="btn btn-outline-primary btn-sm px-3 shadow-sm">
            🖨️ Cetak Laporan
        </button>
    </div>
</div>

<!-- Responsive Filter Card -->
<div class="card shadow-sm border-0 mb-4 no-print" style="border-radius: 12px; background: #fff;">
    <div class="card-body p-4">
        <form action="{{ route('laporan.rekap_harian') }}" method="GET">
            <div class="row align-items-end">
                <div class="col-12 mb-3">
                    <label class="small fw-bold text-muted">Periode:</label>
                </div>
                <div class="col-12 col-md-5 mb-3">
                    <select name="bulan" class="form-select form-select-sm shadow-sm p-2" style="border-radius: 8px;">
                        @for($i=1; $i<=12; $i++)
                            <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-12 col-md-5 mb-3">
                    <select name="tahun" class="form-select form-select-sm shadow-sm p-2" style="border-radius: 8px;">
                        @for($i=now()->year; $i>=2024; $i--)
                            <option value="{{ $i }}" {{ $tahun == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-12 col-md-2 mb-3">
                    <button type="submit" class="btn btn-primary btn-sm w-100 fw-bold shadow-sm p-2" style="border-radius: 8px;">
                        Tampilkan Data
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card p-4">
    <div class="text-center mb-4">
        <h2 class="fw-bold mb-1">PENGIRIMAN SUSU</h2>
        <p class="mb-0">Bulan: {{ date('F Y', mktime(0, 0, 0, $bulan, 1, $tahun)) }}</p>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered text-center" style="border: 2px solid #000;">
            <thead style="background: #f1f5f9;">
                <tr>
                    <th style="width: 10%; border: 2px solid #000;">Tgl</th>
                    <th style="width: 23%; border: 2px solid #000;">Jumlah Susu (L)</th>
                    <th style="width: 10%; border: 2px solid #000;">Tgl</th>
                    <th style="width: 23%; border: 2px solid #000;">Jumlah Susu (L)</th>
                    <th style="width: 10%; border: 2px solid #000;">Tgl</th>
                    <th style="width: 24%; border: 2px solid #000;">Jumlah Susu (L)</th>
                </tr>
            </thead>
            <tbody>
                @for($i = 1; $i <= 11; $i++)
                <tr>
                    @php $cols = [$i, $i+11, $i+22]; @endphp
                    @foreach($cols as $day)
                        @if($day <= $daysInMonth)
                            <td style="border: 2px solid #000; font-weight: bold; background: #fafafa;">{{ $day }}</td>
                            <td style="border: 2px solid #000; font-size: 1.1rem;">
                                {{ isset($dailyTotals[$day]) ? number_format($dailyTotals[$day], 1, ',', '.') : '-' }}
                            </td>
                        @else
                            <td style="border: 2px solid #000; background: #eee;"></td>
                            <td style="border: 2px solid #000; background: #eee;"></td>
                        @endif
                    @endforeach
                </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <div class="mt-4 p-3 border" style="border-width: 2px !important; border-color: #000 !important; max-width: 300px;">
        <div class="d-flex justify-content-between font-bold" style="font-size: 1.2rem;">
            <span>TOTAL:</span>
            <span>{{ number_format($monthlyTotal, 1, ',', '.') }} Ltr</span>
        </div>
    </div>

    <div class="signature-section" style="display: flex; justify-content: space-between; margin-top: 50px; padding: 0 50px;">
        <div style="text-align: center; width: 250px;">
            <p class="mb-5">Mengetahui,</p>
            <div style="margin-top: 70px;">
                <p class="fw-bold mb-0" contenteditable="true">( ____________________ )</p>
                <p class="small text-muted mt-1" contenteditable="true">Tim Verifikasi</p>
            </div>
        </div>
        <div style="text-align: center; width: 250px;">
            <p class="mb-0" contenteditable="true">.........., .......... 20....</p>
            <p class="mb-5">Pengelola SIPERAH,</p>
            <div style="margin-top: 70px;">
                <p class="fw-bold mb-0" contenteditable="true">( ____________________ )</p>
                <p class="small text-muted mt-1" contenteditable="true">Nama Pengelola</p>
            </div>
        </div>
    </div>
</div>

<style>
    [contenteditable="true"]:hover {
        background-color: rgba(0,0,0,0.05);
        outline: 1px dashed var(--primary);
    }
    @media print {
        @page {
            size: landscape;
            margin: 1cm;
        }
        .navbar, .sidebar, .btn, form, .no-print { display: none !important; }
        .content { padding: 0 !important; margin: 0 !important; }
        .layout { display: block !important; }
        .card { border: none !important; box-shadow: none !important; padding: 0 !important; }
        body { background: white !important; font-size: 10pt; }
        .table { width: 100% !important; border-collapse: collapse !important; margin-bottom: 10px !important; }
        .table td, .table th {
            border: 1px solid #000 !important;
            padding: 4px 8px !important; 
            font-size: 9pt;
        }
        .mt-5 { margin-top: 2rem !important; }
        .signature-section { display: flex !important; justify-content: space-between !important; }
        [contenteditable="true"] { border: none !important; outline: none !important; }
    }
    .table-bordered td, .table-bordered th {
        border: 2px solid #000 !important;
        vertical-align: middle;
    }
</style>
@endsection
