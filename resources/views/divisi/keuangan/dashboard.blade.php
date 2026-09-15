@extends('layouts.dashboard')

@section('title', 'Dashboard Keuangan')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; }
    .page-header {
        background: linear-gradient(135deg, #0f766e 0%, #134e4a 100%);
        border-radius: 18px;
        padding: 30px 32px;
        color: #fff;
        box-shadow: 0 12px 30px rgba(15, 118, 110, .2);
        overflow: hidden;
        position: relative;
    }
    .page-header::after {
        content: '';
        position: absolute;
        width: 190px;
        height: 190px;
        border: 24px solid rgba(255, 255, 255, .08);
        border-radius: 50%;
        right: -45px;
        top: -80px;
    }
    .page-header h1 { color: #fff; }
    .page-header .text-muted-light { color: rgba(255,255,255,.85) !important; }

    .filter-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 8px 26px rgba(15, 23, 42, .05);
        border: 1px solid #edf0f5;
    }
    .stat-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #edf0f5;
        box-shadow: 0 8px 26px rgba(15, 23, 42, .05);
        transition: transform .2s ease, box-shadow .2s ease;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 32px rgba(15, 23, 42, .09);
    }
    .summary-icon {
        width: 44px;
        height: 44px;
        border-radius: 13px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }
    .stat-value { font-size: 24px; font-weight: 800; line-height: 1.1; }
    .stat-label { font-size: 13px; font-weight: 600; color: #7e8299; margin-top: 4px; }
    .modern-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #edf0f5;
        box-shadow: 0 8px 26px rgba(15, 23, 42, .05);
    }
    .modern-card .card-title { font-weight: 700; font-size: 17px; color: #181c32; }
    .summary-caption,
    .summary-note { color: #7e8299; font-size: 12px; font-weight: 600; }
    .section-kicker { color: #0f766e; font-size: 11px; font-weight: 800; letter-spacing: .8px; text-transform: uppercase; }

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
    .empty-state { padding: 42px 20px; text-align: center; color: #7e8299; }
    @media (max-width: 767.98px) {
        .page-header { padding: 24px; }
        .filter-card .form-group,
        .filter-card .form-control { width: 100% !important; margin-right: 0 !important; }
        .filter-card .btn { width: 100%; margin-top: 12px; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">

    @include('partials.submenu-keuangan')

    @php
        $periodeLabel = $awal->format('d/m/Y') . ' - ' . $akhir->format('d/m/Y');
    @endphp

    {{-- Header + Filter --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div class="position-relative" style="z-index: 1;">
            <div class="text-uppercase font-size-sm font-weight-bold mb-2" style="letter-spacing: 1px; color: #99f6e4;">Ringkasan keuangan</div>
            <h1 class="font-weight-bolder mb-2">Dashboard Keuangan</h1>
            <span class="text-muted-light font-weight-bold">Ringkasan pendapatan, belanja, dan anggaran rumah sakit</span>
        </div>
    </div>

    <form method="GET" class="filter-card d-flex align-items-end flex-wrap p-4 mb-6">
        <div class="w-100 mb-3">
            <div class="section-kicker">Periode laporan</div>
            <div class="summary-note">Atur tanggal untuk memperbarui seluruh indikator dan grafik.</div>
        </div>
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
            <i class="fas fa-info-circle mr-1"></i>Realisasi anggaran mengikuti {{ $awal->translatedFormat('F Y') }}
        </span>
    </form>

    {{-- Indikator Utama --}}
    <div class="row mb-2">

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="summary-icon" style="background: #ccfbf1; color: #0f766e;"><i class="fas fa-wallet"></i></div>
                        <span class="summary-caption">Arus masuk</span>
                    </div>
                    <div class="stat-value text-dark">
                        Rp {{ number_format($data['total_pendapatan'], 0, ',', '.') }}
                    </div>
                    <div class="stat-label">Total pendapatan</div>
                    <div class="summary-note mt-3"><i class="fas fa-calendar-alt mr-1"></i>{{ $periodeLabel }}</div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="summary-icon" style="background: #dbeafe; color: #2563eb;"><i class="fas fa-chart-pie"></i></div>
                        <span class="summary-caption">Perencanaan</span>
                    </div>
                    <div class="stat-value text-dark">
                        Rp {{ number_format($data['total_anggaran'], 0, ',', '.') }}
                    </div>
                    <div class="stat-label">Total anggaran</div>
                    <div class="summary-note mt-3">Anggaran periode berjalan</div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="summary-icon" style="background: #fef3c7; color: #b45309;"><i class="fas fa-file-invoice-dollar"></i></div>
                        <span class="summary-caption">Tagihan</span>
                    </div>
                    <div class="stat-value text-dark">
                        Rp {{ number_format($data['total_piutang'], 0, ',', '.') }}
                    </div>
                    <div class="stat-label">Piutang belum lunas</div>
                    <div class="summary-note mt-3">Perlu ditindaklanjuti</div>
                </div>
            </div>
        </div>

    </div>

    {{-- Rincian Belanja --}}
    <div class="row mb-2">

        <div class="col-xl-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="summary-icon" style="background: #fce7f3; color: #be185d;"><i class="fas fa-users"></i></div>
                        <span class="summary-caption">SDM</span>
                    </div>
                    <div class="stat-label mb-1">Belanja pegawai</div>
                    <div class="stat-value text-dark">
                        Rp {{ number_format($data['belanja_pegawai'], 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="summary-icon" style="background: #ede9fe; color: #7c3aed;"><i class="fas fa-gears"></i></div>
                        <span class="summary-caption">Operasional</span>
                    </div>
                    <div class="stat-label mb-1">Belanja operasional</div>
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
                    <div class="section-kicker mb-1">Pergerakan keuangan</div>
                    <h3 class="card-title mb-4">Tren Pendapatan vs Belanja</h3>
                    <div class="summary-note mb-3">Perbandingan arus masuk dan belanja selama 6 bulan terakhir.</div>

                    <canvas id="chartTren" height="230"></canvas>
                </div>
            </div>
        </div>

        {{-- Pendapatan per Kategori --}}
        <div class="col-lg-5 mb-6">
            <div class="card modern-card h-100">
                <div class="card-body p-5">
                    <div class="section-kicker mb-1">Distribusi penerimaan</div>
                    <h3 class="card-title mb-4">Pendapatan per Kategori</h3>
                    <div class="summary-note mb-3">Kontribusi setiap kategori terhadap total pendapatan.</div>

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
                    <div class="section-kicker mb-1">Sumber penerimaan</div>
                    <h3 class="card-title mb-4">Pendapatan per Unit Kerja</h3>

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

                        <div class="empty-state">
                            <div class="mb-3"><i class="fas fa-building fa-2x text-muted"></i></div>
                            Belum ada data pendapatan per unit pada periode ini.
                        </div>

                    @endforelse

                </div>
            </div>
        </div>

        {{-- Realisasi Anggaran --}}
        <div class="col-lg-6 mb-6">
            <div class="card modern-card h-100">
                <div class="card-body p-5">
                    <div class="section-kicker mb-1">Kontrol anggaran</div>
                    <h3 class="card-title mb-4">Realisasi Anggaran</h3>
                    <div class="summary-note mb-3">Periode {{ $awal->translatedFormat('F Y') }} dengan indikator realisasi terhadap anggaran.</div>

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

                        <div class="empty-state">
                            <div class="mb-3"><i class="fas fa-chart-column fa-2x text-muted"></i></div>
                            Belum ada data anggaran untuk periode ini.
                        </div>

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