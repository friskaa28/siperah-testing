@extends('layouts.app')

@section('title', 'Laporan Pusat - SIP-SUSU')

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h1 class="h3 fw-bold mb-0">Laporan Pusat</h1>
        <p class="text-muted mb-0">Laporan rekapitulasi literase tanpa nama & nominal untuk dikirim ke pusat</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <div class="d-flex gap-2 justify-content-md-end">
            <a href="{{ request()->fullUrlWithQuery(['export' => 'excel']) }}" class="btn btn-outline-success btn-sm px-3 shadow-sm" data-tooltip="Download laporan rekapitulasi dalam format Excel">
                📊 Export Excel
            </a>
            <button class="btn btn-outline-primary btn-sm px-3 shadow-sm" onclick="window.print()" data-tooltip="Cetak laporan ini atau simpan sebagai file PDF">
                🖨️ Cetak / Simpan PDF
            </button>
        </div>
    </div>
</div>

<!-- Responsive Filter Card -->
<div class="card shadow-sm border-0 mb-4 no-print" style="border-radius: 12px; background: #fff;">
    <div class="card-body p-3">
        <form action="{{ route('laporan.pusat') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-6 col-md-3">
                <label class="small fw-bold text-muted mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control form-control-sm shadow-sm" value="{{ $startDate }}" style="border-radius: 8px;">
            </div>
            <div class="col-6 col-md-3">
                <label class="small fw-bold text-muted mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control form-control-sm shadow-sm" value="{{ $endDate }}" style="border-radius: 8px;">
            </div>
            <div class="col-12 col-md-auto">
                <button type="submit" class="btn btn-primary btn-sm w-100 px-4 fw-bold shadow-sm" style="border-radius: 8px;">
                    Filter Recap
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card p-0 overflow-hidden">
    <div style="background: #f8fafc; padding: 20px; border-bottom: 2px solid var(--border); text-align: center;">
        <h2 class="fw-bold mb-1">REKAPITULASI SETORAN SUSU</h2>
        <p class="mb-0 text-muted">Periode: {{ \Carbon\Carbon::parse($startDate)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d/m/Y') }}</p>
    </div>
    <div class="table-responsive">
        <table class="table mb-0 text-center">
            <thead style="background: #f1f5f9;">
                <tr>
                    <th class="py-3 px-4" style="border: 1px solid #e2e8f0;">TANGGAL</th>
                    <th class="py-3 px-4" style="border: 1px solid #e2e8f0;">LITER PAGI</th>
                    <th class="py-3 px-4" style="border: 1px solid #e2e8f0;">LITER SORE</th>
                    <th class="py-3 px-4" style="border: 1px solid #e2e8f0;">GRAND TOTAL (L)</th>
                </tr>
            </thead>
            <tbody>
                @php $grandTotal = 0; @endphp
                @forelse($reportData as $data)
                <tr>
                    <td class="py-3 px-4 fw-bold" style="border: 1px solid #e2e8f0;">{{ \Carbon\Carbon::parse($data->tanggal)->format('d/m/Y') }}</td>
                    <td class="py-3 px-4" style="border: 1px solid #e2e8f0;">{{ number_format($data->pagi, 1, ',', '.') }}</td>
                    <td class="py-3 px-4" style="border: 1px solid #e2e8f0;">{{ number_format($data->sore, 1, ',', '.') }}</td>
                    <td class="py-3 px-4 fw-bold" style="border: 1px solid #e2e8f0; font-size: 1rem;">{{ number_format($data->total, 1, ',', '.') }}</td>
                </tr>
                @php $grandTotal += $data->total; @endphp
                @empty
                <tr><td colspan="4" class="text-center py-5 text-muted">Tidak ada data untuk periode ini.</td></tr>
                @endforelse
            </tbody>
            <tfoot style="background: #f8fafc; border-top: 2px solid var(--border);">
                <tr>
                    <td colspan="3" class="py-3 px-4 text-end fw-bold">TOTAL KESELURUHAN</td>
                    <td class="py-3 px-4 fw-bold" style="font-size: 1.25rem; color: var(--primary); border: 2px solid var(--primary);">{{ number_format($grandTotal, 1, ',', '.') }} L</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-6 text-center">
        <p class="mb-5">Mengetahui,</p>
        <p class="mt-5 fw-bold">( ____________________ )</p>
    </div>
    <div class="col-md-6 text-center">
        <p class="mb-5">Pengelola SIPERAH,</p>
        <p class="mt-5 fw-bold">( ____________________ )</p>
    </div>
</div>

<style>
    @media print {
        .no-print { display: none !important; }
        .sidebar { display: none !important; }
        .navbar { display: none !important; }
        .content { padding: 0 !important; margin: 0 !important; }
        .card { border: none !important; box-shadow: none !important; }
        body { background: white !important; }
    }
</style>
@endsection
