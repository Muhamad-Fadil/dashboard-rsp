@extends('layouts.dashboard')

@section('title', 'Produktivitas SDM')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; }
    .modern-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); }
    .filter-card { background: #fff; border-radius: 14px; box-shadow: 0 4px 18px rgba(0,0,0,.06); border: none; }
    .stat-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); height: 100%; }
    .stat-icon {
        width: 52px; height: 52px; border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px; margin-bottom: 14px;
    }
    .stat-value { font-size: 26px; font-weight: 800; line-height: 1.1; }
    .stat-label { font-size: 13px; font-weight: 600; color: #7e8299; margin-top: 4px; }
    .table-modern thead th { border: none; color: #a1a5b7; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
    .table-modern td { border-color: #f1f1f4; vertical-align: middle; }
    .table-modern tbody tr:hover { background: #f9f9fb; }
    .badge-modern { border-radius: 20px; padding: 6px 14px; font-weight: 600; font-size: 12px; }

    .page-header {
        background: linear-gradient(135deg, #8950FC 0%, #6236DB 100%);
        border-radius: 18px;
        padding: 28px 32px;
        color: #fff;
        box-shadow: 0 10px 30px rgba(137,80,252,.25);
    }
    .page-header h1 { color: #fff; }
    .page-header .text-muted-light { color: rgba(255,255,255,.8) !important; }

    .rasio-bar-bg { background: #f1f1f4; border-radius: 10px; height: 6px; overflow: hidden; margin-top: 6px; }
    .rasio-bar-fill { height: 100%; border-radius: 10px; background: linear-gradient(90deg,#8950FC,#6236DB); }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">

    @include('partials.submenu-sdm')

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div>
            <h1 class="font-weight-bolder mb-1"><i class="fas fa-chart-simple fa-chart-bar mr-2"></i>Produktivitas SDM</h1>
            <span class="text-muted-light font-weight-bold">Perbandingan beban kerja (kunjungan pasien) dengan jumlah tenaga per unit</span>
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
            <h3 class="card-title font-weight-bold mb-1">Rasio Beban Kerja per Unit</h3>
            <p class="text-muted font-size-sm mb-4">
                Beban kerja dihitung dari jumlah kunjungan pasien pada unit tersebut selama periode terpilih.
                Rasio = beban kerja &divide; jumlah tenaga aktif di unit itu (kunjungan per pegawai). Unit dengan
                rasio tinggi berarti bebannya besar dibanding jumlah tenaganya.
            </p>

            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Unit Kerja</th>
                            <th>Jumlah Tenaga</th>
                            <th>Beban Kerja (Kunjungan)</th>
                            <th>Rasio / Tenaga</th>
                            <th style="min-width: 160px;">Visual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $maxRasio = $produktivitas->max('rasio') ?: 1; @endphp
                        @forelse ($produktivitas as $unit)
                        <tr>
                            <td class="font-weight-bold text-dark">{{ $unit['nama_unit'] }}</td>
                            <td>{{ $unit['jumlah_pegawai'] }}</td>
                            <td>{{ $unit['beban_kerja'] }}</td>
                            <td>
                                <span class="badge badge-modern" style="background:#F1E9FF; color:#8950FC;">
                                    {{ $unit['rasio'] }}
                                </span>
                            </td>
                            <td>
                                <div class="rasio-bar-bg">
                                    <div class="rasio-bar-fill" style="width: {{ ($unit['rasio'] / $maxRasio) * 100 }}%;"></div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-muted">Belum ada data untuk periode ini</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection