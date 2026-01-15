@extends('layouts.app')

@section('title', 'Detail Produksi - SIPERAH')

@section('content')
<div class="container-fluid px-2">
    <div class="row align-items-center mb-4">
        <div class="col">
            <h3 class="fw-bold mb-0">📦 Detail Produksi</h3>
            <p class="text-muted small mb-0">Informasi detail pencatatan susu harian</p>
        </div>
        <div class="col-auto">
            <a href="{{ route('produksi.index') }}" class="btn btn-outline-secondary btn-sm">
                ← Kembali
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0" style="border-radius: 12px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-primary mb-4 border-bottom pb-2">Data Produksi</h5>
                    
                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted small uppercase fw-bold">Tanggal</div>
                        <div class="col-sm-8 fw-medium">{{ $produksi->tanggal->format('d F Y') }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted small uppercase fw-bold">Waktu Setor</div>
                        <div class="col-sm-8 fw-medium text-capitalize">
                            @if($produksi->waktu_setor == 'pagi')
                                <span class="badge bg-warning text-dark"><i class="fas fa-sun me-1"></i> Pagi</span>
                            @else
                                <span class="badge bg-secondary"><i class="fas fa-moon me-1"></i> Sore</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-sm-4 text-muted small uppercase fw-bold">Peternak</div>
                        <div class="col-sm-8 fw-medium">
                            {{ $produksi->peternak->nama_peternak }}
                            <small class="text-muted d-block">{{ $produksi->peternak->no_peternak }}</small>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-sm-4 text-muted small uppercase fw-bold">Jumlah Susu</div>
                        <div class="col-sm-8">
                            <span class="display-6 fw-bold text-dark">{{ number_format($produksi->jumlah_susu_liter, 2) }}</span>
                            <span class="text-muted fs-5">Liter</span>
                        </div>
                    </div>

                    <h6 class="fw-bold text-secondary mb-3 mt-4 border-bottom pb-2">Rincian Biaya Operasional</h6>
                    
                    <div class="table-responsive bg-light rounded p-3">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-muted">Biaya Pakan</td>
                                <td class="text-end fw-medium">Rp {{ number_format($produksi->biaya_pakan, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Biaya Tenaga</td>
                                <td class="text-end fw-medium">Rp {{ number_format($produksi->biaya_tenaga, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Biaya Operasional Lain</td>
                                <td class="text-end fw-medium">Rp {{ number_format($produksi->biaya_operasional, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="border-top border-secondary">
                                <td class="pt-2 fw-bold text-dark">Total Biaya</td>
                                <td class="pt-2 text-end fw-bold text-danger">Rp {{ number_format($produksi->total_biaya, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>

                    @if($produksi->catatan)
                    <div class="alert alert-info d-flex align-items-center mt-4 mb-0" role="alert">
                        <i class="fas fa-info-circle me-3 fs-4"></i>
                        <div>
                            <div class="small fw-bold uppercase">Catatan</div>
                            <div>{{ $produksi->catatan }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
