@extends('layouts.app')

@section('title', $title)

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-12">
        <h1 class="h3 mb-0"><i class="fas fa-question-circle"></i> {{ $title }}</h1>
        <p class="text-muted">Pelajari cara menggunakan fitur SIPERAH dengan mudah</p>
    </div>
</div>

<div class="row">
    <!-- Cek Saldo & Gaji -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-body">
                <div class="text-primary mb-3" style="font-size: 2rem;"><i class="fas fa-wallet"></i></div>
                <h5 class="fw-bold">Cek Saldo & Gaji</h5>
                <p class="text-muted small">Cara melihat estimasi pendapatan:</p>
                <ul class="small ps-3 text-muted">
                    <li>Buka halaman <strong>Dashboard</strong>.</li>
                    <li>Lihat kartu <strong>Estimasi Gaji Bersih</strong>.</li>
                    <li>Angka tersebut adalah estimasi (Liter x Harga) dikurangi Kasbon.</li>
                    <li>Nilai final akan tercetak di Slip Gaji Bulanan.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Riwayat Setoran -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-body">
                <div class="text-success mb-3" style="font-size: 2rem;"><i class="fas fa-history"></i></div>
                <h5 class="fw-bold">Riwayat Setoran</h5>
                <p class="text-muted small">Memantau setoran harian Anda:</p>
                <ul class="small ps-3 text-muted">
                    <li>Pilih menu <strong>Riwayat Produksi</strong>.</li>
                    <li>Gunakan filter tanggal untuk melihat periode tertentu.</li>
                    <li>Pastikan jumlah liter Pagi & Sore sudah sesuai.</li>
                    <li>Jika ada kesalahan, segera lapor ke petugas/admin.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Laporan & Cetak -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-body">
                <div class="text-warning mb-3" style="font-size: 2rem;"><i class="fas fa-file-invoice-dollar"></i></div>
                <h5 class="fw-bold">E-Statement (Laporan)</h5>
                <p class="text-muted small">Mendapatkan laporan penghasilan:</p>
                <ul class="small ps-3 text-muted">
                    <li>Masuk ke menu <strong>Laporan Pendapatan</strong>.</li>
                    <li>Lihat ringkasan total liter dan potongan kasbon.</li>
                    <li>Klik tombol <strong>Download PDF</strong> untuk menyimpan bukti.</li>
                    <li>Dokumen dilengkapi QR Code validasi digital.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Buku Panduan Section -->
<div class="card shadow-sm border-0 mt-4 mb-4" style="border-radius: 12px; overflow: hidden;">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0 text-primary"><i class="fas fa-book-open me-2"></i> Buku Panduan Digital (Flipping Book)</h5>
    </div>
    <div class="card-body p-0">
        <div class="ratio ratio-16x9" style="min-height: 600px;">
            <iframe src="https://heyzine.com/flip-book/e6622afd64.html" allowfullscreen class="border-0 w-100 h-100"></iframe>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mt-2" style="border-radius: 12px;">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-4"><i class="fas fa-info-circle"></i> Pertanyaan sering diajukan (FAQ)</h5>
        
        <div class="mb-3">
            <h6 class="fw-bold">1. Kapan periode gajian dihitung?</h6>
            <p class="text-muted small">Periode gaji dihitung mulai tanggal <strong>14 bulan lalu</strong> sampai dengan tanggal <strong>13 bulan ini</strong>.</p>
        </div>
        <hr>
        <div class="mb-3">
            <h6 class="fw-bold">2. Bagaimana jika saya lupa password?</h6>
            <p class="text-muted small">Silakan hubungi Admin atau Pengelola Koperasi untuk melakukan reset password akun Anda.</p>
        </div>
        <hr>
        <div class="mb-0">
            <h6 class="fw-bold">3. Apakah saya bisa mengedit data setoran sendiri?</h6>
            <p class="text-muted small">Tidak. Demi keamanan data, hanya Admin/Pengelola yang berhak mengubah data setoran. Peternak hanya dapat melihat (Read-Only).</p>
        </div>
    </div>
</div>

<div class="text-center mt-5 mb-4">
    <p class="text-muted small">Butuh bantuan teknis lebih lanjut? Hubungi Tim IT SIPERAH.</p>
</div>
@endsection
