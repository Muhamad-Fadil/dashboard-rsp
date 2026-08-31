@extends('layouts.dashboard')

@section('title', 'Pendapatan')

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
    <div class="page-header d-flex justify-content-between align-items-center mb-6">

        <div>
            <h1 class="font-weight-bolder mb-1">
                Pendapatan
            </h1>

            <span class="text-muted-light font-weight-bold">
                Data pendapatan rumah sakit
            </span>
        </div>

        <a
            href="{{ route('divisi.keuangan.pendapatan.pdf', $division->slug) }}"
            target="_blank"
            class="btn font-weight-bold px-5"
            style="background-color: #000; color: #fff; border: none;">
            Download PDF
        </a>

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
                value="{{ $awal }}"
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
                value="{{ $akhir }}"
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

        <div class="col-xl-3 col-md-6 mb-4">

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

    </div>

    {{-- Data Pendapatan --}}
    <div class="card modern-card mb-6">

        <div class="card-body p-5">

            <h3 class="font-weight-bolder text-dark mb-4">
                Data Pendapatan
            </h3>

            <div class="table-responsive">

                <table class="table table-modern">

                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Kategori</th>
                            <th>Unit Kerja</th>
                            <th>Keterangan</th>
                            <th>Jumlah</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($pendapatan as $item)

                            <tr>

                                <td class="font-weight-bold text-dark">
                                    {{ $item->tanggal?->format('d/m/Y') ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->kategori->nama_kategori ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->unitKerja->nama_unit ?? '-' }}
                                </td>

                                <td>
                                    {{ $item->keterangan ?? '-' }}
                                </td>

                                <td class="font-weight-bold text-dark">
                                    Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
                                    Belum ada data pendapatan pada periode ini.
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