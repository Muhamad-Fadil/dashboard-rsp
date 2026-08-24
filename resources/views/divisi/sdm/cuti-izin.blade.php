@extends('layouts.dashboard')

@section('title', 'Cuti & Izin Pegawai')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; }
    .page-header {
        background: linear-gradient(135deg, #8950FC 0%, #6236DB 100%);
        border-radius: 18px; padding: 28px 32px; color: #fff;
        box-shadow: 0 10px 30px rgba(137,80,252,.25);
    }
    .page-header h1 { color: #fff; }
    .page-header .text-muted-light { color: rgba(255,255,255,.8) !important; }
    .modern-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); }
    .filter-card { background: #fff; border-radius: 14px; box-shadow: 0 4px 18px rgba(0,0,0,.06); border: none; }
    .stat-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); }
    .stat-icon { width: 52px; height: 52px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin-bottom: 14px; }
    .stat-value { font-size: 26px; font-weight: 800; }
    .stat-label { font-size: 13px; font-weight: 600; color: #7e8299; margin-top: 4px; }
    .table-modern thead th { border: none; color: #a1a5b7; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
    .table-modern td { border-color: #f1f1f4; vertical-align: middle; }
    .table-modern tbody tr:hover { background: #f9f9fb; }
    .badge-modern { border-radius: 20px; padding: 5px 12px; font-weight: 600; font-size: 11px; }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">
    @include('partials.submenu-sdm')

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div>
            <h1 class="font-weight-bolder mb-1"><i class="fas fa-plane-departure mr-2"></i>Cuti & Izin Pegawai</h1>
            <span class="text-muted-light font-weight-bold">Daftar pengajuan cuti dan izin harian pegawai</span>
        </div>
    </div>

    <form method="GET" class="filter-card d-flex align-items-end flex-wrap p-4 mb-6">
        <div class="form-group mb-0 mr-4">
            <label class="font-weight-bold mb-1 font-size-sm text-muted">Dari Tanggal</label>
            <input type="date" name="awal" value="{{ $awal->format('Y-m-d') }}" class="form-control form-control-solid" style="width: 170px;">
        </div>
        <div class="form-group mb-0 mr-4">
            <label class="font-weight-bold mb-1 font-size-sm text-muted">Sampai Tanggal</label>
            <input type="date" name="akhir" value="{{ $akhir->format('Y-m-d') }}" class="form-control form-control-solid" style="width: 170px;">
        </div>
        <button type="submit" class="btn btn-primary font-weight-bold px-6"><i class="fas fa-filter mr-2"></i>Terapkan</button>
    </form>

    <div class="row">
        <div class="col-xl-4 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-icon" style="background:#FFF6E0; color:#FFA800;"><i class="fas fa-user-clock"></i></div>
                <div class="stat-value text-dark">{{ $jumlahCutiIzin }}</div>
                <div class="stat-label">Total Cuti + Izin (Periode Ini)</div>
            </div></div>
        </div>
        <div class="col-xl-4 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-icon" style="background:#EEF3FF; color:#6993FF;"><i class="fas fa-file-alt"></i></div>
                <div class="stat-value text-dark">{{ $cuti->count() }}</div>
                <div class="stat-label">Pengajuan Cuti</div>
            </div></div>
        </div>
        <div class="col-xl-4 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-icon" style="background:#F1E9FF; color:#8950FC;"><i class="fas fa-calendar-day"></i></div>
                <div class="stat-value text-dark">{{ $izinHarian->count() }}</div>
                <div class="stat-label">Izin Harian (dari Absensi)</div>
            </div></div>
        </div>
    </div>

    <div class="card modern-card mb-6">
        <div class="card-body p-5">
            <h3 class="font-weight-bolder mb-4">Daftar Pengajuan Cuti</h3>
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Nama Pegawai</th>
                            <th>Jenis Cuti</th>
                            <th>Tanggal Mulai</th>
                            <th>Tanggal Selesai</th>
                            <th>Jumlah Hari</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($cuti as $c)
                        <tr>
                            <td class="font-weight-bold text-dark">{{ $c->pegawai->nama ?? '-' }}</td>
                            <td>{{ $c->jenis_cuti }}</td>
                            <td>{{ $c->tanggal_mulai->translatedFormat('d M Y') }}</td>
                            <td>{{ $c->tanggal_selesai->translatedFormat('d M Y') }}</td>
                            <td>{{ $c->jumlah_hari }} hari</td>
                            <td>
                                @php
                                    $statusBadge = [
                                        'disetujui' => ['#E8FFF3', '#1BC5BD'],
                                        'menunggu' => ['#FFF6E0', '#FFA800'],
                                        'ditolak' => ['#FFE9EA', '#F64E60'],
                                    ];
                                @endphp
                                <span class="badge badge-modern" style="background:{{ $statusBadge[$c->status][0] ?? '#F3F6F9' }}; color:{{ $statusBadge[$c->status][1] ?? '#464E5F' }};">
                                    {{ ucfirst($c->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-6">Belum ada pengajuan cuti pada periode ini</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card modern-card mb-6">
        <div class="card-body p-5">
            <h3 class="font-weight-bolder mb-4">Izin Harian (dari Absensi)</h3>
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr><th>Tanggal</th><th>Nama Pegawai</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($izinHarian as $i)
                        <tr>
                            <td>{{ $i->tanggal->translatedFormat('d M Y') }}</td>
                            <td class="font-weight-bold text-dark">{{ $i->pegawai->nama ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="2" class="text-center text-muted py-6">Belum ada izin harian pada periode ini</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection