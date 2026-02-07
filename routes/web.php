<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\DistribusiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\GajiController;
use App\Http\Controllers\PeternakLaporanController;
use App\Http\Controllers\LogistikController;
use App\Http\Controllers\HargaSusuController;
use App\Http\Controllers\KasbonController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PanduanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BantuanController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root landing page
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isPeternak()) {
            return redirect('/dashboard-peternak');
        } else {
            return redirect('/dashboard-pengelola');
        }
    }
    return view('landing');
});

// =====================================================================
// Authentication Routes
// =====================================================================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    // Registration
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

    // Password Reset
    Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// =====================================================================
// Peternak Routes (Protected with peternak.only middleware)
// =====================================================================
Route::middleware(['auth', 'peternak.only'])->group(function () {
    // Dashboard
    Route::get('/dashboard-peternak', [DashboardController::class, 'peternakDashboard'])->name('dashboard.peternak');

    // Produksi History for Peternak
    Route::get('/riwayat-produksi', [ProduksiController::class, 'listPeternak'])->name('produksi.riwayat');

    // Laporan Pendapatan (E-Statement)
    Route::get('/laporan', [PeternakLaporanController::class, 'index'])->name('peternak.laporan.index');
    Route::get('/laporan/pdf', [PeternakLaporanController::class, 'exportPdf'])->name('peternak.laporan.pdf');
});

// =====================================================================
// Shared Restricted Routes (Admin, Pengelola, Peternak)
// =====================================================================
Route::middleware(['auth'])->group(function () {


    // Shared Notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{idnotif}/mark-read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.markAsRead');
    Route::post('/notifikasi/mark-all-read', [NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.markAllAsRead');
    Route::get('/notifikasi/unread-count', [NotifikasiController::class, 'countUnread'])->name('notifikasi.countUnread');
    Route::get('/notifikasi/unread-count', [NotifikasiController::class, 'countUnread'])->name('notifikasi.countUnread');
    Route::delete('/notifikasi/{idnotif}', [NotifikasiController::class, 'delete'])->name('notifikasi.delete');

    // Panduan (Shared)
    Route::get('/panduan', [PanduanController::class, 'index'])->name('panduan.index');
});

// =====================================================================
// Pengelola/Admin Routes (Protected with pengelola.admin.only middleware)
// =====================================================================
Route::middleware(['auth', 'pengelola.admin.only'])->group(function () {
    // Dashboard
    Route::get('/dashboard-pengelola', [DashboardController::class, 'pengelolaDashboard'])->name('dashboard.pengelola');



    // Produksi Routes (Input) - Admin/Pengelola
    Route::get('/produksi/print', [ProduksiController::class, 'printRiwayat'])->name('produksi.print');
    Route::get('/produksi', [ProduksiController::class, 'listPeternak'])->name('produksi.index');
    Route::get('/produksi/input', [ProduksiController::class, 'create'])->name('produksi.create');
    Route::post('/produksi/store', [ProduksiController::class, 'store'])->name('produksi.store');
    Route::get('/produksi/{idproduksi}', [ProduksiController::class, 'detailPerhitungan'])->name('produksi.detail');
    Route::get('/produksi/{idproduksi}/edit', [ProduksiController::class, 'edit'])->name('produksi.edit');
    Route::put('/produksi/{idproduksi}', [ProduksiController::class, 'update'])->name('produksi.update');
    Route::delete('/produksi/{idproduksi}', [ProduksiController::class, 'destroy'])->name('produksi.destroy');
    Route::get('/produksi/template/download', [ProduksiController::class, 'downloadTemplate'])->name('produksi.template');
    Route::post('/produksi/import', [ProduksiController::class, 'import'])->name('produksi.import');



    // Settings
    Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');



    // Gaji (Salary Slip) Management
    Route::get('/gaji', [GajiController::class, 'index'])->name('gaji.index');
    Route::post('/gaji/generate', [GajiController::class, 'generate'])->name('gaji.generate');
    Route::get('/gaji/{idslip}/edit', [GajiController::class, 'edit'])->name('gaji.edit');
    Route::put('/gaji/{idslip}', [GajiController::class, 'update'])->name('gaji.update');
    Route::get('/gaji/{idslip}/print', [GajiController::class, 'print'])->name('gaji.print');
    Route::post('/gaji/import', [GajiController::class, 'import'])->name('gaji.import');
    Route::post('/gaji/confirm-import', [GajiController::class, 'confirmImport'])->name('gaji.confirm-import');
    Route::get('/gaji/template', [GajiController::class, 'downloadTemplate'])->name('gaji.template');
    Route::post('/gaji/{idslip}/sign', [GajiController::class, 'sign'])->name('gaji.sign');

    // Clean Report for Pusat
    Route::get('/laporan/pusat', [LaporanController::class, 'pusatReport'])->name('laporan.pusat');
    Route::get('/laporan/rekap-harian', [LaporanController::class, 'rekapHarian'])->name('laporan.rekap_harian');

    // Peternak Management
    Route::get('/peternak', [App\Http\Controllers\PeternakController::class, 'index'])->name('peternak.index');
    Route::post('/peternak', [App\Http\Controllers\PeternakController::class, 'store'])->name('peternak.store');
    Route::post('/peternak/{id}/update-status', [App\Http\Controllers\PeternakController::class, 'updateStatus'])->name('peternak.update_status');
    Route::delete('/peternak/{id}', [App\Http\Controllers\PeternakController::class, 'destroy'])->name('peternak.destroy');

    // Katalog Logistik CRUD
    Route::get('/logistik', [LogistikController::class, 'index'])->name('logistik.index');
    Route::post('/logistik', [LogistikController::class, 'store'])->name('logistik.store');
    Route::put('/logistik/{id}', [LogistikController::class, 'update'])->name('logistik.update');
    Route::delete('/logistik/{id}', [LogistikController::class, 'destroy'])->name('logistik.destroy');

    // Harga Susu History
    Route::get('/harga-susu', [HargaSusuController::class, 'index'])->name('harga_susu.index');
    Route::post('/harga-susu', [HargaSusuController::class, 'store'])->name('harga_susu.store');
    Route::delete('/harga-susu/{id}', [HargaSusuController::class, 'destroy'])->name('harga_susu.destroy');

    // Kasbon Management
    Route::get('/kasbon', [KasbonController::class, 'index'])->name('kasbon.index');
    Route::post('/kasbon', [KasbonController::class, 'store'])->name('kasbon.store');
    Route::delete('/kasbon/{id}', [KasbonController::class, 'destroy'])->name('kasbon.destroy');

    // Laporan Data (Konsolidasi)
    Route::get('/laporan/data', [LaporanController::class, 'laporanData'])->name('laporan.data');

    // Laporan Sub-Penampung
    Route::get('/laporan/sub-penampung', [LaporanController::class, 'subPenampungReport'])->name('laporan.sub_penampung');
    
    // Monitoring Harian (Renamed to Laporan Harian in UI)
    Route::get('/monitoring-harian', [\App\Http\Controllers\RekapController::class, 'index'])->name('monitoring.index');

    // Pengumuman
    Route::post('/pengumuman', [PengumumanController::class, 'broadcast'])->name('pengumuman.broadcast');

    // Activity Log
    Route::get('/activity-log', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-log.index');
    Route::get('/activity-log/{id}', [App\Http\Controllers\ActivityLogController::class, 'show'])->name('activity-log.show');
    Route::post('/activity-log/clear', [App\Http\Controllers\ActivityLogController::class, 'clear'])->name('activity-log.clear');
    Route::post('/activity-log/{id}/undo', [App\Http\Controllers\ActivityLogController::class, 'undoAction'])->name('activity-log.undo');

    // Kelola Pengguna
    Route::get('/settings/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/settings/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/settings/users/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/settings/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // Sistem Bantuan
    Route::get('/system/bantuan', [BantuanController::class, 'index'])->name('bantuan.index');
});
