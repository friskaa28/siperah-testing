@extends('layouts.app')

@section('title', 'Riwayat Produksi - SIPERAH')

@section('content')
<h1>Riwayat Produksi</h1>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jumlah Susu (L)</th>
                <th>Biaya Pakan</th>
                <th>Biaya Tenaga</th>
                <th>Biaya Operasional</th>
                <th>Total Biaya</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($produksi as $p)
                <tr>
                    <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                    <td>{{ number_format($p->jumlah_susu_liter, 2) }}</td>
                    <td>Rp {{ number_format($p->biaya_pakan, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($p->biaya_tenaga, 0, ',', '.') }}</td>
                    <td>Rp {{ number_format($p->biaya_operasional, 0, ',', '.') }}</td>
                    <td><strong>Rp {{ number_format($p->total_biaya, 0, ',', '.') }}</strong></td>
                    <td>
                        <a href="{{ route('produksi.detail', $p->idproduksi) }}" class="btn btn-primary" style="font-size: 12px; padding: 6px 12px;">
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-light);">Belum ada data produksi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($produksi->hasPages())
        <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--border);">
            {{ $produksi->links() }}
        </div>
    @endif
</div>

@endsection
