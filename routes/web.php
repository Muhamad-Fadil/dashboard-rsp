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
use App\Http\Controllers\ProduktivitasController;
use App\Http\Controllers\KomposisiPegawaiController;
use App\Http\Controllers\DataPegawaiController;
use App\Http\Controllers\JadwalKerjaController;
use App\Http\Controllers\KehadiranController;
use App\Http\Controllers\CutiIzinController;
use App\Http\Controllers\DistribusiPegawaiController;
use App\Http\Controllers\PelatihanController;
use App\Http\Controllers\PendapatanController;
use App\Http\Controllers\PengeluaranController;
use App\Http\Controllers\CashFlowController;
use App\Http\Controllers\KlaimBpjsController;
use App\Http\Controllers\OperatorManagementController;
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

    Route::get('/admin/users/{user}/edit', [AdminController::class, 'editUser'])
        ->name('admin.users.edit')
        ->middleware('role:admin');

    Route::put('/admin/users/{user}', [AdminController::class, 'updateUser'])
        ->name('admin.users.update')
        ->middleware('role:admin');

    // ---- Kelola Operator: bisa diakses Admin (semua divisi) dan Manajer (divisinya sendiri) ----
    Route::prefix('admin/operator')->name('admin.operator.')->middleware('role:admin,manajer')->group(function () {
        Route::get('/', [OperatorManagementController::class, 'index'])->name('index');
        Route::get('/create', [OperatorManagementController::class, 'create'])->name('create');
        Route::post('/', [OperatorManagementController::class, 'store'])->name('store');
        Route::get('/{operator}/edit', [OperatorManagementController::class, 'edit'])->name('edit');
        Route::put('/{operator}', [OperatorManagementController::class, 'update'])->name('update');
        Route::delete('/{operator}', [OperatorManagementController::class, 'destroy'])->name('destroy');
    });

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
        ->middleware(['role:direktur,manajer,operator', 'division.access', 'submenu:pasien']);

    Route::get('/divisi/{division:slug}/pasien/pdf', [PasienController::class, 'exportPdf'])
        ->name('divisi.layanan.pasien.pdf')
        ->middleware(['role:direktur,manajer,operator', 'division.access', 'submenu:pasien']);

    Route::get('/divisi/{division:slug}/kunjungan', [KunjunganController::class, 'index'])
        ->name('divisi.layanan.kunjungan')
        ->middleware(['role:direktur,manajer,operator', 'division.access', 'submenu:kunjungan']);

    Route::get('/divisi/{division:slug}/kunjungan/pdf', [KunjunganController::class, 'exportPdf'])
        ->name('divisi.layanan.kunjungan.pdf')
        ->middleware(['role:direktur,manajer,operator', 'division.access', 'submenu:kunjungan']);

    Route::get('/divisi/{division:slug}/rawat-inap', [RawatInapController::class, 'index'])
        ->name('divisi.layanan.rawat-inap')
        ->middleware(['role:direktur,manajer,operator', 'division.access', 'submenu:rawat-inap']);

    Route::get('/divisi/{division:slug}/rawat-inap/pdf', [RawatInapController::class, 'exportPdf'])
        ->name('divisi.layanan.rawat-inap.pdf')
        ->middleware(['role:direktur,manajer,operator', 'division.access', 'submenu:rawat-inap']);

    Route::get('/divisi/{division:slug}/operasi', [OperasiController::class, 'index'])
        ->name('divisi.layanan.operasi')
        ->middleware(['role:direktur,manajer,operator', 'division.access', 'submenu:operasi']);

    Route::get('/divisi/{division:slug}/laboratorium', [LaboratoriumController::class, 'index'])
        ->name('divisi.layanan.laboratorium')
        ->middleware(['role:direktur,manajer,operator', 'division.access', 'submenu:laboratorium']);

    Route::get('/divisi/{division:slug}/radiologi', [RadiologiController::class, 'index'])
        ->name('divisi.layanan.radiologi')
        ->middleware(['role:direktur,manajer,operator', 'division.access', 'submenu:radiologi']);

    // ---- Sub-menu SDM: Komposisi Pegawai, Kehadiran, Cuti & Izin, Distribusi Pegawai, Pelatihan, Produktivitas, Data Pegawai, Jadwal Kerja ----
    Route::get('/divisi/{division:slug}/komposisi', [KomposisiPegawaiController::class, 'index'])
        ->name('divisi.sdm.komposisi')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/data-pegawai', [DataPegawaiController::class, 'index'])
        ->name('divisi.sdm.data-pegawai')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/data-pegawai/pdf', [DataPegawaiController::class, 'exportPdf'])
        ->name('divisi.sdm.data-pegawai.pdf')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/jadwal-kerja', [JadwalKerjaController::class, 'index'])
        ->name('divisi.sdm.jadwal-kerja')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/jadwal-kerja/pdf', [JadwalKerjaController::class, 'exportPdf'])
        ->name('divisi.sdm.jadwal-kerja.pdf')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/kehadiran', [KehadiranController::class, 'index'])
        ->name('divisi.sdm.kehadiran')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/kehadiran/pdf', [KehadiranController::class, 'exportPdf'])
        ->name('divisi.sdm.kehadiran.pdf')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/cuti-izin', [CutiIzinController::class, 'index'])
        ->name('divisi.sdm.cuti-izin')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/cuti-izin/pdf', [CutiIzinController::class, 'exportPdf'])
        ->name('divisi.sdm.cuti-izin.pdf')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/distribusi', [DistribusiPegawaiController::class, 'index'])
        ->name('divisi.sdm.distribusi')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/pelatihan', [PelatihanController::class, 'index'])
        ->name('divisi.sdm.pelatihan')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/produktivitas', [ProduktivitasController::class, 'index'])
        ->name('divisi.sdm.produktivitas')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

   // ---- Sub-menu Keuangan: Pendapatan, Pengeluaran, Cashflow ----

    Route::get('/divisi/{division:slug}/pendapatan', [PendapatanController::class, 'index'])
        ->name('divisi.keuangan.pendapatan')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/pendapatan/pdf', [PendapatanController::class, 'exportPdf'])
        ->name('divisi.keuangan.pendapatan.pdf')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/pengeluaran', [PengeluaranController::class, 'index'])
        ->name('divisi.keuangan.pengeluaran')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/pengeluaran/pdf', [PengeluaranController::class, 'exportPdf'])
        ->name('divisi.keuangan.pengeluaran.pdf')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/cash-flow', [CashFlowController::class, 'index'])
        ->name('divisi.keuangan.cash-flow')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/klaim-bpjs', [KlaimBpjsController::class, 'index'])
        ->name('divisi.keuangan.klaim-bpjs')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);

    Route::get('/divisi/{division:slug}/klaim-bpjs/pdf', [KlaimBpjsController::class, 'exportPdf'])
        ->name('divisi.keuangan.klaim-bpjs.pdf')
        ->middleware(['role:direktur,manajer,operator', 'division.access']);
});

// arahkan halaman utama ke /login
Route::get('/', fn() => redirect()->route('login'));
