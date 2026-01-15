@extends('layouts.app')

@section('title', 'Manajemen Distribusi - SIPERAH')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-7">
            <h2 class="fw-bold mb-1">🚚 Manajemen Distribusi</h2>
            <p class="text-muted mb-0">Kelola pengiriman susu ke pengepul atau industri</p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0">
             <div class="d-flex gap-2 justify-content-md-end flex-wrap">
                <a href="{{ route('distribusi.download_template') }}" class="btn btn-outline-success btn-sm">
                    📥 Download Template
                </a>
                <a href="{{ route('distribusi.export_pdf', request()->all()) }}" class="btn btn-outline-danger btn-sm" target="_blank">
                    🖨️ Cetak PDF
                </a>
             </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Quick Actions: Import & Manual Input -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-4">🚀 Aksi Cepat</h5>
                    
                    <!-- Import Section -->
                    <div class="mb-4 pb-4 border-bottom">
                        <label class="form-label text-uppercase mb-3">Import dari Excel/CSV</label>
                        <form action="{{ route('distribusi.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="input-group">
                                <input type="file" name="file_csv" class="form-control" accept=".csv" required style="border-top-right-radius: 0; border-bottom-right-radius: 0;">
                                <button type="submit" class="btn btn-success" style="border-top-left-radius: 0; border-bottom-left-radius: 0; padding: 0 1.5rem;">Upload</button>
                            </div>
                            <p class="text-muted small mt-2 mb-0">Format: CSV (Pemisah , atau ;)</p>
                        </form>
                    </div>

                    <!-- Manual Input Form -->
                    <form action="{{ route('distribusi.store') }}" method="POST">
                        @csrf
                        <label class="form-label text-uppercase mb-3">Input Manual</label>
                        
                        <div class="mb-3">
                            <select name="idpeternak" class="form-select" required>
                                <option value="">-- Pilih Peternak --</option>
                                @foreach($peternaks as $p)
                                    <option value="{{ $p->idpeternak }}">{{ $p->nama_peternak }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <input type="text" name="tujuan" class="form-control" placeholder="Tujuan / Buyer" required>
                        </div>

                        <div class="d-flex gap-3 mb-3">
                            <div class="input-group" style="flex: 2;">
                                <input type="number" step="0.1" name="volume" class="form-control" placeholder="Qty" required>
                                <span class="input-group-text">L</span>
                            </div>
                            <input type="number" name="harga_per_liter" class="form-control" placeholder="Rp/L" value="7000" required style="flex: 2;">
                        </div>

                        <div class="mb-4">
                            <input type="date" name="tanggal_kirim" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <button type="submit" class="btn btn-primary w-100 py-3 fw-bold mt-2" style="border-radius: 12px; font-size: 1rem;">Simpan Distribusi</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- History & Filters -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 h-100" style="border-radius: 15px;">
                <div class="card-header bg-transparent border-0 p-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-4">
                        <h5 class="fw-bold mb-0">📋 Riwayat Pengiriman</h5>
                        <form action="{{ route('distribusi.index') }}" method="GET" class="d-flex gap-4 flex-wrap align-items-center">
                            <div class="d-flex align-items-center gap-2">
                                <label class="small text-muted fw-bold mb-0">Bulan:</label>
                                <select name="bulan" class="form-select w-auto shadow-none" style="padding-top: 5px; padding-bottom: 5px; min-width: 140px;" onchange="this.form.submit()">
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                            {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <label class="small text-muted fw-bold mb-0">Tahun:</label>
                                <select name="tahun" class="form-select w-auto shadow-none" style="padding-top: 5px; padding-bottom: 5px; min-width: 100px;" onchange="this.form.submit()">
                                    <option value="2025" {{ $tahun == 2025 ? 'selected' : '' }}>2025</option>
                                    <option value="2026" {{ $tahun == 2026 ? 'selected' : '' }}>2026</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3">Tanggal & Peternak</th>
                                    <th class="py-3">Tujuan</th>
                                    <th class="py-3 text-center">Volume</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3 text-end px-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($distribusi as $d)
                                <tr>
                                    <td class="px-4 py-3">
                                        <div class="fw-bold">{{ $d->tanggal_kirim->format('d/m/Y') }}</div>
                                        <div class="small text-muted">{{ $d->peternak->nama_peternak }}</div>
                                    </td>
                                    <td class="py-3">{{ $d->tujuan }}</td>
                                    <td class="py-3 text-center fw-bold text-primary">{{ number_format($d->volume, 1) }} L</td>
                                    <td class="py-3">
                                        <span class="badge @if($d->status=='pending') bg-warning-subtle text-warning @elseif($d->status=='terkirim') bg-info-subtle text-info @else bg-success-subtle text-success @endif" style="font-weight: 600; padding: 6px 12px; border-radius: 6px;">
                                            {{ ucfirst($d->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-end px-4">
                                        <form action="{{ route('distribusi.updateStatus', $d->iddistribusi) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <select name="status" class="form-select form-select-sm" style="width: auto; display: inline-block;" onchange="this.form.submit()">
                                                <option value="pending" {{ $d->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="terkirim" {{ $d->status == 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                                                <option value="diterima" {{ $d->status == 'diterima' ? 'selected' : '' }}>Diterima</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Tidak ada riwayat.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-600 { font-weight: 600; font-size: 0.9rem; color: #475569; }
</style>
@endsection
