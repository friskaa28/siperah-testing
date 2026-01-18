@extends('layouts.app')

@section('title', 'Laporan Pendapatan - SIPERAH')

@section('content')

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h1 class="h3 fw-bold mb-0"><i class="fas fa-file-invoice-dollar"></i> Laporan Pendapatan</h1>
        <p class="text-muted mb-0">Rincian setoran susu dan potongan kasbon periode ini</p>
    </div>
    <div class="col-md-6 text-md-end mt-3 mt-md-0">
        <a href="{{ route('peternak.laporan.pdf', request()->all()) }}" class="btn btn-outline-danger btn-sm px-3 shadow-sm">
            <i class="fas fa-file-pdf"></i> Download PDF
        </a>
    </div>
</div>

<!-- Responsive Filter Card -->
<div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; background: #fff;">
    <div class="card-body p-3">
        <form action="{{ route('peternak.laporan.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-12 col-md-3">
                <label class="small fw-bold text-muted mb-1">Periode</label>
                <select name="range" class="form-select form-select-sm shadow-sm" onchange="this.form.submit()" style="border-radius: 8px;">
                    <option value="1" {{ $range == '1' ? 'selected' : '' }}>Periode Aktif (14-13)</option>
                    <option value="custom" {{ $range == 'custom' ? 'selected' : '' }}>Pilih Tanggal Manual</option>
                </select>
            </div>
            
            @if($range == 'custom')
            <div class="col-6 col-md-3">
                <label class="small fw-bold text-muted mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control form-control-sm shadow-sm" value="{{ $startDate->format('Y-m-d') }}" style="border-radius: 8px;">
            </div>
            <div class="col-6 col-md-3">
                <label class="small fw-bold text-muted mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control form-control-sm shadow-sm" value="{{ $endDate->format('Y-m-d') }}" style="border-radius: 8px;">
            </div>
            <div class="col-12 col-md-auto">
                <button type="submit" class="btn btn-primary btn-sm w-100 px-4 fw-bold shadow-sm" style="border-radius: 8px;">
                    Filter Data
                </button>
            </div>
            @endif
        </form>
    </div>
</div>


<div class="grid" style="grid-template-columns: 1.5fr 1fr; gap: 1.5rem;">
    <!-- Detail Setoran -->
    <div class="card">
        <h3 class="fw-bold mb-3" style="font-size: 1rem;"><i class="fas fa-history"></i> Histori Setoran Susu</h3>
        <table class="table small">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Waktu</th>
                    <th class="text-end">Liter</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produksi as $p)
                <tr>
                    <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                    <td>{{ ucfirst($p->waktu_setor) }}</td>
                    <td class="text-end fw-bold">{{ number_format($p->jumlah_susu_liter, 1) }}</td>
                </tr>
                @empty
                <tr><td colspan="3" class="text-center py-4 text-muted">Tidak ada data produksi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Detail Kasbon -->
    <div class="card">
        <h3 class="fw-bold mb-3" style="font-size: 1rem;"><i class="fas fa-shopping-basket"></i> Histori Kasbon & Logistik</h3>
        <table class="table small">
            <thead>
                <tr>
                    <th>Barang / Tgl</th>
                    <th class="text-end">Rupiah</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kasbonHistory as $k)
                <tr>
                    <td>
                        <div class="fw-bold">{{ $k->nama_item }}</div>
                        <div class="text-muted" style="font-size: 0.75rem;">{{ $k->tanggal->format('d/m/y') }} | {{ $k->qty }} x Rp{{ number_format($k->harga_satuan, 0) }}</div>
                    </td>
                    <td class="text-end text-danger fw-bold">-{{ number_format($k->total_rupiah, 0) }}</td>
                </tr>
                @empty
                <tr><td colspan="2" class="text-center py-4 text-muted">Tidak ada data kasbon.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card mt-4 border-0 shadow-sm" style="background: #f8fafc; border-radius: 15px; padding: 1.5rem;">
    <div class="d-flex align-items-center gap-4">
        <div class="bg-white p-2 shadow-sm" style="border-radius: 12px;">
            @if($qrBase64)
                <img src="{{ $qrBase64 }}" alt="QR Verification" style="width: 80px; height: 80px;">
            @else
                <div style="width: 80px; height: 80px; border: 1px solid #ddd; display: flex; align-items: center; justify-content: center; font-size: 8pt;">QR N/A</div>
            @endif
        </div>
        <div class="text-start">
            <h5 class="fw-bold mb-1 text-primary">Verifikasi Dokumen Digital</h5>
            <p class="text-muted small mb-1">ID MITRA: <strong>{{ $peternakId }}</strong></p>
            <p class="text-muted mb-0" style="font-size: 0.85rem;">Dokumen ini dihasilkan secara otomatis dan sah sebagai bukti transaksi digital pada sistem <strong>SIPERAH</strong>.</p>
        </div>
    </div>
</div>

@endsection
