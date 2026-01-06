@extends('layouts.app')

@section('title', 'Laporan Pendapatan - SIPERAH')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h2>📄 Laporan Pendapatan</h2>
        </div>
        <div class="col-md-6 text-end">
            <a href="/dashboard-peternak" class="btn btn-outline-secondary">Kembali</a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('laporan.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Jenis Laporan</label>
                    <select name="filter" class="form-select" onchange="this.form.submit()">
                        <option value="harian" {{ $filter == 'harian' ? 'selected' : '' }}>Harian</option>
                        <option value="bulanan" {{ $filter == 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                        <option value="tahunan" {{ $filter == 'tahunan' ? 'selected' : '' }}>Tahunan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Tanggal / Bulan / Tahun</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}" onchange="this.form.submit()">
                </div>
                <div class="col-md-6 text-end">
                    <a href="{{ route('laporan.export', ['filter' => $filter, 'date' => $date]) }}" class="btn btn-danger" target="_blank">
                         🖨️ Cetak PDF
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5>Total Susu (Liter)</h5>
                    <h3>{{ number_format($totalLiter, 2, ',', '.') }} Liter</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5>Total Pendapatan (Rp)</h5>
                    <h3>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Tanggal</th>
                            <th>Liter Susu</th>
                            <th>Pendapatan</th>
                            <th>Status Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($laporan as $item)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d F Y') }}</td>
                            <td>{{ number_format($item->produksi->jumlah_susu_liter, 2, ',', '.') }} L</td>
                            <td class="text-success fw-bold">Rp {{ number_format($item->hasil_pemilik, 0, ',', '.') }}</td>
                            <td>
                                @if($item->status == 'lunas')
                                    <span class="badge bg-success">Lunas</span>
                                @elseif($item->status == 'sebagian')
                                    <span class="badge bg-warning">Sebagian</span>
                                @else
                                    <span class="badge bg-danger">Pending</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Tidak ada data untuk periode ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
