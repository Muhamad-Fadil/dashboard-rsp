@extends('layouts.dashboard')

@section('title', 'Dashboard Keuangan')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; }
    .page-header {
        background: linear-gradient(135deg, #1BC5BD 0%, #0B806A 100%);
        border-radius: 18px;
        padding: 28px 32px;
        color: #fff;
        box-shadow: 0 10px 30px rgba(27,197,189,.25);
    }
    .page-header h1 { color: #fff; }
    .page-header .text-muted-light { color: rgba(255,255,255,.85) !important; }

    .filter-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(0,0,0,.06);
        border: none;
    }
    .stat-card {
        background: #fff;
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 18px rgba(0,0,0,.06);
        transition: transform .2s ease, box-shadow .2s ease;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(0,0,0,.1);
    }
    .stat-icon {
        width: 52px; height: 52px;
        border-radius: 14px;
        display: flex; align-items: center; justify-content: center;
        font-size: 22px;
        margin-bottom: 14px;
    }
    .stat-value { font-size: 24px; font-weight: 800; line-height: 1.1; }
    .stat-label { font-size: 13px; font-weight: 600; color: #7e8299; margin-top: 4px; }
    .modern-card {
        background: #fff;
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 18px rgba(0,0,0,.06);
    }
    .modern-card .card-title { font-weight: 700; font-size: 17px; color: #181c32; }

    .table-modern thead th {
        border: none; color: #a1a5b7; font-size: 12px; text-transform: uppercase; letter-spacing: .5px;
    }
    .table-modern td { border-color: #f1f1f4; vertical-align: middle; }
    .table-modern tbody tr:hover { background: #f9f9fb; }
    .progress-modern { height: 8px; border-radius: 10px; background: #f1f1f4; overflow: hidden; }
    .progress-modern-bar { height: 100%; border-radius: 10px; background: linear-gradient(90deg,#1BC5BD,#0B806A); }
    .progress-modern-bar.over { background: linear-gradient(90deg,#F64E60,#B3182E); }
    .unit-bar-bg { background: #f1f1f4; border-radius: 10px; height: 6px; overflow: hidden; margin-top: 6px; }
    .unit-bar-fill { height: 100%; border-radius: 10px; background: linear-gradient(90deg,#3699FF,#1B6DE0); }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">

    @include('partials.submenu-keuangan')

    {{-- Header + Filter --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div>
            <h1 class="font-weight-bolder mb-1">Dashboard Keuangan</h1>
            <span class="text-muted-light font-weight-bold">Ringkasan pendapatan, belanja, dan anggaran rumah sakit</span>
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

        <button type="submit" class="btn btn-primary font-weight-bold px-6">
            <i class="fas fa-filter mr-2"></i>Terapkan
        </button>

        <span class="ml-auto font-size-sm text-muted mt-3 mt-md-0">
            <i class="fas fa-info-circle mr-1"></i>Realisasi anggaran mengikuti tahun & bulan dari "Dari Tanggal"
        </span>
    </form>

    {{-- Indikator Utama --}}
    <div class="row">

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-value text-dark">
                        Rp {{ number_format($data['total_pendapatan'], 0, ',', '.') }}
                    </div>
                    <div class="stat-label">Total Pendapatan</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-value text-dark">
                        Rp {{ number_format($data['total_belanja'], 0, ',', '.') }}
                    </div>
                    <div class="stat-label">Total Belanja</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    @php
                        $selisih = $data['total_pendapatan'] - $data['total_belanja'];
                    @endphp

                    <div class="stat-value {{ $selisih >= 0 ? 'text-dark' : 'text-danger' }}">
                        Rp {{ number_format($selisih, 0, ',', '.') }}
                    </div>

                    <div class="stat-label">Selisih (Pendapatan - Belanja)</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-value text-dark">
                        Rp {{ number_format($data['total_piutang'], 0, ',', '.') }}
                    </div>
                    <div class="stat-label">Total Piutang Belum Lunas</div>
                </div>
            </div>
        </div>

    </div>

    {{-- Rincian Belanja --}}
    <div class="row">

        <div class="col-xl-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-label mb-1">Belanja Pegawai</div>
                    <div class="stat-value text-dark">
                        Rp {{ number_format($data['belanja_pegawai'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-label mb-1">Belanja Operasional</div>
                    <div class="stat-value text-dark">
                        Rp {{ number_format($data['belanja_operasional'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">

        {{-- Tren Pendapatan vs Belanja --}}
        <div class="col-lg-7 mb-6">
            <div class="card modern-card h-100">
                <div class="card-body p-5">
                    <h3 class="card-title mb-4">
                        Tren Pendapatan vs Belanja (6 Bulan Terakhir)
                    </h3>

                    <canvas id="chartTren" height="230"></canvas>
                </div>
            </div>
        </div>

        {{-- Pendapatan per Kategori --}}
        <div class="col-lg-5 mb-6">
            <div class="card modern-card h-100">
                <div class="card-body p-5">
                    <h3 class="card-title mb-4">
                        Pendapatan per Kategori
                    </h3>

                    <canvas id="chartKategori" height="220"></canvas>
                </div>
            </div>
        </div>

    </div>

    <div class="row">

        {{-- Pendapatan per Unit --}}
        <div class="col-lg-6 mb-6">
            <div class="card modern-card h-100">
                <div class="card-body p-5">

                    <h3 class="card-title mb-4">
                        Pendapatan per Unit Kerja
                    </h3>

                    @php
                        $maxUnit = $data['pendapatan_per_unit']->max('total') ?: 1;
                    @endphp

                    @forelse ($data['pendapatan_per_unit'] as $unit)

                        <div class="mb-3">

                            <div class="d-flex justify-content-between">
                                <span class="font-weight-bold text-dark font-size-sm">
                                    {{ $unit->nama_unit }}
                                </span>

                                <span class="font-weight-bolder text-primary font-size-sm">
                                    Rp {{ number_format($unit->total, 0, ',', '.') }}
                                </span>
                            </div>

                            <div class="unit-bar-bg">
                                <div
                                    class="unit-bar-fill"
                                    style="width: {{ ($unit->total / $maxUnit) * 100 }}%;">
                                </div>
                            </div>

                        </div>

                    @empty

                        <p class="text-muted">
                            Belum ada data pendapatan per unit pada periode ini
                        </p>

                    @endforelse

                </div>
            </div>
        </div>

        {{-- Realisasi Anggaran --}}
        <div class="col-lg-6 mb-6">
            <div class="card modern-card h-100">
                <div class="card-body p-5">

                    <h3 class="card-title mb-4">
                        Realisasi Anggaran ({{ $awal->translatedFormat('F Y') }})
                    </h3>

                    @forelse ($data['realisasi_anggaran'] as $item)

                        <div class="mb-4">

                            <div class="d-flex justify-content-between mb-1">

                                <span class="font-weight-bold text-dark font-size-sm">
                                    {{ $item['kategori'] }}
                                </span>

                                <span class="font-weight-bolder font-size-sm {{ $item['persentase'] > 100 ? 'text-danger' : 'text-dark' }}">
                                    {{ $item['persentase'] }}%
                                </span>

                            </div>

                            <div class="progress-modern">

                                <div
                                    class="progress-modern-bar {{ $item['persentase'] > 100 ? 'over' : '' }}"
                                    style="width: {{ min($item['persentase'], 100) }}%;">
                                </div>

                            </div>

                            <div class="d-flex justify-content-between mt-1">

                                <span class="text-muted font-size-xs">
                                    Realisasi: Rp {{ number_format($item['realisasi'], 0, ',', '.') }}
                                </span>

                                <span class="text-muted font-size-xs">
                                    Anggaran: Rp {{ number_format($item['anggaran'], 0, ',', '.') }}
                                </span>

                            </div>

                        </div>

                    @empty

                        <p class="text-muted">
                            Belum ada data anggaran untuk periode ini
                        </p>

                    @endforelse

                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    // Tren Pendapatan vs Belanja
    const trenBulan = {!! json_encode($data['tren_bulanan']->pluck('bulan')) !!};
    const trenPendapatan = {!! json_encode($data['tren_bulanan']->pluck('pendapatan')) !!};
    const trenBelanja = {!! json_encode($data['tren_bulanan']->pluck('belanja')) !!};

    new Chart(document.getElementById('chartTren'), {
        type: 'line',

        data: {
            labels: trenBulan,

            datasets: [
                {
                    label: 'Pendapatan',
                    data: trenPendapatan,
                    borderColor: '#1BC5BD',
                    backgroundColor: 'rgba(27,197,189,.1)',
                    tension: .35,
                    fill: true,
                },
                {
                    label: 'Belanja',
                    data: trenBelanja,
                    borderColor: '#F64E60',
                    backgroundColor: 'rgba(246,78,96,.1)',
                    tension: .35,
                    fill: true,
                },
            ]
        },

        options: {
            responsive: true,

            plugins: {
                legend: {
                    position: 'bottom'
                }
            },

            scales: {
                y: {
                    ticks: {
                        callback: v =>
                            'Rp ' +
                            (v / 1000000).toLocaleString('id-ID') +
                            'jt'
                    }
                }
            }
        }
    });

    // Pendapatan per Kategori
    const kategoriLabels = {!! json_encode($data['pendapatan_per_kategori']->pluck('nama_kategori')) !!};
    const kategoriTotals = {!! json_encode($data['pendapatan_per_kategori']->pluck('total')) !!};

    new Chart(document.getElementById('chartKategori'), {
        type: 'doughnut',

        data: {
            labels: kategoriLabels,

            datasets: [{
                data: kategoriTotals,
                backgroundColor: [
                    '#1BC5BD',
                    '#3699FF',
                    '#FFA800',
                    '#8950FC',
                    '#F64E60',
                    '#6993FF'
                ],
                borderWidth: 0,
            }]
        },

        options: {
            responsive: true,
            cutout: '60%',

            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
</script>
@endpush