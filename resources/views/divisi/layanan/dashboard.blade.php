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
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">

    @include('partials.submenu-layanan')

    {{-- Header --}}
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div>
            <h1 class="font-weight-bolder mb-1">Dashboard Layanan</h1>
            <span class="text-muted-light font-weight-bold">Statistik pelayanan pasien & fasilitas rumah sakit</span>
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
        <div class="form-group mb-0 mr-4">
            <label class="font-weight-bold mb-1 font-size-sm text-muted">Grafik Tren</label>
            <select name="bulan_chart" class="form-control form-control-solid" style="width: 160px;">
                <option value="3" @selected($jumlahBulanChart == 3)>3 Bulan Terakhir</option>
                <option value="6" @selected($jumlahBulanChart == 6)>6 Bulan Terakhir</option>
                <option value="12" @selected($jumlahBulanChart == 12)>12 Bulan Terakhir</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary font-weight-bold px-6">Terapkan</button>
    </form>

    {{-- Akses Cepat ke Semua Sub-Menu --}}
    <div class="dashboard-section">
        <div class="section-title">Akses Cepat</div>
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('divisi.layanan.pasien', $division->slug) }}" class="quickmenu-card">
                    <div class="quickmenu-label">Data Pasien</div>
                    <div class="quickmenu-value">{{ number_format($ringkasanSubMenu['pasien']['total']) }}</div>
                    <div class="quickmenu-sub">Total pasien terdaftar</div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('divisi.layanan.kunjungan', $division->slug) }}" class="quickmenu-card">
                    <div class="quickmenu-label">Kunjungan</div>
                    <div class="quickmenu-value">{{ number_format($ringkasanSubMenu['kunjungan']['total']) }}</div>
                    <div class="quickmenu-sub">{{ $ringkasanSubMenu['kunjungan']['menunggu'] }} sedang menunggu</div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('divisi.layanan.rawat-inap', $division->slug) }}" class="quickmenu-card">
                    <div class="quickmenu-label">Rawat Inap</div>
                    <div class="quickmenu-value">{{ $ringkasanSubMenu['rawat_inap']['dirawat'] }}</div>
                    <div class="quickmenu-sub">Pasien sedang dirawat</div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('divisi.layanan.operasi', $division->slug) }}" class="quickmenu-card">
                    <div class="quickmenu-label">Operasi</div>
                    <div class="quickmenu-value">{{ $ringkasanSubMenu['operasi']['aktif'] }}</div>
                    <div class="quickmenu-sub">Dijadwalkan / berlangsung</div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('divisi.layanan.laboratorium', $division->slug) }}" class="quickmenu-card">
                    <div class="quickmenu-label">Laboratorium</div>
                    <div class="quickmenu-value">{{ $ringkasanSubMenu['laboratorium']['aktif'] }}</div>
                    <div class="quickmenu-sub">Menunggu / diproses</div>
                </a>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <a href="{{ route('divisi.layanan.radiologi', $division->slug) }}" class="quickmenu-card">
                    <div class="quickmenu-label">Radiologi</div>
                    <div class="quickmenu-value">{{ $ringkasanSubMenu['radiologi']['aktif'] }}</div>
                    <div class="quickmenu-sub">Menunggu / diproses</div>
                </a>
            </div>
        </div>
    </div>

    {{-- 8 Indikator Utama --}}
    <div class="dashboard-section">
        <div class="section-title">Indikator Layanan</div>
        <div class="row">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card"><div class="card-body">
                    <div class="stat-value text-dark">{{ number_format($data['jumlah_kunjungan']) }}</div>
                    <div class="stat-label">Jumlah Kunjungan</div>
                </div></div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card"><div class="card-body">
                    <div class="stat-value text-dark">{{ $data['bor'] }}%</div>
                    <div class="stat-label">BOR (Bed Occupancy Rate)</div>
                </div></div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card"><div class="card-body">
                    <div class="stat-value text-dark">{{ $data['alos'] }} <span class="font-size-sm">hr</span></div>
                    <div class="stat-label">ALOS (Avg Length of Stay)</div>
                </div></div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card"><div class="card-body">
                    <div class="stat-value text-dark">{{ $data['pasien_rawat_inap_aktif'] }}</div>
                    <div class="stat-label">Rawat Inap Aktif</div>
                    <div class="text-muted" style="font-size: 10px;">per {{ $akhir->format('d M Y') }}</div>
                </div></div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card"><div class="card-body">
                    <div class="stat-value text-dark">{{ $data['toi'] }} <span class="font-size-sm">hr</span></div>
                    <div class="stat-label">TOI (Turn Over Interval)</div>
                </div></div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card"><div class="card-body">
                    <div class="stat-value text-dark">{{ $data['bto'] }}</div>
                    <div class="stat-label">BTO (Bed Turn Over)</div>
                </div></div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card"><div class="card-body">
                    <div class="stat-value text-dark">{{ $data['ketersediaan_bed']['tersedia'] }}<span class="font-size-lg text-muted">/{{ $data['ketersediaan_bed']['total'] }}</span></div>
                    <div class="stat-label">Bed Tersedia</div>
                    <div class="text-muted" style="font-size: 10px;">per {{ $akhir->format('d M Y') }}</div>
                </div></div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card"><div class="card-body">
                    <div class="stat-value text-dark">{{ $data['waktu_tunggu_rata_rata'] }} <span class="font-size-sm">mnt</span></div>
                    <div class="stat-label">Waktu Tunggu Rata-rata</div>
                </div></div>
            </div>
        </div>
    </div>

    {{-- Tabel Target vs Realisasi --}}
    @php
        $targetRealisasi = [
            ['indikator' => 'Kunjungan pasien', 'target' => '12.000', 'realisasi' => number_format($data['jumlah_kunjungan']), 'ok' => $data['jumlah_kunjungan'] >= 12000],
            ['indikator' => 'BOR', 'target' => '70-85%', 'realisasi' => $data['bor'] . '%', 'ok' => $data['bor'] >= 70 && $data['bor'] <= 85],
            ['indikator' => 'ALOS', 'target' => '4-6 hari', 'realisasi' => $data['alos'] . ' hari', 'ok' => $data['alos'] >= 4 && $data['alos'] <= 6],
            ['indikator' => 'Waktu tunggu', 'target' => '≤ 60 menit', 'realisasi' => $data['waktu_tunggu_rata_rata'] . ' menit', 'ok' => $data['waktu_tunggu_rata_rata'] <= 60],
        ];
    @endphp
    <div class="dashboard-section">
        <div class="card modern-card">
            <div class="card-body p-5">
                <h3 class="card-title mb-4">Ringkasan Target vs Realisasi</h3>
                <table class="table table-modern">
                    <thead><tr><th>Indikator</th><th>Target</th><th>Realisasi</th><th>Status</th></tr></thead>
                    <tbody>
                        @foreach ($targetRealisasi as $row)
                        <tr>
                            <td class="font-weight-bold text-dark">{{ $row['indikator'] }}</td>
                            <td>{{ $row['target'] }}</td>
                            <td class="font-weight-bold">{{ $row['realisasi'] }}</td>
                            <td>
                                @if ($row['ok'])
                                    <span class="badge badge-modern" style="background:#E8FFF3; color:#1BC5BD;">Baik</span>
                                @else
                                    <span class="badge badge-modern" style="background:#FFF6E0; color:#FFA800;">Perlu perhatian</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Grafik Tren Kunjungan + Kunjungan per Poli --}}
    <div class="dashboard-section">
        <div class="row">
            <div class="col-lg-7 mb-4">
                <div class="card modern-card h-100">
                    <div class="card-body p-5">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="card-title mb-0" id="judulGrafikKunjungan">Tren Kunjungan per Bulan</h3>
                            <button type="button" id="btnKembaliBulanan" class="btn btn-sm btn-light-primary font-weight-bold" style="display:none;">
                                Kembali ke Bulanan
                            </button>
                        </div>
                        <p class="text-muted font-size-sm mb-3">Klik salah satu bar bulan untuk lihat rincian per hari</p>
                        <canvas id="chartKunjungan" height="120"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-lg-5 mb-4">
                <div class="card modern-card h-100">
                    <div class="card-body p-5">
                        <h3 class="card-title mb-4">Kunjungan per Poliklinik</h3>
                        @php $maxPoli = $data['kunjungan_per_poli']->max('total') ?: 1; @endphp
                        @forelse ($data['kunjungan_per_poli'] as $poli)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="font-weight-bold text-dark font-size-sm">{{ $poli['nama_poli'] }}</span>
                                <span class="font-weight-bolder text-primary font-size-sm">{{ $poli['total'] }}</span>
                            </div>
                            <div class="poli-bar-bg">
                                <div class="poli-bar-fill" style="width: {{ ($poli['total'] / $maxPoli) * 100 }}%;"></div>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted">Belum ada data</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Trend Penyakit --}}
    <div class="dashboard-section">
        <div class="card modern-card">
            <div class="card-body p-5">
                <h3 class="card-title mb-1">Trend Penyakit Pasien</h3>
                <p class="text-muted font-size-sm mb-4">8 diagnosa terbanyak pada periode yang dipilih</p>
                @php $maxPenyakit = $data['trend_penyakit']->max('total') ?: 1; @endphp
                @forelse ($data['trend_penyakit'] as $penyakit)
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span class="font-weight-bold text-dark font-size-sm">{{ $penyakit->diagnosa }}</span>
                        <span class="font-weight-bolder text-primary font-size-sm">{{ $penyakit->total }} kasus</span>
                    </div>
                    <div class="poli-bar-bg">
                        <div class="poli-bar-fill" style="width: {{ ($penyakit->total / $maxPenyakit) * 100 }}%;"></div>
                    </div>
                </div>
                @empty
                <p class="text-muted">Belum ada data diagnosa pada periode ini</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Trend Daerah Asal Pasien --}}
    <div class="dashboard-section">
        <div class="card modern-card">
            <div class="card-body p-5">
                <h3 class="card-title mb-1">Trend Daerah Asal Pasien</h3>
                <p class="text-muted font-size-sm mb-4">10 kecamatan asal pasien terbanyak (Kabupaten/Kota Bogor)</p>
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead><tr><th>Kecamatan</th><th>Wilayah</th><th>Jumlah Kunjungan</th></tr></thead>
                        <tbody>
                            @forelse ($data['trend_daerah'] as $daerah)
                            <tr>
                                <td class="font-weight-bold text-dark">{{ $daerah->nama_kecamatan }}</td>
                                <td>
                                    @if ($daerah->kabupaten_kota === 'kota')
                                        <span class="badge-modern" style="background:#F1E9FF; color:#8950FC;">Kota Bogor</span>
                                    @else
                                        <span class="badge-modern" style="background:#EEF3FF; color:#6993FF;">Kabupaten Bogor</span>
                                    @endif
                                </td>
                                <td class="font-weight-bold">{{ $daerah->total }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="text-center text-muted py-4">Belum ada data pada periode ini</td></tr>
                            @endforelse
                        </tbody>
                    </table>
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