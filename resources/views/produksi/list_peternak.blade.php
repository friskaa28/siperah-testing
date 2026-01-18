@extends('layouts.app')

@section('title', 'Riwayat Produksi - SIPERAH')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="h3 mb-0"><i class="fas fa-history"></i> Riwayat Produksi</h1>
        <p class="text-muted">Kelola dan pantau data pengumpulan susu harian</p>
    </div>
    @if(isset($isAdmin) && $isAdmin)
    <div class="col-md-6">
        <form action="{{ route('produksi.index') }}" method="GET" class="d-flex gap-2 justify-content-end">
            <select name="idpeternak" class="form-select border-2" style="max-width: 250px;" onchange="this.form.submit()">
                <option value="">-- Semua Peternak --</option>
                @foreach($peternaks as $p)
                    <option value="{{ $p->idpeternak }}" {{ request('idpeternak') == $p->idpeternak ? 'selected' : '' }}>
                        {{ $p->nama_peternak }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
    @endif
</div>

<div class="card shadow-sm border-0" style="border-radius: 12px;">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="px-4 py-3">Tanggal</th>
                    @if(isset($isAdmin) && $isAdmin)
                        <th class="py-3">Peternak</th>
                    @endif
                    <th class="py-3">Susu (L)</th>
                    <th class="py-3">Operasional</th>
                    <th class="py-3">Total Biaya</th>
                    <th class="py-3 text-end px-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produksi as $p)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="fw-bold">{{ $p->tanggal->format('d/m/Y') }}</div>
                            <div class="small text-muted text-capitalize">{{ $p->waktu_setor }}</div>
                        </td>
                        @if(isset($isAdmin) && $isAdmin)
                            <td class="py-3">{{ $p->peternak->nama_peternak }}</td>
                        @endif
                        <td class="py-3 fw-bold">{{ number_format($p->jumlah_susu_liter, 2) }}</td>
                        <td class="py-3">
                            <div class="small">Pakan: Rp {{ number_format($p->biaya_pakan, 0, ',', '.') }}</div>
                            <div class="small">Tenaga: Rp {{ number_format($p->biaya_tenaga, 0, ',', '.') }}</div>
                            <div class="small">Ops: Rp {{ number_format($p->biaya_operasional, 0, ',', '.') }}</div>
                        </td>
                        <td class="py-3">
                            <span class="badge bg-primary-subtle text-primary" style="font-size: 0.95rem;">
                                Rp {{ number_format($p->biaya_pakan + $p->biaya_tenaga + $p->biaya_operasional, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="py-3 text-end px-4">
                            <a href="{{ route('produksi.detail', $p->idproduksi) }}" class="btn btn-sm btn-outline-primary" style="border-radius: 6px;">
                                Lihat Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ isset($isAdmin) && $isAdmin ? 6 : 5 }}" class="text-center py-5 text-muted">
                            <div class="mb-2" style="font-size: 2rem;"><i class="fas fa-inbox"></i></div>
                            Belum ada data produksi tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($produksi->hasPages())
        <div class="card-footer bg-white border-0 p-4">
            {{ $produksi->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
