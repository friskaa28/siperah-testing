@extends('layouts.app')

@section('title', 'Detail Distribusi - SIPERAH')

@section('content')
<h1>Detail Distribusi</h1>

<div class="card">
    <table style="width: 100%; border-collapse: collapse;">
        <tr style="border-bottom: 1px solid var(--border);">
            <td style="padding: 12px 0; color: var(--text-light); width: 30%;">ID Distribusi</td>
            <td style="padding: 12px 0; font-weight: 500;">{{ $distribusi->iddistribusi }}</td>
        </tr>
        <tr style="border-bottom: 1px solid var(--border);">
            <td style="padding: 12px 0; color: var(--text-light);">Tanggal Kirim</td>
            <td style="padding: 12px 0; font-weight: 500;">{{ $distribusi->tanggal_kirim->format('d/m/Y') }}</td>
        </tr>
        <tr style="border-bottom: 1px solid var(--border);">
            <td style="padding: 12px 0; color: var(--text-light);">Tujuan</td>
            <td style="padding: 12px 0; font-weight: 500;">{{ $distribusi->tujuan }}</td>
        </tr>
        <tr style="border-bottom: 1px solid var(--border);">
            <td style="padding: 12px 0; color: var(--text-light);">Volume</td>
            <td style="padding: 12px 0; font-weight: 500;">{{ number_format($distribusi->volume, 2) }} Liter</td>
        </tr>
        <tr style="border-bottom: 1px solid var(--border);">
            <td style="padding: 12px 0; color: var(--text-light);">Harga Per Liter</td>
            <td style="padding: 12px 0; font-weight: 500;">Rp {{ number_format($distribusi->harga_per_liter, 0, ',', '.') }}</td>
        </tr>
        <tr style="border-bottom: 1px solid var(--border);">
            <td style="padding: 12px 0; color: var(--text-light);">Total Penjualan</td>
            <td style="padding: 12px 0; font-weight: 600; color: var(--success);">Rp {{ number_format($distribusi->total_penjualan, 0, ',', '.') }}</td>
        </tr>
        <tr style="border-bottom: 1px solid var(--border);">
            <td style="padding: 12px 0; color: var(--text-light);">Status</td>
            <td style="padding: 12px 0; font-weight: 500;">
                <span style="padding: 4px 8px; border-radius: 4px;
                    @if($distribusi->status === 'pending') background: #FEF3C7; color: #92400E;
                    @elseif($distribusi->status === 'terkirim') background: #DBEAFE; color: #0C4A6E;
                    @elseif($distribusi->status === 'diterima') background: #DCFCE7; color: #166534;
                    @else background: #FEE2E2; color: #991B1B;
                    @endif
                ">
                    {{ ucfirst($distribusi->status) }}
                </span>
            </td>
        </tr>
        <tr>
            <td style="padding: 12px 0; color: var(--text-light);">Catatan</td>
            <td style="padding: 12px 0;">{{ $distribusi->catatan ?? '-' }}</td>
        </tr>
    </table>
</div>

<div style="margin-top: 24px;">
    <a href="/distribusi/recap" class="btn btn-secondary">← Kembali</a>
</div>

@endsection
