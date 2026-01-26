@extends('layouts.app')

@section('title', 'Bantuan & Panduan - SIPERAH')

@section('content')
<div class="row mb-4 align-items-center">
    <div class="col-md-12">
        <h1 class="h3 mb-0"><i class="fas fa-question-circle"></i> Pusat Bantuan & Panduan</h1>
        <p class="text-muted">Pelajari cara menggunakan fitur SIPERAH dengan mudah</p>
    </div>
</div>

<div class="row">
    <!-- Panduan Cepat -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-body">
                <div class="text-primary mb-3" style="font-size: 2rem;"><i class="fas fa-file-import"></i></div>
                <h5 class="fw-bold">Input Setor Susu</h5>
                <p class="text-muted small">Cara mencatat setoran susu harian peternak:</p>
                <ul class="small ps-3 text-muted">
                    <li>Pilih menu <strong>Input Setor Susu</strong>.</li>
                    <li>Pilih Peternak yang menyetor.</li>
                    <li>Isi jumlah liter dan pilih waktu (Pagi/Sore).</li>
                    <li>Klik simpan untuk mencatat data.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Data Peternak -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-body">
                <div class="text-success mb-3" style="font-size: 2rem;"><i class="fas fa-address-book"></i></div>
                <h5 class="fw-bold">Manajemen Peternak</h5>
                <p class="text-muted small">Kelola data mitra dan kategori:</p>
                <ul class="small ps-3 text-muted">
                    <li>Menu <strong>Data Peternak</strong> untuk daftar mitra.</li>
                    <li>Gunakan tombol <strong>Edit</strong> untuk mengubah kategori (Peternak/Sub-Penampung).</li>
                    <li>Tambah peternak baru dengan tombol <strong>Tambah</strong>.</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Laporan & Cetak -->
    <div class="col-md-4 mb-4">
        <div class="card shadow-sm border-0 h-100" style="border-radius: 12px;">
            <div class="card-body">
                <div class="text-warning mb-3" style="font-size: 2rem;"><i class="fas fa-print"></i></div>
                <h5 class="fw-bold">Laporan & Cetak</h5>
                <p class="text-muted small">Cara mencetak laporan bulanan:</p>
                <ul class="small ps-3 text-muted">
                    <li>Menu <strong>Laporan Harian</strong>.</li>
                    <li>Klik tombol <strong>Rekap Bulanan</strong>.</li>
                    <li>Pilih bulan/tahun lalu klik <strong>Cetak</strong>.</li>
                    <li>Laporan Pusat juga dapat di-export ke Excel.</li>
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
            <iframe src="https://heyzine.com/flip-book/88e632ae68.html" allowfullscreen class="border-0 w-100 h-100"></iframe>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mt-2" style="border-radius: 12px;">
    <div class="card-body p-4">
        <h5 class="fw-bold mb-4"><i class="fas fa-info-circle"></i> Pertanyaan sering diajukan (FAQ)</h5>
        
        <div class="mb-3">
            <h6 class="fw-bold">1. Apa perbedaan Peternak dan Sub-Penampung?</h6>
            <p class="text-muted small"><strong>Peternak</strong> adalah mitra perorangan, sedangkan <strong>Sub-Penampung</strong> adalah mitra yang mengumpulkan susu dari beberapa peternak lain. Kategori ini menentukan pengelompokan di Laporan Pusat (POS).</p>
        </div>
        <hr>
        <div class="mb-3">
            <h6 class="fw-bold">2. Bagaimana cara mengganti password?</h6>
            <p class="text-muted small">Admin dapat mengganti password melalui menu <strong>Pengaturan Fitur</strong> > <strong>Kelola Pengguna</strong>. Cari nama akun dan klik tombol Edit.</p>
        </div>
        <hr>
        <div class="mb-0">
            <h6 class="fw-bold">3. Data setoran salah input, apa yang harus dilakukan?</h6>
            <p class="text-muted small">Buka menu <strong>Riwayat Setor Susu</strong>, cari data yang salah berdasarkan tanggal, lalu klik <strong>Lihat Detail</strong> untuk melakukan perubahan atau penghapusan data.</p>
        </div>
    </div>
</div>

<div class="text-center mt-5 mb-4">
    <p class="text-muted small">Butuh bantuan teknis lebih lanjut? Hubungi Tim IT SIPERAH.</p>
</div>
@endsection
