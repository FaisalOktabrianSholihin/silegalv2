<?php

use App\Http\Controllers\{AuthController, DashboardController, DokumenController, DivisiController,
    KaryawanController, UserController, RoleController, LogAktivitasController, JenisDokumenController};
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:8,1')->name('login.proses');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // ---------- Master Data ----------
    Route::middleware('izin:master.kelola')->prefix('master')->name('master.')->group(function () {
        // Divisi & Karyawan dibaca dari db_indukk (read-only)
        Route::get('divisi', [DivisiController::class, 'index'])->name('divisi.index');
        Route::get('karyawan', [KaryawanController::class, 'index'])->name('karyawan.index');
    });
    Route::middleware('izin:user.kelola')->prefix('master')->group(function () {
        Route::resource('users', UserController::class)->except(['show']);
    });

    Route::middleware('izin:jenis.kelola')->prefix('master')->group(function () {
        Route::resource('jenis-dokumen', JenisDokumenController::class)->parameters(['jenis-dokumen' => 'jenis'])->except(['show']);
    });

    // ---------- Roles & Log ----------
    Route::middleware('izin:role.kelola')->resource('roles', RoleController::class)->except(['show']);
    Route::middleware('izin:log.lihat')->get('/log-aktivitas', [LogAktivitasController::class, 'index'])->name('log.index');

    // ---------- Dokumen ----------
    Route::post('/dokumen-preview/{dokumen}', [DokumenController::class, 'catatPreview'])->name('dokumen.preview-log');

    Route::prefix('dokumen/{jenis}')->name('dokumen.')
        ->where(['jenis' => '[a-z0-9]+(?:-[a-z0-9]+)*']) // slug valid; jenis yang tidak ada -> 404 di controller
        ->group(function () {
            Route::get('/', [DokumenController::class, 'index'])->name('index');
            Route::post('/rangkuman-ai', [DokumenController::class, 'rangkumanAi'])->middleware('throttle:10,1')->name('rangkuman-ai');
            Route::get('/tambah', [DokumenController::class, 'create'])->middleware('izin:dokumen.tambah')->name('create');
            Route::post('/', [DokumenController::class, 'store'])->middleware('izin:dokumen.tambah')->name('store');
            Route::get('/{dokumen}', [DokumenController::class, 'show'])->whereNumber('dokumen')->name('show');
            Route::get('/{dokumen}/ubah', [DokumenController::class, 'edit'])->middleware('izin:dokumen.ubah')->whereNumber('dokumen')->name('edit');
            Route::put('/{dokumen}', [DokumenController::class, 'update'])->middleware('izin:dokumen.ubah')->whereNumber('dokumen')->name('update');
            Route::delete('/{dokumen}', [DokumenController::class, 'destroy'])->middleware('izin:dokumen.hapus')->whereNumber('dokumen')->name('destroy');
        });
});
