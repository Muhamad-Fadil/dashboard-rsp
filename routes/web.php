<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DirekturController;
use App\Http\Controllers\DivisiController;
use Illuminate\Support\Facades\Route;

// ---- Login ----
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // ---- Admin: kelola semuanya ----
    Route::get('/admin', [AdminController::class, 'index'])
        ->name('admin.dashboard')
        ->middleware('role:admin');

    // ---- Direktur: bisa lihat semua divisi ----
    Route::get('/direktur', [DirekturController::class, 'index'])
        ->name('direktur.dashboard')
        ->middleware('role:direktur');

    // ---- Dashboard per divisi: direktur (semua), manajer & operator (cuma divisinya sendiri) ----
    Route::get('/divisi/{division:slug}', [DivisiController::class, 'show'])
        ->name('divisi.dashboard')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/kunjungan-harian', [DivisiController::class, 'kunjunganHarian'])
        ->name('divisi.kunjungan-harian')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);
});

// arahkan halaman utama ke /login
Route::get('/', fn () => redirect()->route('login'));
