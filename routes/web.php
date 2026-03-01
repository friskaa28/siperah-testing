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
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\SetorSusuController;
use App\Http\Controllers\RekapController;
use App\Http\Controllers\PeternakController;
use App\Http\Controllers\ActivityLogController;

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
        } elseif ($user->isAnalytics()) {
            return redirect('/analytics/dashboard');
        } else {
            return redirect('/dashboard-pengelola');
        }
    }
    return view('landing');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);
    Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// =====================================================================
// Authenticated Routes with Session Tracking
// =====================================================================
Route::middleware(['auth', 'track_session'])->group(function () {
    
    // Shared Restricted Routes
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{idnotif}/mark-read', [NotifikasiController::class, 'markAsRead'])->name('notifikasi.markAsRead');
    Route::post('/notifikasi/mark-all-read', [NotifikasiController::class, 'markAllAsRead'])->name('notifikasi.markAllAsRead');
    Route::get('/notifikasi/unread-count', [NotifikasiController::class, 'countUnread'])->name('notifikasi.countUnread');
    Route::delete('/notifikasi/{idnotif}', [NotifikasiController::class, 'delete'])->name('notifikasi.delete');
    Route::get('/panduan', [PanduanController::class, 'index'])->name('panduan.index');

    // Peternak Routes
    Route::middleware(['peternak.only'])->group(function () {
        Route::get('/dashboard-peternak', [DashboardController::class, 'peternakDashboard'])->name('dashboard.peternak');
        Route::get('/riwayat-produksi', [ProduksiController::class, 'listPeternak'])->name('produksi.riwayat');
        Route::get('/laporan', [PeternakLaporanController::class, 'index'])->name('peternak.laporan.index');
        Route::get('/laporan/pdf', [PeternakLaporanController::class, 'exportPdf'])->name('peternak.laporan.pdf');
        Route::get('/riwayat-setoran', [SetorSusuController::class, 'riwayat'])->name('riwayat.setoran');
    });

    // Pengelola/Admin Routes
    Route::middleware(['pengelola.admin.only'])->group(function () {
        Route::get('/dashboard-pengelola', [DashboardController::class, 'pengelolaDashboard'])->name('dashboard.pengelola');
        Route::get('/produksi', [ProduksiController::class, 'listPeternak'])->name('produksi.index');
        Route::get('/produksi/input', [ProduksiController::class, 'create'])->name('produksi.create');
        Route::post('/produksi/store', [ProduksiController::class, 'store'])->name('produksi.store');
        Route::get('/produksi/print', [ProduksiController::class, 'printRiwayat'])->name('produksi.print');
        Route::get('/produksi/{idproduksi}', [ProduksiController::class, 'detailPerhitungan'])->name('produksi.detail');
        Route::get('/produksi/{idproduksi}/edit', [ProduksiController::class, 'edit'])->name('produksi.edit');
        Route::put('/produksi/{idproduksi}', [ProduksiController::class, 'update'])->name('produksi.update');
        Route::delete('/produksi/{idproduksi}', [ProduksiController::class, 'destroy'])->name('produksi.destroy');
        Route::get('/produksi/template/download', [ProduksiController::class, 'downloadTemplate'])->name('produksi.template');
        Route::post('/produksi/import', [ProduksiController::class, 'import'])->name('produksi.import');

        Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');

        Route::get('/gaji', [GajiController::class, 'index'])->name('gaji.index');
        Route::post('/gaji/generate', [GajiController::class, 'generate'])->name('gaji.generate');
        Route::get('/gaji/{idslip}/edit', [GajiController::class, 'edit'])->name('gaji.edit');
        Route::put('/gaji/{idslip}', [GajiController::class, 'update'])->name('gaji.update');
        Route::get('/gaji/{idslip}/print', [GajiController::class, 'print'])->name('gaji.print');
        Route::post('/gaji/import', [GajiController::class, 'import'])->name('gaji.import');
        Route::post('/gaji/confirm-import', [GajiController::class, 'confirmImport'])->name('gaji.confirm-import');
        Route::get('/gaji/template', [GajiController::class, 'downloadTemplate'])->name('gaji.template');
        Route::post('/gaji/{idslip}/sign', [GajiController::class, 'sign'])->name('gaji.sign');

        Route::get('/laporan/pusat', [LaporanController::class, 'pusatReport'])->name('laporan.pusat');
        Route::get('/laporan/rekap-harian', [LaporanController::class, 'rekapHarian'])->name('laporan.rekap_harian');
        Route::get('/laporan/data', [LaporanController::class, 'laporanData'])->name('laporan.data');
        Route::get('/laporan/sub-penampung', [LaporanController::class, 'subPenampungReport'])->name('laporan.sub_penampung');

        Route::get('/peternak', [PeternakController::class, 'index'])->name('peternak.index');
        Route::post('/peternak', [PeternakController::class, 'store'])->name('peternak.store');
        Route::post('/peternak/{id}/update-status', [PeternakController::class, 'updateStatus'])->name('peternak.update_status');
        Route::delete('/peternak/{id}', [PeternakController::class, 'destroy'])->name('peternak.destroy');

        Route::get('/logistik', [LogistikController::class, 'index'])->name('logistik.index');
        Route::post('/logistik', [LogistikController::class, 'store'])->name('logistik.store');
        Route::put('/logistik/{id}', [LogistikController::class, 'update'])->name('logistik.update');
        Route::delete('/logistik/{id}', [LogistikController::class, 'destroy'])->name('logistik.destroy');

        Route::get('/harga-susu', [HargaSusuController::class, 'index'])->name('harga_susu.index');
        Route::post('/harga-susu', [HargaSusuController::class, 'store'])->name('harga_susu.store');
        Route::delete('/harga-susu/{id}', [HargaSusuController::class, 'destroy'])->name('harga_susu.destroy');

        Route::get('/kasbon', [KasbonController::class, 'index'])->name('kasbon.index');
        Route::post('/kasbon', [KasbonController::class, 'store'])->name('kasbon.store');
        Route::get('/kasbon/{id}/edit', [KasbonController::class, 'edit'])->name('kasbon.edit');
        Route::put('/kasbon/{id}', [KasbonController::class, 'update'])->name('kasbon.update');
        Route::delete('/kasbon/{id}', [KasbonController::class, 'destroy'])->name('kasbon.destroy');

        Route::get('/monitoring-harian', [RekapController::class, 'index'])->name('monitoring.index');
        Route::post('/pengumuman', [PengumumanController::class, 'broadcast'])->name('pengumuman.broadcast');

        Route::get('/activity-log', [ActivityLogController::class, 'index'])->name('activity-log.index');
        Route::get('/activity-log/{id}', [ActivityLogController::class, 'show'])->name('activity-log.show');
        Route::post('/activity-log/clear', [ActivityLogController::class, 'clear'])->name('activity-log.clear');
        Route::post('/activity-log/{id}/undo', [ActivityLogController::class, 'undoAction'])->name('activity-log.undo');

        Route::get('/settings/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/settings/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/settings/users/{id}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/settings/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
        Route::get('/system/bantuan', [BantuanController::class, 'index'])->name('bantuan.index');

        // KPI Management (within Pengelola/Admin group)
        Route::get('/analytics/kpi', [AnalyticsController::class, 'kpiIndex'])->name('analytics.kpi');
        Route::post('/analytics/profit', [AnalyticsController::class, 'storeProfit'])->name('analytics.profit.store');
        Route::delete('/analytics/profit/{id}', [AnalyticsController::class, 'destroyProfit'])->name('analytics.profit.destroy');
        Route::post('/analytics/errors', [AnalyticsController::class, 'storeError'])->name('analytics.error.store');
        Route::put('/analytics/errors/{id}', [AnalyticsController::class, 'updateError'])->name('analytics.error.update');
        Route::delete('/analytics/errors/{id}', [AnalyticsController::class, 'destroyError'])->name('analytics.error.destroy');
    });

    // Analytics Only Routes
    Route::middleware(['analytics.only'])->prefix('analytics')->group(function () {
        Route::get('/dashboard', [AnalyticsController::class, 'dashboard'])->name('analytics.dashboard');
        Route::get('/usage', [AnalyticsController::class, 'usage'])->name('analytics.usage');
        Route::get('/errors', [AnalyticsController::class, 'errors'])->name('analytics.errors');
        Route::get('/profit', [AnalyticsController::class, 'profit'])->name('analytics.profit');
    });
});
