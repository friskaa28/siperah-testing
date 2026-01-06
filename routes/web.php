<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProduksiController;
use App\Http\Controllers\DistribusiController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotifikasiController;
use App\Http\Controllers\GajiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Root redirect
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->isPeternak()) {
            return redirect('/dashboard-peternak');
        } else {
            return redirect('/dashboard-pengelola');
        }
    }
    return redirect('/login');
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
});

// =====================================================================
// Shared Restricted Routes (Admin, Pengelola, Peternak)
// =====================================================================
Route::middleware(['auth'])->group(function () {
    // Shared Distribusi
    Route::get('/distribusi/{iddistribusi}', [DistribusiController::class, 'show'])->name('distribusi.show');

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

    // Distribusi Management
    Route::put('/distribusi/{iddistribusi}/status', [DistribusiController::class, 'updateStatus'])->name('distribusi.updateStatus');

    // Produksi Routes (Input) - Admin/Pengelola
    Route::get('/produksi/input', [ProduksiController::class, 'create'])->name('produksi.create');
    Route::post('/produksi/store', [ProduksiController::class, 'store'])->name('produksi.store');

    // Manajemen Distribusi (Unified Input & Rekap) - Admin ONLY
    Route::middleware(['auth', 'admin.only'])->group(function () {
        Route::get('/manajemen-distribusi', [DistribusiController::class, 'index'])->name('distribusi.index');
        Route::get('/manajemen-distribusi/export', [DistribusiController::class, 'exportPdf'])->name('distribusi.export_pdf');
        Route::get('/manajemen-distribusi/template', [DistribusiController::class, 'downloadTemplate'])->name('distribusi.download_template');
        Route::post('/manajemen-distribusi/import', [DistribusiController::class, 'import'])->name('distribusi.import');
        
        // Form Storage (Re-using store logic but pointing redirect to index)
        Route::get('/distribusi/input', [DistribusiController::class, 'create'])->name('distribusi.create');
        Route::post('/distribusi/store', [DistribusiController::class, 'store'])->name('distribusi.store');
    });

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

    // Quick Update Peternak No
    Route::put('/peternak/{idpeternak}/update-no', function(\Illuminate\Http\Request $request, $id) {
        $peternak = \App\Models\Peternak::findOrFail($id);
        $peternak->update(['no_peternak' => $request->no_peternak]);
        return back()->with('success', 'Nomor peternak berhasil diatur!');
    })->name('peternak.update_no');
});
