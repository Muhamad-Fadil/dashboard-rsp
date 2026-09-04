@extends('layouts.dashboard')

@section('title', 'Kehadiran Pegawai')

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
    .stat-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); height: 100%; }
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
            <h1 class="font-weight-bolder mb-1"><i class="fas fa-user-check mr-2"></i>Kehadiran Pegawai</h1>
            <span class="text-muted-light font-weight-bold">Rekap dan detail kehadiran harian pegawai</span>
        </div>
        <x-modal-pdf id="modalPdfKehadiran" title="Kehadiran Pegawai" :action="route('divisi.sdm.kehadiran.pdf', $division->slug)" />
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
        <div class="form-group mb-0 mr-4">
            <label class="font-weight-bold mb-1 font-size-sm text-muted">Status</label>
            <select name="status" class="form-control form-control-solid" style="width: 170px;">
                <option value="">Semua Status</option>
                @foreach (['hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'] as $val => $lbl)
                    <option value="{{ $val }}" @selected($statusFilter === $val)>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary font-weight-bold px-6"><i class="fas fa-filter mr-2"></i>Terapkan</button>
    </form>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-icon" style="background:#E8FFF3; color:#1BC5BD;"><i class="fas fa-percent"></i></div>
                <div class="stat-value text-dark">{{ $persentaseKehadiran }}%</div>
                <div class="stat-label">Persentase Kehadiran</div>
            </div></div>
        </div>
        @php
            $statusLabel = ['hadir' => 'Hadir', 'terlambat' => 'Terlambat', 'izin' => 'Izin', 'sakit' => 'Sakit', 'alpha' => 'Alpha'];
            $statusColor = [
                'hadir' => ['#E8FFF3', '#1BC5BD'], 'terlambat' => ['#FFF6E0', '#FFA800'],
                'izin' => ['#EEF3FF', '#6993FF'], 'sakit' => ['#F1E9FF', '#8950FC'], 'alpha' => ['#FFE9EA', '#F64E60'],
            ];
        @endphp
        @foreach ($rekapStatus as $status => $total)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-icon" style="background:{{ $statusColor[$status][0] ?? '#F3F6F9' }}; color:{{ $statusColor[$status][1] ?? '#464E5F' }};">
                    <i class="fas fa-user-clock"></i>
                </div>
                <div class="stat-value text-dark">{{ $total }}</div>
                <div class="stat-label">{{ $statusLabel[$status] ?? ucfirst($status) }}</div>
            </div></div>
        </div>
        @endforeach
    </div>

    <div class="card modern-card mb-6">
        <div class="card-body p-5">
            <h3 class="font-weight-bolder mb-1">Detail Absensi</h3>
            <p class="text-muted font-size-sm mb-4">Menampilkan maksimal 200 record terbaru pada periode ini</p>
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama Pegawai</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($absensi as $a)
                        <tr>
                            <td>{{ $a->tanggal->translatedFormat('d M Y') }}</td>
                            <td class="font-weight-bold text-dark">{{ $a->pegawai->nama ?? '-' }}</td>
                            <td>{{ $a->jam_masuk ? $a->jam_masuk->format('H:i') : '-' }}</td>
                            <td>{{ $a->jam_pulang ? $a->jam_pulang->format('H:i') : '-' }}</td>
                            <td>
                                <span class="badge badge-modern" style="background:{{ $statusColor[$a->status][0] ?? '#F3F6F9' }}; color:{{ $statusColor[$a->status][1] ?? '#464E5F' }};">
                                    {{ $statusLabel[$a->status] ?? ucfirst($a->status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-6">Belum ada data absensi pada periode ini</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection