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
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 18px rgba(0,0,0,.06);
    }

    .stat-card {
        background: #fff;
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 18px rgba(0,0,0,.06);
        height: 100%;
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
        background: linear-gradient(135deg, #1BC5BD 0%, #0B806A 100%);
        width: 100%;
        border-radius: 18px;
        padding: 28px 32px;
        color: #fff;
        box-shadow: 0 10px 30px rgba(27,197,189,.25);
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
        box-shadow: 0 4px 18px rgba(0,0,0,.06);
        border: none;
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

    {{-- Header --}}
    <div class="page-header mb-6">
        <h1 class="font-weight-bolder mb-1">
            Cash Flow
        </h1>

        <span class="text-muted-light font-weight-bold">
            Ringkasan arus kas berdasarkan pendapatan dan pengeluaran rumah sakit
        </span>
    </div>

    {{-- Filter --}}
    <form method="GET" class="filter-card d-flex align-items-end flex-wrap p-4 mb-6">

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
    <div class="row">

        <div class="col-xl-4 col-md-6 mb-4">

            <div class="card stat-card">

                <div class="card-body">

                    <div class="stat-value text-dark">
                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                    </div>

                    <div class="stat-label">
                        Total Pendapatan
                    </div>

                </div>

            </div>

        </div>

        <div class="col-xl-4 col-md-6 mb-4">

            <div class="card stat-card">

                <div class="card-body">

                    <div class="stat-value text-dark">
                        Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
                    </div>

                    <div class="stat-label">
                        Total Pengeluaran
                    </div>

                </div>

            </div>

        </div>

        <div class="col-xl-4 col-md-6 mb-4">

            <div class="card stat-card">

                <div class="card-body">

                    <div class="stat-value {{ $cashFlow >= 0 ? 'text-dark' : 'text-danger' }}">
                        Rp {{ number_format($cashFlow, 0, ',', '.') }}
                    </div>

                    <div class="stat-label">
                        Cash Flow Bersih
                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- Grafik --}}
    <div class="card modern-card mb-6">

        <div class="card-body p-5">

            <h3 class="font-weight-bolder text-dark mb-4">
                Tren Cash Flow 6 Bulan Terakhir
            </h3>

            <canvas id="chartCashFlow" height="120"></canvas>

        </div>

    </div>

    {{-- Ringkasan --}}
    <div class="card modern-card mb-6">

        <div class="card-body p-5">

            <h3 class="font-weight-bolder text-dark mb-4">
                Ringkasan Cash Flow
            </h3>

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

                                <td class="font-weight-bold {{ $bersih >= 0 ? 'text-dark' : 'text-danger' }}">
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