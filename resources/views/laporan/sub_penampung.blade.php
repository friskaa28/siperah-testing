@extends('layouts.app')

@section('title', 'Laporan Sub-Penampung - SIPERAH')

@section('content')
<div class="row align-items-center mb-4 no-print">
    <div class="col-md-6">
        <h1 class="h3 fw-bold mb-0">Laporan Sub-Penampung</h1>
        <p class="text-muted mb-0">Rekapitulasi setoran susu dari mitra kategori Sub-Penampung</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <div class="d-flex gap-2 justify-content-md-end">
            <button class="btn btn-outline-primary btn-sm px-3 shadow-sm" onclick="window.print()">
                🖨️ Cetak / Simpan PDF
            </button>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4 no-print" style="border-radius: 12px; background: #fff;">
    <div class="card-body p-3">
        <form action="{{ route('laporan.sub_penampung') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-6 col-md-3">
                <label class="small fw-bold text-muted mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
            </div>
            <div class="col-6 col-md-3">
                <label class="small fw-bold text-muted mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
            </div>
            <div class="col-12 col-md-auto">
                <button type="submit" class="btn btn-primary btn-sm w-100 px-4 fw-bold">
                    Filter Laporan
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card p-0 overflow-hidden shadow-sm border-0" style="border-radius: 12px;">
    <div style="background: #f8fafc; padding: 20px; border-bottom: 2px solid var(--border); text-align: center;">
        <h2 class="fw-bold mb-1">REKAPITULASI SETORAN SUB-PENAMPUNG</h2>
        <p class="mb-0 text-muted">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
    </div>
    <div class="table-responsive">
        <table class="table mb-0 text-center">
            <thead style="background: #f1f5f9;">
                <tr>
                    <th class="py-3 px-4" style="border-bottom: 2px solid #e2e8f0; text-align: left;">NAMA SUB-PENAMPUNG</th>
                    <th class="py-3 px-4" style="border-bottom: 2px solid #e2e8f0;">LITER PAGI</th>
                    <th class="py-3 px-4" style="border-bottom: 2px solid #e2e8f0;">LITER SORE</th>
                    <th class="py-3 px-4" style="border-bottom: 2px solid #e2e8f0;">TOTAL (L)</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @forelse($reportData as $data)
                <tr style="page-break-inside: avoid;">
                    <td class="py-3 px-4 fw-bold" style="text-align: left;">{{ $data->peternak->nama_peternak }}</td>
                    <td class="py-3 px-4">{{ number_format($data->pagi, 1, ',', '.') }}</td>
                    <td class="py-3 px-4">{{ number_format($data->sore, 1, ',', '.') }}</td>
                    <td class="py-3 px-4 fw-bold" style="font-size: 1rem; text-align: right;">{{ number_format($data->total, 1, ',', '.') }}</td>
                </tr>
                @php $grandTotal += $data->total; @endphp
                @empty
                <tr><td colspan="4" class="text-center py-5 text-muted">Tidak ada data untuk periode ini.</td></tr>
                @endforelse
            </tbody>
            <tfoot style="background: #f8fafc; border-top: 2px solid var(--border);">
                <tr class="fw-bold" style="page-break-inside: avoid;">
                    <td colspan="3" class="py-3 px-4 text-end fw-bold">TOTAL KESELURUHAN</td>
                    <td class="py-3 px-4 fw-bold text-primary" style="font-size: 1.25rem; text-align: right;">{{ number_format($grandTotal, 1, ',', '.') }} L</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="signature-section" style="display: flex; justify-content: space-between; margin-top: 50px; padding: 0 50px; page-break-inside: avoid;">
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

<style>
    [contenteditable="true"]:hover {
        background-color: rgba(0,0,0,0.05);
        outline: 1px dashed var(--primary);
    }
    @media print {
        @page { size: portrait; margin: 1cm; }
        .no-print, .sidebar, .navbar, .footer, .btn, form { display: none !important; }
        .content { padding: 0 !important; margin: 0 !important; width: 100% !important; overflow: visible !important; }
        .layout { display: block !important; min-height: 0 !important; }
        .card { border: none !important; box-shadow: none !important; padding: 0 !important; margin-bottom: 20px !important; background: transparent !important; }
        body { background: white !important; font-size: 10pt; color: black; }
        table { width: 100% !important; border-collapse: collapse !important; border: 1px solid #000 !important; margin-bottom: 20px !important; }
        th, td { border: 1px solid #000 !important; padding: 8px !important; color: black !important; }
        .bg-primary { background-color: #f1f5f9 !important; color: black !important; }
        .text-white { color: black !important; }
        .text-primary { color: black !important; }
        .signature-section { display: flex !important; justify-content: space-between !important; margin-top: 50px !important; page-break-inside: avoid !important; }
        [contenteditable="true"] { border: none !important; outline: none !important; }
        tr { page-break-inside: avoid !important; }
        tfoot { display: table-footer-group; }
    }
</style>
@endsection
