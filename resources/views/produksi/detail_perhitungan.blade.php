@extends('layouts.app')

@section('title', 'Detail Perhitungan Bagi Hasil - SIPERAH')

@section('content')
<h1>Detail Perhitungan Bagi Hasil</h1>

<div class="grid">
    <!-- Produksi Detail -->
    <div class="card">
        <h2 style="margin-bottom: 16px;">Data Produksi</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 8px 0; color: var(--text-light);">Tanggal</td>
                <td style="padding: 8px 0; font-weight: 500;">{{ $produksi->tanggal->format('d/m/Y') }}</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 8px 0; color: var(--text-light);">Jumlah Susu</td>
                <td style="padding: 8px 0; font-weight: 500;">{{ number_format($produksi->jumlah_susu_liter, 2) }} Liter</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 8px 0; color: var(--text-light);">Biaya Pakan</td>
                <td style="padding: 8px 0; font-weight: 500;">Rp {{ number_format($produksi->biaya_pakan, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 8px 0; color: var(--text-light);">Biaya Tenaga</td>
                <td style="padding: 8px 0; font-weight: 500;">Rp {{ number_format($produksi->biaya_tenaga, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 8px 0; color: var(--text-light);">Biaya Operasional</td>
                <td style="padding: 8px 0; font-weight: 500;">Rp {{ number_format($produksi->biaya_operasional, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: var(--text-light);">Total Biaya</td>
                <td style="padding: 8px 0; font-weight: 600; color: var(--danger);">Rp {{ number_format($produksi->total_biaya, 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- Bagi Hasil Detail -->
    @if($bagiHasil)
    <div class="card">
        <h2 style="margin-bottom: 16px;">Perhitungan Bagi Hasil</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 8px 0; color: var(--text-light);">Total Pendapatan</td>
                <td style="padding: 8px 0; font-weight: 500;">Rp {{ number_format($bagiHasil->total_pendapatan, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 8px 0; color: var(--text-light);">Persentase Pemilik</td>
                <td style="padding: 8px 0; font-weight: 500;">{{ $bagiHasil->persentase_pemilik }}%</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 8px 0; color: var(--text-light);">Hasil Pemilik</td>
                <td style="padding: 8px 0; font-weight: 600; color: var(--success);">Rp {{ number_format($bagiHasil->hasil_pemilik, 0, ',', '.') }}</td>
            </tr>
            <tr style="border-bottom: 1px solid var(--border);">
                <td style="padding: 8px 0; color: var(--text-light);">Persentase Pengelola</td>
                <td style="padding: 8px 0; font-weight: 500;">{{ $bagiHasil->persentase_pengelola }}%</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: var(--text-light);">Hasil Pengelola</td>
                <td style="padding: 8px 0; font-weight: 600; color: var(--primary);">Rp {{ number_format($bagiHasil->hasil_pengelola, 0, ',', '.') }}</td>
            </tr>
        </table>
        <div style="margin-top: 16px; padding-top: 16px; border-top: 1px solid var(--border);">
            <p style="color: var(--text-light); font-size: 12px;">Status: <span style="color: var(--warning);">{{ ucfirst($bagiHasil->status) }}</span></p>
        </div>
    </div>
    @else
    <div class="card">
        <p style="color: var(--text-light); text-align: center;">Bagi hasil belum dihitung</p>
    </div>
    @endif
</div>

<div style="margin-top: 24px;">
    <a href="/produksi/list" class="btn btn-secondary">← Kembali</a>
</div>

@endsection