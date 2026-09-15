@extends('layouts.dashboard')

@section('title', 'Cash Flow')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    body {
        font-family: 'Poppins', sans-serif;
    }

    .modern-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #edf0f5;
        box-shadow: 0 8px 26px rgba(15, 23, 42, .05);
    }

    .stat-card {
        background: #fff;
        border-radius: 18px;
        border: 1px solid #edf0f5;
        box-shadow: 0 8px 26px rgba(15, 23, 42, .05);
        height: 100%;
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 32px rgba(15, 23, 42, .09);
    }

    .stat-value {
        font-size: 26px;
        font-weight: 800;
        line-height: 1.1;
    }

    .stat-label {
        font-size: 13px;
        font-weight: 600;
        color: #7e8299;
        margin-top: 4px;
    }

    .page-header {
        background: linear-gradient(135deg, #0f766e 0%, #134e4a 100%);
        width: 100%;
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

    .page-header h1 {
        color: #fff;
    }

    .page-header .text-muted-light {
        color: rgba(255,255,255,.85) !important;
    }

    .filter-card {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 8px 26px rgba(15, 23, 42, .05);
        border: 1px solid #edf0f5;
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

    .summary-caption,
    .summary-note {
        color: #7e8299;
        font-size: 12px;
        font-weight: 600;
    }

    .section-kicker {
        color: #0f766e;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .8px;
        text-transform: uppercase;
    }

    .cash-flow-positive { color: #047857 !important; }
    .cash-flow-negative { color: #dc2626 !important; }

    .empty-state {
        padding: 58px 20px;
        text-align: center;
        color: #7e8299;
    }

    @media (max-width: 767.98px) {
        .page-header { padding: 24px; }
        .filter-card .form-group,
        .filter-card .form-control { width: 100% !important; margin-right: 0 !important; }
        .filter-card .btn { width: 100%; margin-top: 12px; }
    }

    .table-modern thead th {
        border: none;
        color: #a1a5b7;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    .table-modern td {
        border-color: #f1f1f4;
        vertical-align: middle;
    }

    .table-modern tbody tr:hover {
        background: #f9f9fb;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">

    @include('partials.submenu-keuangan')

    @php
        $periodeLabel = $awal->format('d/m/Y') . ' - ' . $akhir->format('d/m/Y');
    @endphp

    {{-- Header --}}
    <div class="page-header mb-6">
        <div class="position-relative" style="z-index: 1;">
            <div class="text-uppercase font-size-sm font-weight-bold mb-2" style="letter-spacing: 1px; color: #99f6e4;">
                Ringkasan keuangan
            </div>
            <h1 class="font-weight-bolder mb-2">Cash Flow</h1>
            <span class="text-muted-light font-weight-bold">
                Ringkasan arus kas berdasarkan pendapatan dan pengeluaran rumah sakit
            </span>
        </div>
    </div>

    {{-- Filter --}}
    <form method="GET" class="filter-card d-flex align-items-end flex-wrap p-4 mb-6">

        <div class="w-100 mb-3">
            <div class="section-kicker">Periode laporan</div>
            <div class="summary-note">Atur rentang tanggal untuk memperbarui ringkasan cash flow.</div>
        </div>

        <div class="form-group mb-0 mr-4">
            <label class="font-weight-bold mb-1 font-size-sm text-muted">
                Dari Tanggal
            </label>

            <input
                type="date"
                name="awal"
                value="{{ $awal->format('Y-m-d') }}"
                class="form-control form-control-solid"
                style="width: 170px;">
        </div>

        <div class="form-group mb-0 mr-4">
            <label class="font-weight-bold mb-1 font-size-sm text-muted">
                Sampai Tanggal
            </label>

            <input
                type="date"
                name="akhir"
                value="{{ $akhir->format('Y-m-d') }}"
                class="form-control form-control-solid"
                style="width: 170px;">
        </div>

        <button
            type="submit"
            class="btn btn-primary font-weight-bold px-6">
            Terapkan
        </button>

    </form>

    {{-- Ringkasan --}}
    <div class="row mb-2">

        <div class="col-xl-4 col-md-6 mb-4">

            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="summary-icon" style="background: #ccfbf1; color: #0f766e;"><i class="fas fa-arrow-trend-up"></i></div>
                        <span class="summary-caption">Arus masuk</span>
                    </div>
                    <div class="stat-value text-dark">
                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
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
                        <div class="summary-icon" style="background: #fee2e2; color: #dc2626;"><i class="fas fa-arrow-trend-down"></i></div>
                        <span class="summary-caption">Arus keluar</span>
                    </div>
                    <div class="stat-value text-dark">
                        Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                    </div>
                    <div class="stat-label">Total pengeluaran</div>
                    <div class="summary-note mt-3">Belanja pada periode aktif</div>
                </div>

            </div>

        </div>

        <div class="col-xl-4 col-md-6 mb-4">

            <div class="card stat-card">

                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="summary-icon" style="background: {{ $cashFlow >= 0 ? '#d1fae5' : '#fee2e2' }}; color: {{ $cashFlow >= 0 ? '#047857' : '#dc2626' }};"><i class="fas fa-scale-balanced"></i></div>
                        <span class="summary-caption">Saldo bersih</span>
                    </div>
                    <div class="stat-value {{ $cashFlow >= 0 ? 'text-dark' : 'text-danger' }}">
                        Rp {{ number_format($cashFlow, 0, ',', '.') }}
                    </div>
                    <div class="stat-label">Cash flow bersih</div>
                    <div class="summary-note mt-3 {{ $cashFlow >= 0 ? 'cash-flow-positive' : 'cash-flow-negative' }}">
                        <i class="fas fa-{{ $cashFlow >= 0 ? 'arrow-up' : 'arrow-down' }} mr-1"></i>{{ $cashFlow >= 0 ? 'Surplus' : 'Defisit' }} periode aktif
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- Grafik --}}
    <div class="card modern-card mb-6">

        <div class="card-body p-5">

            <div class="section-kicker mb-1">Pergerakan arus kas</div>
            <h3 class="font-weight-bolder text-dark mb-4">Tren Cash Flow 6 Bulan Terakhir</h3>

            <canvas id="chartCashFlow" height="120"></canvas>

        </div>

    </div>

    {{-- Ringkasan --}}
    <div class="card modern-card mb-6">

        <div class="card-body p-5">

            <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                <div>
                    <div class="section-kicker mb-1">Rincian bulanan</div>
                    <h3 class="font-weight-bolder text-dark mb-0">Ringkasan Cash Flow</h3>
                </div>
                <span class="badge badge-light-primary px-3 py-2 mt-2 mt-md-0">
                    {{ number_format($trenBulanan->count(), 0, ',', '.') }} bulan
                </span>
            </div>

            @if ($trenBulanan->isEmpty())
                <div class="empty-state">
                    <div class="mb-3"><i class="fas fa-chart-line fa-3x text-muted"></i></div>
                    <strong>Belum ada data cash flow.</strong>
                    <div class="mt-2">Coba pilih rentang tanggal yang berbeda untuk melihat ringkasan.</div>
                </div>
            @else
            <div class="table-responsive">

                <table class="table table-modern">

                    <thead>
                        <tr>
                            <th>Bulan</th>
                            <th>Pendapatan</th>
                            <th>Pengeluaran</th>
                            <th>Cash Flow Bersih</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($trenBulanan as $item)

                            @php
                                $bersih = $item['pendapatan'] - $item['belanja'];
                            @endphp

                            <tr>

                                <td class="font-weight-bold text-dark">
                                    {{ $item['bulan'] }}
                                </td>

                                <td>
                                    Rp {{ number_format($item['pendapatan'], 0, ',', '.') }}
                                </td>

                                <td>
                                    Rp {{ number_format($item['belanja'], 0, ',', '.') }}
                                </td>

                                <td class="font-weight-bold {{ $bersih >= 0 ? 'cash-flow-positive' : 'cash-flow-negative' }}">
                                    Rp {{ number_format($bersih, 0, ',', '.') }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
                                    Belum ada data cash flow.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>
            @endif

        </div>

    </div>

</div>
@endsection

@push('scripts')
<script>
    const cashFlowLabels = {!! json_encode($trenBulanan->pluck('bulan')) !!};
    const cashFlowPendapatan = {!! json_encode($trenBulanan->pluck('pendapatan')) !!};
    const cashFlowPengeluaran = {!! json_encode($trenBulanan->pluck('belanja')) !!};

    new Chart(document.getElementById('chartCashFlow'), {
        type: 'line',

        data: {
            labels: cashFlowLabels,

            datasets: [
                {
                    label: 'Pendapatan',
                    data: cashFlowPendapatan,
                    borderColor: '#1BC5BD',
                    backgroundColor: 'rgba(27,197,189,.1)',
                    tension: .35,
                    fill: true
                },
                {
                    label: 'Pengeluaran',
                    data: cashFlowPengeluaran,
                    borderColor: '#F64E60',
                    backgroundColor: 'rgba(246,78,96,.1)',
                    tension: .35,
                    fill: true
                }
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
                        callback: function(value) {
                            return 'Rp ' + (value / 1000000).toLocaleString('id-ID') + 'jt';
                        }
                    }
                }
            }
        }
    });
</script>
@endpush