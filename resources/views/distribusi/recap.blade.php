@extends('layouts.app')

@section('title', 'Recap Distribusi - SIPERAH')

@section('content')
<h1>Recap Distribusi</h1>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Tanggal Kirim</th>
                <th>Tujuan</th>
                <th>Volume (L)</th>
                <th>Harga/L</th>
                <th>Total Penjualan</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($distribusi as $d)
                <tr>
                    <td>{{ $d->tanggal_kirim->format('d/m/Y') }}</td>
                    <td>{{ $d->tujuan }}</td>
                    <td>{{ number_format($d->volume, 2) }}</td>
                    <td>Rp {{ number_format($d->harga_per_liter, 0, ',', '.') }}</td>
                    <td><strong>Rp {{ number_format($d->total_penjualan, 0, ',', '.') }}</strong></td>
                    <td>
                        <span style="padding: 4px 8px; border-radius: 4px; font-size: 12px; 
                            @if($d->status === 'pending') background: #FEF3C7; color: #92400E;
                            @elseif($d->status === 'terkirim') background: #DBEAFE; color: #0C4A6E;
                            @elseif($d->status === 'diterima') background: #DCFCE7; color: #166534;
                            @else background: #FEE2E2; color: #991B1B;
                            @endif
                        ">
                            {{ ucfirst($d->status) }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('distribusi.show', $d->iddistribusi) }}" class="btn btn-primary" style="font-size: 12px; padding: 6px 12px;">
                            Detail
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; color: var(--text-light);">Belum ada data distribusi</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    @if($distribusi->hasPages())
        <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid var(--border);">
            {{ $distribusi->links() }}
        </div>
    @endif
</div>

@endsection