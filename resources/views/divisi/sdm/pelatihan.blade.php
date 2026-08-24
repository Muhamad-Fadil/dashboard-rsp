@extends('layouts.dashboard')

@section('title', 'Pelatihan Pegawai')

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
            <h1 class="font-weight-bolder mb-1"><i class="fas fa-graduation-cap mr-2"></i>Pelatihan Pegawai</h1>
            <span class="text-muted-light font-weight-bold">Daftar program pelatihan dan jumlah pesertanya</span>
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

    <div class="card modern-card mb-6">
        <div class="card-body p-5">
            <h3 class="font-weight-bolder mb-4">Daftar Pelatihan ({{ $pelatihan->count() }})</h3>
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Nama Pelatihan</th>
                            <th>Penyelenggara</th>
                            <th>Tanggal</th>
                            <th>Lokasi</th>
                            <th>Peserta</th>
                            <th>Selesai</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pelatihan as $p)
                        <tr>
                            <td class="font-weight-bold text-dark">{{ $p->nama_pelatihan }}</td>
                            <td>{{ $p->penyelenggara }}</td>
                            <td>{{ $p->tanggal_mulai->translatedFormat('d M Y') }} &ndash; {{ $p->tanggal_selesai->translatedFormat('d M Y') }}</td>
                            <td>{{ $p->lokasi }}</td>
                            <td>
                                <span class="badge badge-modern" style="background:#EEF3FF; color:#6993FF;">{{ $p->jumlah_peserta }} peserta</span>
                            </td>
                            <td>
                                <span class="badge badge-modern" style="background:#E8FFF3; color:#1BC5BD;">{{ $p->jumlah_selesai }} selesai</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-6">Belum ada pelatihan pada periode ini</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection