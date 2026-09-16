@extends('layouts.dashboard')

@section('title', 'Dashboard Layanan')

@push('styles')
@include('partials.dashboard-styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #6993FF 0%, #4D6FE0 100%);
        border-radius: 18px;
        padding: 28px 32px;
        color: #fff;
        box-shadow: 0 10px 30px rgba(105,147,255,.25);
    }
    .page-header h1 { color: #fff; }
    .page-header .text-muted-light { color: rgba(255,255,255,.8) !important; }

    .summary-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px 22px;
        border: 1px solid rgba(105,147,255,.08);
        box-shadow: 0 8px 22px rgba(0,0,0,.06);
        height: 100%;
        position: relative;
        overflow: hidden;
    }
    .summary-card::after {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #6993FF, #1BC5BD);
    }
    .summary-card .summary-label {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .7px;
        text-transform: uppercase;
        color: #7e8299;
    }
    .summary-card .summary-value {
        font-size: 30px;
        font-weight: 800;
        color: #181c32;
        margin: 12px 0 4px;
    }
    .summary-card .summary-meta {
        font-size: 11px;
        color: #a1a5b7;
        font-weight: 600;
    }
    .summary-card .summary-meta span {
        color: #1BC5BD;
    }

    /* --- UI polish for chart and top poliklinik --- */
    .modern-card {
        border-radius: 18px;
        border: 1px solid rgba(105,147,255,.14);
        box-shadow: 0 10px 30px rgba(0,0,0,.05);
        background: #ffffff;
    }

    .section-title {
        font-size: clamp(1.1rem, 2vw, 1.6rem);
        font-weight: 800;
        letter-spacing: -0.02em;
        color: #181c32;
    }

    .chart-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    .chart-head-left {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .chart-head .chart-eyebrow {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .12em;
        color: #6993FF;
    }

    .chart-head .section-title {
        margin: 0;
    }

    .chart-head-tools {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .chart-chip {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #eef4ff;
        color: #4D6FE0;
        font-size: 11px;
        font-weight: 800;
        padding: 6px 12px;
        border-radius: 999px;
    }

    #btnKembaliBulanan {
        border: none;
        border-radius: 10px;
        padding: 8px 14px;
        box-shadow: none;
        font-weight: 700;
        color: #4D6FE0;
        background: #eef4ff;
    }

    #btnKembaliBulanan:hover {
        background: #dce8ff;
    }

    .chart-note {
        font-size: 12px;
        color: #6f7389;
        font-weight: 600;
        margin: 0 0 16px;
    }

    .chart-frame {
        position: relative;
        min-height: 300px;
        height: clamp(280px, 34vw, 360px);
        width: 100%;
    }

    .top-poli-section {
        background: linear-gradient(180deg, #ffffff 0%, #eef4ff 100%);
    }

    .top-poli-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        border-bottom: 1px solid rgba(105,147,255,.12);
        padding-bottom: 12px;
    }

    .top-poli-head .section-title {
        margin: 0;
    }

    .top-poli-date {
        font-size: 11px;
        color: #7e8299;
        font-weight: 700;
    }

    .top-poli-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-top: 16px;
    }

    .top-poli-item {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .top-poli-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        font-size: 12px;
    }

    .top-poli-name {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #181c32;
        font-weight: 800;
        font-size: clamp(11px, 1.7vw, 13px);
    }

    .top-poli-rank {
        color: #6993FF;
        font-weight: 900;
        font-size: 11px;
    }

    .top-poli-total {
        min-width: 46px;
        text-align: right;
        color: #4D6FE0;
        font-weight: 900;
        font-size: clamp(11px, 1.7vw, 13px);
    }

    .top-poli-bar {
        height: 8px;
        background: #eef1f8;
        border-radius: 999px;
        overflow: hidden;
        width: 100%;
    }

    .top-poli-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #6993FF, #1BC5BD);
        border-radius: inherit;
    }

    @media (max-width: 767px) {
        .chart-head {
            align-items: flex-start;
        }

        .chart-frame {
            min-height: 260px;
            height: 280px;
        }

        .top-poli-head {
            align-items: flex-start;
        }

        .top-poli-total {
            min-width: 40px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-10 py-6">

    @include('partials.submenu-layanan')

    {{-- Header --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div>
            <h1 class="font-weight-bolder mb-1">Dashboard Layanan</h1>
            <span class="text-muted-light font-weight-bold">Statistik pelayanan pasien & fasilitas rumah sakit</span>
                        <div class="text-muted-light font-size-sm mt-1">Menampilkan data periode {{ $awal->format('d M Y') }} — {{ $akhir->format('d M Y') }}</div>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="filter-card d-flex align-items-end flex-wrap p-4 mb-8">
        <div class="form-group mb-0 mr-4">
            <label class="font-weight-bold mb-1 font-size-sm text-muted">Dari Tanggal</label>
            <input type="date" name="awal" value="{{ $awal->format('Y-m-d') }}" class="form-control form-control-solid" style="width: 170px;">
        </div>
        <div class="form-group mb-0 mr-4">
            <label class="font-weight-bold mb-1 font-size-sm text-muted">Sampai Tanggal</label>
            <input type="date" name="akhir" value="{{ $akhir->format('Y-m-d') }}" class="form-control form-control-solid" style="width: 170px;">
        </div>
        <button type="submit" class="btn btn-primary font-weight-bold px-6">Terapkan</button>
        <a href="{{ route('divisi.dashboard', $division->slug) }}" class="btn btn-light font-weight-bold px-6 ml-2">
            Reset
        </a>
    </form>

    {{-- Ringkasan Layanan Lengkap --}}
    <div class="dashboard-section">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
            <div>
                <div class="section-title font-size-h1 mb-0">Ringkasan Indikator Layanan</div>
                <div class="text-muted font-size-sm">Periode {{ $awal->format('d M Y') }} — {{ $akhir->format('d M Y') }}</div>
            </div>
            <div class="data-freshness">
                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 6L9 17l-5-5"/>
                </svg>
                Data terkini
            </div>
        </div>

        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="summary-card">
                    <div class="summary-label">
                        <span>Jumlah Kunjungan</span>
                        <span class="badge badge-primary">Total</span>
                    </div>
                    <div class="summary-value">{{ number_format($data['jumlah_kunjungan']) }}</div>
                    <div class="summary-meta">
                        <span>{{ $data['status_kunjungan']['selesai'] }}</span> selesai ·
                        <span style="color:#F64E60;">{{ $data['status_kunjungan']['batal'] }}</span> batal
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="summary-card">
                    <div class="summary-label">
                        <span>BOR</span>
                        <span class="badge badge-warning">Utilisasi</span>
                    </div>
                    <div class="summary-value">{{ $data['bor'] }}%</div>
                    <div class="summary-meta">Bed Occupancy Rate</div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="summary-card">
                    <div class="summary-label">
                        <span>ALOS</span>
                        <span class="badge badge-info">Hari</span>
                    </div>
                    <div class="summary-value">{{ $data['alos'] }}</div>
                    <div class="summary-meta">Avg Length of Stay</div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="summary-card">
                    <div class="summary-label">
                        <span>Rawat Inap Aktif</span>
                        <span class="badge badge-success">Aktif</span>
                    </div>
                    <div class="summary-value">{{ number_format($data['pasien_rawat_inap_aktif']) }}</div>
                    <div class="summary-meta">Pasien dirawat saat ini</div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="summary-card">
                    <div class="summary-label">
                        <span>TOI</span>
                        <span class="badge badge-secondary">Hari</span>
                    </div>
                    <div class="summary-value">{{ $data['toi'] }}</div>
                    <div class="summary-meta">Turn Over Interval</div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="summary-card">
                    <div class="summary-label">
                        <span>BTO</span>
                        <span class="badge badge-secondary">Frek.</span>
                    </div>
                    <div class="summary-value">{{ $data['bto'] }}</div>
                    <div class="summary-meta">Bed Turn Over</div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="summary-card">
                    <div class="summary-label">
                        <span>Bed Tersedia</span>
                        <span class="badge badge-info">Kamar</span>
                    </div>
                    <div class="summary-value">{{ number_format($data['ketersediaan_bed']['tersedia']) }}/{{ number_format($data['ketersediaan_bed']['total']) }}</div>
                    <div class="summary-meta">tersedia / total</div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="summary-card">
                    <div class="summary-label">
                        <span>Waktu Tunggu</span>
                        <span class="badge badge-primary">Rata-rata</span>
                    </div>
                    <div class="summary-value">{{ $data['waktu_tunggu_rata_rata'] }}</div>
                    <div class="summary-meta">Menit per kunjungan</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Tren Kunjungan Bulanan --}}
    <div class="dashboard-section">
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card modern-card h-100">
                    <div class="card-body p-4">
                        <div class="chart-head">
                            <div class="chart-head-left">
                                <span class="chart-eyebrow">Tren Kunjungan</span>
                                <h3 class="section-title font-size-h1 mb-0" id="judulGrafikKunjungan">Tren Kunjungan Bulanan</h3>
                            </div>
                            <div class="chart-head-tools">
                                <span class="chart-chip"><i class="fas fa-calendar-alt mr-2"></i>{{ $awal->format('d M Y') }} – {{ $akhir->format('d M Y') }}</span>
                                <button type="button" id="btnKembaliBulanan" class="btn btn-sm btn-light-primary font-weight-bold" style="display:none;">
                                    Kembali ke Bulanan
                                </button>
                            </div>
                        </div>

                        <p class="chart-note">Klik salah satu bulan untuk melihat rincian harian</p>

                        <div class="chart-frame">
                            <canvas id="chartKunjungan"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top 10 Poliklinik --}}
    <div class="dashboard-section top-poli-section">
        <div class="row">
            <div class="col-lg-12 mb-4">
                <div class="card modern-card h-100">
                    <div class="card-body p-4">
                        <div class="top-poli-head">
                            <h3 class="section-title font-size-h1 mb-0">10 Top Poliklinik</h3>
                            <span class="top-poli-date">Periode {{ $awal->format('d M Y') }} — {{ $akhir->format('d M Y') }}</span>
                        </div>

                        @php
                            $topPoli = collect($data['kunjungan_per_poli'])
                                ->sortByDesc('total')
                                ->take(10);
                            $maxTopPoli = $topPoli->max('total') ?: 1;
                        @endphp

                        @if ($topPoli->isEmpty())
                            <p class="text-muted mb-0">Belum ada data poliklinik.</p>
                        @else
                            <div class="top-poli-list">
                                @foreach ($topPoli as $index => $poli)
                                    @php
                                        $width = max((($poli['total'] / $maxTopPoli) * 100), 3);
                                    @endphp
                                    <div class="top-poli-item">
                                        <div class="top-poli-top">
                                            <span class="top-poli-name">
                                                <span class="top-poli-rank">{{ $index + 1 }}.</span>
                                                {{ $poli['nama_poli'] }}
                                            </span>
                                            <span class="top-poli-total">{{ number_format($poli['total']) }}</span>
                                        </div>
                                        <div class="top-poli-bar">
                                            <div class="top-poli-bar-fill" style="width: {{ $width }}%;"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    const dataBulanan = {!! json_encode($data['kunjungan_per_bulan']) !!};
    const urlKunjunganHarian = "{{ route('divisi.layanan.kunjungan-harian', $division->slug) }}";

    const ctx = document.getElementById('chartKunjungan');
    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 300);
    gradient.addColorStop(0, 'rgba(105,147,255,0.9)');
    gradient.addColorStop(1, 'rgba(105,147,255,0.5)');

    const chartKunjungan = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: dataBulanan.map(d => d.bulan),
            datasets: [{
                label: 'Jumlah Kunjungan',
                data: dataBulanan.map(d => d.total),
                backgroundColor: gradient,
                borderRadius: 8,
                maxBarThickness: 48,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { color: '#f1f1f4' } },
                x: { grid: { display: false } }
            },
            onClick: (evt, elements) => {
                if (!elements.length) return;
                const index = elements[0].index;
                const bulanDipilih = dataBulanan[index];
                tampilkanGrafikHarian(bulanDipilih);
            }
        }
    });

    function tampilkanGrafikHarian(bulanDipilih) {
        fetch(`${urlKunjunganHarian}?tahun=${bulanDipilih.tahun_angka}&bulan=${bulanDipilih.bulan_angka}`)
            .then(res => res.json())
            .then(dataHarian => {
                chartKunjungan.config.type = 'line';
                chartKunjungan.data.labels = dataHarian.map(d => d.tanggal);
                chartKunjungan.data.datasets[0].data = dataHarian.map(d => d.total);
                chartKunjungan.data.datasets[0].fill = true;
                chartKunjungan.data.datasets[0].tension = 0.3;
                chartKunjungan.data.datasets[0].pointRadius = 3;
                chartKunjungan.update();

                document.getElementById('judulGrafikKunjungan').innerText =
                    `Kunjungan Harian - ${bulanDipilih.bulan}`;
                document.getElementById('btnKembaliBulanan').style.display = 'inline-block';
            });
    }

    document.getElementById('btnKembaliBulanan').addEventListener('click', () => {
        chartKunjungan.config.type = 'bar';
        chartKunjungan.data.labels = dataBulanan.map(d => d.bulan);
        chartKunjungan.data.datasets[0].data = dataBulanan.map(d => d.total);
        chartKunjungan.data.datasets[0].fill = false;
        chartKunjungan.update();

        document.getElementById('judulGrafikKunjungan').innerText = 'Tren Kunjungan per Bulan';
        document.getElementById('btnKembaliBulanan').style.display = 'none';
    });
</script>
@endpush