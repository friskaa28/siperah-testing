@extends('layouts.app')

@section('title', 'Laporan Pendapatan - SIPERAH')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2 class="fw-bold">📄 Laporan Pendapatan</h2>
            <p class="text-muted">Pantau rincian bagi hasil dan slip gaji bulanan Anda</p>
        </div>
        <div class="col-md-6 text-end">
            <form action="{{ route('peternak.laporan.index') }}" method="GET" class="d-inline-flex gap-2">
                <select name="bulan" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $bulan == $m ? 'selected' : '' }}>
                            {{ Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                        </option>
                    @endforeach
                </select>
                <select name="tahun" class="form-select form-select-sm" onchange="this.form.submit()" style="width: auto;">
                    <option value="2025" {{ $tahun == 2025 ? 'selected' : '' }}>2025</option>
                    <option value="2026" {{ $tahun == 2026 ? 'selected' : '' }}>2026</option>
                </select>
                <a href="{{ route('peternak.laporan.pdf', request()->all()) }}" class="btn btn-danger btn-sm px-3" target="_blank" style="border-radius: 6px; font-weight: 600;">
                    🖨️ Cetak Slip Gaji (PDF)
                </a>
            </form>
        </div>
    </div>

    @if($slip)
    <!-- Header Ringkasan Slip (Jika ada slip resmi dari Admin) -->
    <div class="card shadow-sm border-0 mb-4" style="border-radius: 12px; border-left: 5px solid #10B981; background: #ECFDF5;">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="fw-bold text-success mb-1">Status Pembayaran: {{ strtoupper($slip->status) }}</h5>
                    <p class="text-muted small mb-0">Slip gaji periode {{ Carbon\Carbon::create()->month($bulan)->translatedFormat('F') }} {{ $tahun }} telah diterbitkan oleh Admin Kantor.</p>
                </div>
                <div class="col-md-4 text-md-end">
                    <h3 class="fw-bold text-success mb-0">Rp {{ number_format($slip->sisa_pembayaran, 0, ',', '.') }}</h3>
                    <p class="text-muted small">Total Saldo Diterima</p>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <!-- Rincian Bagi Hasil Harian -->
        <div class="col-md-8">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-transparent border-0 p-4 pb-0">
                    <h5 class="fw-bold">📅 Rincian Bagi Hasil Harian</h5>
                </div>
                <div class="card-body p-0 mt-3">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead style="background: #f8fafc; border-bottom: 2px solid #e2e8f0;">
                                <tr>
                                    <th class="px-4 py-3">Tanggal</th>
                                    <th class="py-3">Produksi (L)</th>
                                    <th class="py-3">Total Pendapatan</th>
                                    <th class="py-3">Porsi Anda ({{ $bagiHasil->first()->persentase_pemilik ?? 60 }}%)</th>
                                    <th class="py-3">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($bagiHasil as $bh)
                                <tr>
                                    <td class="px-4 py-3 fw-medium">{{ $bh->tanggal->format('d/m/Y') }}</td>
                                    <td class="py-3 text-center">{{ number_format($bh->produksi->jumlah_susu_liter, 1) }}</td>
                                    <td class="py-3">Rp {{ number_format($bh->total_pendapatan, 0, ',', '.') }}</td>
                                    <td class="py-3 fw-bold text-primary">Rp {{ number_format($bh->hasil_pemilik, 0, ',', '.') }}</td>
                                    <td class="py-3">
                                        <span class="badge @if($bh->status=='pending') bg-warning @else bg-success @endif" style="font-weight: 500;">
                                            {{ ucfirst($bh->status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">Belum ada data bagi hasil untuk bulan ini.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Potongan & Slip -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-header bg-transparent border-0 p-4 pb-0">
                    <h5 class="fw-bold">🧾 Detail Potongan (Bulan Ini)</h5>
                </div>
                <div class="card-body p-4">
                    @if($slip)
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="text-muted">Total Pendapatan</span>
                                <span class="fw-bold text-dark">Rp {{ number_format($slip->total_pembayaran, 0, ',', '.') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0 text-danger">
                                <span>Potongan Pakan</span>
                                <span>- Rp {{ number_format(($slip->potongan_pakan_a + $slip->potongan_pakan_b + $slip->potongan_pakan_b_2), 0, ',', '.') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0 text-danger">
                                <span>Konsentrat & Vitamix</span>
                                <span>- Rp {{ number_format(($slip->potongan_konsentrat + $slip->potongan_vitamix), 0, ',', '.') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0 text-danger">
                                <span>Lain-lain (Keswan/IB)</span>
                                <span>- Rp {{ number_format(($slip->potongan_ib_keswan + $slip->potongan_vaksin + $slip->potongan_lain_lain), 0, ',', '.') }}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between px-0 text-danger">
                                <span>Potongan Kas Bon</span>
                                <span>- Rp {{ number_format($slip->potongan_kas_bon, 0, ',', '.') }}</span>
                            </li>
                            <hr>
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span class="fw-bold">Total Diterima</span>
                                <span class="fw-bold text-success fs-5">Rp {{ number_format($slip->sisa_pembayaran, 0, ',', '.') }}</span>
                            </li>
                        </ul>
                    @else
                        <div class="text-center py-4 bg-light" style="border-radius: 8px;">
                            <span style="font-size: 2rem;">⏳</span>
                            <p class="text-muted small mt-2 mb-0">Slip gaji resmi belum diterbitkan oleh Admin Kantor.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
