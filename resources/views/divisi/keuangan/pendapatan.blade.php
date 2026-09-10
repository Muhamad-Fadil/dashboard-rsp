@extends('layouts.dashboard')

@section('title', 'Pendapatan')

@php
    // daftar sub-submenu (tab) Pendapatan
    $tabList = [
        'semua' => 'Semua Pendapatan',
        'bpjs' => 'BPJS',
        'tunai' => 'Tunai',
        'igd' => 'IGD',
        'poliklinik' => 'Per Poliklinik (Rawat Jalan)',
        'ruangan' => 'Per Ruangan (Rawat Inap)',
    ];

    // tab yang datanya belum tersedia -- tampilannya sudah jadi, isinya nanti nyusul
    $tabBelumTersedia = ['tunai', 'poliklinik', 'ruangan'];
@endphp

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

    .subtab-pendapatan {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(0,0,0,.06);
        overflow-x: auto;
        margin-bottom: 16px;
    }

    .subtab-pendapatan .subtab-track {
        display: flex;
        min-width: max-content;
        padding: 8px;
        gap: 4px;
    }

    .subtab-pendapatan .subtab-item {
        padding: 9px 16px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 13px;
        text-decoration: none;
        white-space: nowrap;
        color: #7e8299;
    }

    .subtab-pendapatan .subtab-item.aktif {
        background: #1BC5BD;
        color: #fff;
    }

    .subtab-pendapatan .subtab-item:not(.aktif):hover {
        background: #f4f6f9;
        color: #464E5F;
    }

    .badge-segera {
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        padding: 1px 7px;
        border-radius: 20px;
        background: #FFF4DE;
        color: #B37E00;
        margin-left: 6px;
        vertical-align: middle;
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

    <div class="page-header d-flex justify-content-between align-items-center mb-6">
        <div>
            <h1 class="font-weight-bolder mb-1">Pendapatan</h1>
            <span class="text-muted-light font-weight-bold">Data pendapatan rumah sakit</span>
        </div>

        <a href="{{ route('divisi.keuangan.pendapatan.pdf', ['division' => $division->slug, 'tab' => $tab, 'awal' => $awal, 'akhir' => $akhir]) }}" target="_blank" class="btn font-weight-bold px-5" style="background-color: #000; color: #fff; border: none;">
            Download PDF
        </a>
    </div>

    <div class="subtab-pendapatan">
        <div class="subtab-track">
            @foreach ($tabList as $kunci => $label)
                <a href="{{ route('divisi.keuangan.pendapatan', ['division' => $division->slug, 'tab' => $kunci, 'awal' => $awal, 'akhir' => $akhir]) }}" class="subtab-item {{ $tab === $kunci ? 'aktif' : '' }}">
                    {{ $label }}
                    @if (in_array($kunci, $tabBelumTersedia, true))
                        <span class="badge-segera">segera</span>
                    @endif
                </a>
            @endforeach
        </div>
    </div>

    <form method="GET" class="filter-card d-flex align-items-end flex-wrap p-4 mb-6">
        <input type="hidden" name="tab" value="{{ $tab }}">

        <div class="form-group mb-0 mr-4">
            <label class="font-weight-bold mb-1 font-size-sm text-muted">Dari Tanggal</label>
            <input type="date" name="awal" value="{{ $awal }}" class="form-control form-control-solid" style="width: 170px;">
        </div>

        <div class="form-group mb-0 mr-4">
            <label class="font-weight-bold mb-1 font-size-sm text-muted">Sampai Tanggal</label>
            <input type="date" name="akhir" value="{{ $akhir }}" class="form-control form-control-solid" style="width: 170px;">
        </div>

        <button type="submit" class="btn btn-primary font-weight-bold px-6">Terapkan</button>
    </form>

    <div class="row mb-2">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="stat-value text-dark">
                        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                    </div>
                    <div class="stat-label">
                        Total Pendapatan &mdash; {{ $tabList[$tab] }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card modern-card mb-6">
        <div class="card-body p-5">
            <h3 class="font-weight-bolder text-dark mb-4">
                Data Pendapatan &mdash; {{ $tabList[$tab] }}
            </h3>

            @if (in_array($tab, $tabBelumTersedia, true))

                <div class="text-center text-muted py-5">
                    Data untuk tampilan <strong>{{ $tabList[$tab] }}</strong> belum tersedia.<br>
                    Tabnya sudah siap, tinggal menyusul data cara bayar / poliklinik / ruangan-nya nanti.
                </div>

            @else

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
                                    <td>{{ $item->kategori->nama_kategori ?? '-' }}</td>
                                    <td>{{ $item->unitKerja->nama_unit ?? '-' }}</td>
                                    <td>{{ $item->keterangan ?? '-' }}</td>
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

            @endif

        </div>
    </div>

</div>
@endsection