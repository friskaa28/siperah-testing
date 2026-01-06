@extends('layouts.app')

@section('title', 'Manajemen Distribusi - SIPERAH')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold">🚚 Manajemen Distribusi</h2>
            <p class="text-muted">Input Data & Pantau Riwayat Pengiriman Susu</p>
        </div>
        <div class="col-md-6 text-end">
             <a href="{{ route('distribusi.download_template') }}" class="btn btn-outline-success me-2" style="border-radius: 8px; font-weight: 600;">
                📥 Unduh Template CSV
            </a>
             <button type="button" class="btn btn-primary px-4 py-2" data-bs-toggle="collapse" data-bs-target="#formImport" style="border-radius: 8px; font-weight: 600;">
                📊 Import Data Excel/CSV
            </button>
        </div>
    </div>

    <!-- Import Section (Collapsible) -->
    <div class="collapse mb-4" id="formImport">
        <div class="card shadow-sm border-0" style="border-radius: 12px; border-left: 5px solid #10B981;">
            <div class="card-body p-4">
                <h5 class="card-title fw-bold mb-3">Upload Data Distribusi (CSV)</h5>
                <form action="{{ route('distribusi.import') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                    @csrf
                    <div class="col-md-8">
                        <input type="file" name="file_csv" class="form-control border-2" accept=".csv" required>
                        <p class="text-muted small mt-2 mb-0">Pastikan format file sesuai dengan template yang diunduh.</p>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold" style="border-radius: 8px;">Upload & Proses</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Input Form Section (Collapsible) -->
    <div class="mb-4">
        <div class="card shadow-sm border-0" style="border-radius: 12px; border-left: 5px solid var(--primary);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title fw-bold mb-0">Input Manual Distribusi</h5>
                    <button class="btn btn-sm btn-link text-decoration-none fw-bold" data-bs-toggle="collapse" data-bs-target="#innerInputForm">
                        Tampilkan/Sembunyikan Form
                    </button>
                </div>
                <div class="collapse" id="innerInputForm">
                <form action="{{ route('distribusi.store') }}" method="POST" class="row g-3">
                    @csrf
                    <div class="col-md-3">
                        <label class="form-label fw-600">Pilih Peternak</label>
                        <select name="idpeternak" class="form-select border-2" required>
                            <option value="">-- Pilih --</option>
                            @foreach($peternaks as $p)
                                <option value="{{ $p->idpeternak }}">{{ $p->nama_peternak }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-600">Tujuan / Buyer</label>
                        <input type="text" name="tujuan" class="form-control border-2" placeholder="Contoh: IPS Lembang" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-600">Volume (Liter)</label>
                        <input type="number" step="0.1" name="volume" class="form-control border-2" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-600">Harga Per Liter (Rp)</label>
                        <input type="number" name="harga_per_liter" class="form-control border-2" value="7000" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-600">Tanggal Kirim</label>
                        <input type="date" name="tanggal_kirim" class="form-control border-2" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-600">Catatan/Keterangan</label>
                        <input type="text" name="catatan" class="form-control border-2" placeholder="Opsional">
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-success w-100 py-2 fw-bold" style="border-radius: 8px;">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Filter & History Table Section -->
    <div class="card shadow-sm border-0" style="border-radius: 12px; background: white;">
        <div class="card-header bg-transparent border-0 p-4 pb-0">
             <form action="{{ route('distribusi.index') }}" method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Filter Peternak</label>
                    <select name="idpeternak" class="form-select form-select-sm border-2" onchange="this.form.submit()">
                        <option value="">-- Semua --</option>
                        @foreach($peternaks as $p)
                            <option value="{{ $p->idpeternak }}" {{ $idpeternak == $p->idpeternak ? 'selected' : '' }}>
                                {{ $p->nama_peternak }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Bulan</label>
                    <select name="bulan" class="form-select form-select-sm border-2" onchange="this.form.submit()">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                                {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Tahun</label>
                    <select name="tahun" class="form-select form-select-sm border-2" onchange="this.form.submit()">
                        <option value="2025" {{ $tahun == 2025 ? 'selected' : '' }}>2025</option>
                        <option value="2026" {{ $tahun == 2026 ? 'selected' : '' }}>2026</option>
                    </select>
                </div>
                <div class="col-md-5 text-end">
                    <a href="{{ route('distribusi.export_pdf', request()->all()) }}" class="btn btn-outline-danger btn-sm px-3" target="_blank" style="border-radius: 6px; font-weight: 600;">
                        🖨️ Cetak PDF Rekap
                    </a>
                </div>
            </form>
        </div>
        <div class="card-body p-0 mt-3">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                        <tr>
                            <th class="px-4 py-3">Tanggal</th>
                            <th class="py-3">Peternak</th>
                            <th class="py-3">Tujuan</th>
                            <th class="py-3 text-center">Volume (L)</th>
                            <th class="py-3">Status</th>
                            <th class="py-3 text-end px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($distribusi as $d)
                        <tr>
                            <td class="px-4 py-3 fw-medium">{{ $d->tanggal_kirim->format('d/m/Y') }}</td>
                            <td class="py-3">{{ $d->peternak->nama_peternak }}</td>
                            <td class="py-3">{{ $d->tujuan }}</td>
                            <td class="py-3 text-center fw-bold">{{ number_format($d->volume, 1) }}</td>
                            <td class="py-3">
                                <span class="badge @if($d->status=='pending') bg-warning @elseif($d->status=='terkirim') bg-info @else bg-success @endif" style="font-weight: 500; padding: 4px 10px;">
                                    {{ ucfirst($d->status) }}
                                </span>
                            </td>
                            <td class="py-3 text-end px-4">
                                <form action="{{ route('distribusi.updateStatus', $d->iddistribusi) }}" method="POST" style="display: inline-block;">
                                    @csrf
                                    @method('PUT')
                                    <select name="status" class="form-select form-select-sm d-inline-block border-2" style="width: auto;" onchange="this.form.submit()">
                                        <option value="pending" {{ $d->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="terkirim" {{ $d->status == 'terkirim' ? 'selected' : '' }}>Terkirim</option>
                                        <option value="diterima" {{ $d->status == 'diterima' ? 'selected' : '' }}>Diterima</option>
                                    </select>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">Tidak ada riwayat untuk periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-600 { font-weight: 600; font-size: 0.9rem; color: #475569; }
</style>
@endsection
