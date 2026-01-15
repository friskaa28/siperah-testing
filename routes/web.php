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
    Route::delete('/notifikasi/{idnotif}', [NotifikasiController::class, 'delete'])->name('notifikasi.delete');
});

// =====================================================================
// Pengelola/Admin Routes (Protected with pengelola.admin.only middleware)
// =====================================================================
Route::middleware(['auth', 'pengelola.admin.only'])->group(function () {
    // Dashboard
    Route::get('/dashboard-pengelola', [DashboardController::class, 'pengelolaDashboard'])->name('dashboard.pengelola');



    // Produksi Routes (Input) - Admin/Pengelola
    Route::get('/produksi', [ProduksiController::class, 'listPeternak'])->name('produksi.index');
    Route::get('/produksi/input', [ProduksiController::class, 'create'])->name('produksi.create');
    Route::post('/produksi/store', [ProduksiController::class, 'store'])->name('produksi.store');
    Route::get('/produksi/{idproduksi}', [ProduksiController::class, 'detailPerhitungan'])->name('produksi.detail');
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
    Route::get('/gaji/template', [GajiController::class, 'downloadTemplate'])->name('gaji.template');
    Route::post('/gaji/{idslip}/sign', [GajiController::class, 'sign'])->name('gaji.sign');

    // Clean Report for Pusat
    Route::get('/laporan/pusat', [LaporanController::class, 'pusatReport'])->name('laporan.pusat');
    Route::get('/laporan/rekap-harian', [LaporanController::class, 'rekapHarian'])->name('laporan.rekap_harian');

    // Quick Update Peternak No
    Route::put('/peternak/{idpeternak}/update-no', function(\Illuminate\Http\Request $request, $id) {
        $peternak = \App\Models\Peternak::findOrFail($id);
        $peternak->update(['no_peternak' => $request->no_peternak]);
        return back()->with('success', 'Nomor peternak berhasil diatur!');
    })->name('peternak.update_no');

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

    // Pengumuman
    Route::post('/pengumuman', [PengumumanController::class, 'broadcast'])->name('pengumuman.broadcast');

    // Activity Log
    Route::get('/activity-log', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('activity-log.index');
    Route::get('/activity-log/{id}', [App\Http\Controllers\ActivityLogController::class, 'show'])->name('activity-log.show');
    Route::post('/activity-log/clear', [App\Http\Controllers\ActivityLogController::class, 'clear'])->name('activity-log.clear');
});
