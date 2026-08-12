<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DirekturController;
use App\Http\Controllers\DivisiController;
use App\Http\Controllers\PasienController;
use App\Http\Controllers\RawatInapController;
use App\Http\Controllers\KunjunganController;
use App\Http\Controllers\OperasiController;
use App\Http\Controllers\LaboratoriumController;
use App\Http\Controllers\RadiologiController;
use App\Http\Controllers\ResepController;
use App\Http\Controllers\ProduktivitasController;
use App\Http\Controllers\PendapatanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\KlaimBpjsController;
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
        ->name('divisi.layanan.kunjungan-harian')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/pasien', [PasienController::class, 'index'])
        ->name('divisi.layanan.pasien')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/pasien/pdf', [PasienController::class, 'exportPdf'])
        ->name('divisi.layanan.pasien.pdf')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/kunjungan', [KunjunganController::class, 'index'])
        ->name('divisi.layanan.kunjungan')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/kunjungan/pdf', [KunjunganController::class, 'exportPdf'])
        ->name('divisi.layanan.kunjungan.pdf')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/rawat-inap', [RawatInapController::class, 'index'])
        ->name('divisi.layanan.rawat-inap')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/rawat-inap/pdf', [RawatInapController::class, 'exportPdf'])
        ->name('divisi.layanan.rawat-inap.pdf')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/operasi', [OperasiController::class, 'index'])
        ->name('divisi.layanan.operasi')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/laboratorium', [LaboratoriumController::class, 'index'])
        ->name('divisi.layanan.laboratorium')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/radiologi', [RadiologiController::class, 'index'])
        ->name('divisi.layanan.radiologi')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/resep', [ResepController::class, 'index'])
        ->name('divisi.layanan.resep')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    // ---- Sub-menu SDM: Produktivitas ----
    Route::get('/divisi/{division:slug}/produktivitas', [ProduktivitasController::class, 'index'])
        ->name('divisi.sdm.produktivitas')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    // ---- Sub-menu Keuangan: Pendapatan, Pengeluaran, Cashflow ----
    Route::get('/divisi/{division:slug}/pendapatan', [PendapatanController::class, 'index'])
        ->name('divisi.keuangan.pendapatan')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/pengeluaran', [PengeluaranController::class, 'index'])
        ->name('divisi.keuangan.pengeluaran')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/cash-flow', [CashFlowController::class, 'index'])
        ->name('divisi.keuangan.cash-flow')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/klaim-bpjs', [KlaimBpjsController::class, 'index'])
        ->name('divisi.keuangan.klaim-bpjs')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);
});

// arahkan halaman utama ke /login
Route::get('/', fn() => redirect()->route('login'));
