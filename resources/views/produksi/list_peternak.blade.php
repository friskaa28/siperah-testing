@extends('layouts.app')

@section('title', 'Riwayat Setor Susu - SIPERAH')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-6">
        <h1 class="h3 mb-0"><i class="fas fa-history"></i> Riwayat Setor Susu</h1>
        <p class="text-muted">Kelola dan pantau data pengumpulan susu harian</p>
    </div>
    @if(isset($isAdmin) && $isAdmin)
    <div class="col-md-6">
        <form action="{{ route('produksi.index') }}" method="GET" class="d-flex gap-2 justify-content-end">
            @if(request('idpeternak')) <input type="hidden" name="idpeternak" value="{{ request('idpeternak') }}"> @endif
            <select name="per_page" class="form-select border-2" style="max-width: 120px;" onchange="this.form.submit()">
                <option value="15" {{ $perPage == 15 ? 'selected' : '' }}>15 baris</option>
                <option value="30" {{ $perPage == 30 ? 'selected' : '' }}>30 baris</option>
                <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50 baris</option>
            </select>
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
                    <th class="py-3">Waktu</th>
                    @if(isset($isAdmin) && $isAdmin)
                        <th class="py-3">Nama</th>
                        <th class="py-3">Kategori</th>
                    @endif
                    <th class="py-3">Susu (L)</th>
                    <th class="py-3 text-center px-4">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($produksi as $p)
                @php
                    // Legacy total just for checking if we strictly need to show something? 
                    // But user asked to conform to input which has NO cost now.
                @endphp
                    <tr>
                        <td class="px-4 py-3 fw-bold">{{ $p->tanggal->format('d/m/Y') }}</td>
                        <td class="py-3 text-capitalize">{{ $p->waktu_setor }}</td>
                        @if(isset($isAdmin) && $isAdmin)
                            <td class="py-3">{{ $p->peternak->nama_peternak }}</td>
                            <td class="py-3">
                                @php
                                    $kategori = [
                                        'peternak' => 'Peternak',
                                        'sub_penampung_tr' => 'Sub-Penampung TR',
                                        'sub_penampung_p' => 'Sub-Penampung P'
                                    ];
                                @endphp
                                <span class="badge bg-light text-dark border">
                                    {{ $kategori[$p->peternak->status_mitra] ?? ucfirst($p->peternak->status_mitra) }}
                                </span>
                            </td>
                        @endif
                        <td class="py-3 fw-bold">{{ number_format($p->jumlah_susu_liter, 2, ',', '.') }}</td>
                        <td class="py-3 px-4">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="{{ route('produksi.detail', $p->idproduksi) }}" class="action-btn detail" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if(isset($isAdmin) && $isAdmin)
                                    <a href="{{ route('produksi.edit', $p->idproduksi) }}" class="action-btn edit" title="Edit Data">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('produksi.destroy', $p->idproduksi) }}" method="POST" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn delete" title="Hapus Data">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ isset($isAdmin) && $isAdmin ? 6 : 4 }}" class="text-center py-5 text-muted">
                            <div class="mb-2" style="font-size: 2rem;"><i class="fas fa-inbox"></i></div>
                            Belum ada data setor susu tercatat.
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
<style>
    .action-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        text-decoration: none;
        border: none;
        font-size: 0.9rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .action-btn.detail {
        background: #e0f2fe;
        color: #0369a1;
    }
    .action-btn.detail:hover {
        background: #0369a1;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(3, 105, 161, 0.2);
    }

    .action-btn.edit {
        background: #fef9c3;
        color: #a16207;
    }
    .action-btn.edit:hover {
        background: #a16207;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(161, 98, 7, 0.2);
    }

    .action-btn.delete {
        background: #fee2e2;
        color: #b91c1c;
    }
    .action-btn.delete:hover {
        background: #b91c1c;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(185, 28, 28, 0.2);
    }
</style>
@endsection
