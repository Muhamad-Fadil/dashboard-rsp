@extends('layouts.dashboard')

@section('title', 'Rawat Inap')

@push('styles')
@include('partials.dashboard-styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #845401 0%, #eae42c 100%);
        width: 100%;
        border-radius: 18px;
        padding: 28px 32px;
        color: #fff;
        box-shadow: 0 10px 30px rgba(105,147,255,.25);
    }
    .page-header h1 { color: #fff; }
    .page-header .text-muted-light { color: rgba(255,255,255,.8) !important; }

    .patient-layout { background: #f8faff; }

    .modern-card {
        border-radius: 18px;
        border: 1px solid rgba(105,147,255,.14);
        box-shadow: 0 10px 30px rgba(0,0,0,.05);
        background: #ffffff;
    }

    .stat-card {
        border-radius: 14px;
        border: 1px solid rgba(105,147,255,.14);
        box-shadow: 0 8px 24px rgba(0,0,0,.04);
        background: #fff;
    }

    .stat-card .stat-value {
        font-size: 26px;
        font-weight: 800;
        color: #4D6FE0;
        line-height: 1.2;
    }

    .stat-card .stat-label {
        color: #6c7389;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .filter-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid rgba(105,147,255,.12);
        box-shadow: 0 8px 24px rgba(0,0,0,.04);
        display: flex;
        align-items: end;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }

    .filter-card .form-group {
        min-width: 170px;
    }

    .filter-card label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .03em;
        color: #6c7389;
        text-transform: uppercase;
    }

    .filter-card .form-control {
        min-height: 40px;
        border-radius: 10px;
        border-color: #d7dce8;
        font-size: 12px;
    }

    .table-modern {
        border-collapse: separate;
        border-spacing: 0;
        font-size: 12px;
        color: #464E5F;
    }

    .table-modern thead th {
        background: #eef4ff;
        color: #4D6FE0;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        padding: 14px 16px;
        border-bottom: 2px solid #dbe7ff;
    }

    .table-modern tbody td {
        padding: 14px 16px;
        border-bottom: 1px solid #eef1f7;
        vertical-align: middle;
    }

    .table-modern tbody tr {
        background: #fff;
        transition: background .2s ease;
    }

    .table-modern tbody tr:hover {
        background: #f7faff;
    }

    .badge-modern {
        display: inline-flex;
        align-items: center;
        padding: 5px 10px;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .04em;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6 patient-layout">

    @include('partials.submenu-layanan')

    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
            <div>
                <h1 class="font-weight-bolder mb-1">Rawat Inap</h1>
                <span class="text-muted-light font-weight-bold">Daftar pasien rawat inap, kamar/bed, dan lama perawatan</span>
                <div class="text-muted-light font-size-sm mt-2">Periode {{ $awal->format('d M Y') }} — {{ $akhir->format('d M Y') }}</div>
            </div>
            <x-modal-pdf id="modalPdfRawatInap" title="Data Rawat Inap" :action="route('divisi.layanan.rawat-inap.pdf', $division->slug)" />
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value" style="color:#F64E60;">{{ $ringkasan['sedang_dirawat'] }}</div>
                <div class="stat-label">Sedang Dirawat Saat Ini</div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value text-dark">{{ $ringkasan['bed_terisi'] }}</div>
                <div class="stat-label">Bed Terisi</div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value" style="color:#1BC5BD;">{{ $ringkasan['bed_tersedia'] }}</div>
                <div class="stat-label">Bed Tersedia</div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value text-dark">{{ $ringkasan['total_bed'] }}</div>
                <div class="stat-label">Total Bed</div>
            </div></div>
        </div>
    </div>

    <div class="card modern-card">
        <div class="card-body p-5">
            <form method="GET" class="filter-card d-flex align-items-end flex-wrap p-4 mb-5">
                <div class="form-group mb-0 mr-4">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Cari</label>
                    <input type="text" name="cari" value="{{ $cari }}" class="form-control form-control-solid" style="width: 260px;" placeholder="Nama pasien / no RM / no kunjungan">
                </div>
                <div class="form-group mb-0 mr-4">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Dari Tanggal</label>
                    <input type="date" name="awal" value="{{ $awal->format('Y-m-d') }}" class="form-control form-control-solid" style="width: 160px;">
                </div>
                <div class="form-group mb-0 mr-4">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Sampai Tanggal</label>
                    <input type="date" name="akhir" value="{{ $akhir->format('Y-m-d') }}" class="form-control form-control-solid" style="width: 160px;">
                </div>
                <div class="form-group mb-0 mr-4">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Bangsal</label>
                    <select name="bangsal" class="form-control form-control-solid" style="width: 170px;">
                        <option value="">Semua Bangsal</option>
                        @foreach ($daftarBangsal as $b)
                            <option value="{{ $b }}" @selected($bangsal == $b)>{{ $b }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-0 mr-4">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Status</label>
                    <select name="status" class="form-control form-control-solid" style="width: 160px;">
                        <option value="">Semua Status</option>
                        <option value="dirawat" @selected($status == 'dirawat')>Masih Dirawat</option>
                        <option value="pulang" @selected($status == 'pulang')>Pulang</option>
                        <option value="rujuk" @selected($status == 'rujuk')>Dirujuk</option>
                        <option value="meninggal" @selected($status == 'meninggal')>Meninggal</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary font-weight-bold px-6">Terapkan</button>
                <a href="{{ route('divisi.layanan.rawat-inap', $division->slug) }}" class="btn btn-light font-weight-bold px-6 ml-2">Reset</a>
            </form>

            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Pasien</th>
                            <th>No. Kunjungan</th>
                            <th>Bangsal / Kamar / Bed</th>
                            <th>Dokter</th>
                            <th>Tgl Masuk</th>
                            <th>Tgl Keluar</th>
                            <th>Lama Rawat</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rawatInap as $ri)
                        <tr>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $ri->kunjungan->pasien->nama ?? '-' }}</div>
                                <div class="text-muted font-size-sm">{{ $ri->kunjungan->pasien->no_rm ?? '-' }}</div>
                            </td>
                            <td>{{ $ri->kunjungan->no_kunjungan ?? '-' }}</td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $ri->bed->kamar->nama_bangsal ?? '-' }}</div>
                                <div class="text-muted font-size-sm">Kamar {{ $ri->bed->kamar->nomor_kamar ?? '-' }} / Bed {{ $ri->bed->nomor_bed ?? '-' }}</div>
                            </td>
                            <td class="text-muted">{{ $ri->dokter->nama ?? 'Belum tercatat' }}</td>
                            <td>{{ $ri->tanggal_masuk ? $ri->tanggal_masuk->format('d M Y, H:i') : '-' }}</td>
                            <td>{{ $ri->tanggal_keluar ? $ri->tanggal_keluar->format('d M Y, H:i') : '-' }}</td>
                            <td>
                                @if ($ri->tanggal_keluar)
                                    {{ $ri->lamaRawatHari() }} hari
                                @else
                                    <span class="text-muted">Berjalan ({{ $ri->tanggal_masuk?->diffInDays(now()) ?? 0 }} hari)</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $warnaStatus = match($ri->status) {
                                        'dirawat' => ['bg' => '#FFE9EA', 'text' => '#F64E60', 'label' => 'Dirawat'],
                                        'pulang' => ['bg' => '#E8FFF3', 'text' => '#1BC5BD', 'label' => 'Pulang'],
                                        'rujuk' => ['bg' => '#FFF6E0', 'text' => '#FFA800', 'label' => 'Dirujuk'],
                                        'meninggal' => ['bg' => '#F3F6F9', 'text' => '#464E5F', 'label' => 'Meninggal'],
                                        default => ['bg' => '#FFF6E0', 'text' => '#FFA800', 'label' => ucfirst($ri->status ?? 'Belum ada')],
                                    };
                                @endphp
                                <span class="badge-modern" style="background:{{ $warnaStatus['bg'] }}; color:{{ $warnaStatus['text'] }};">{{ $warnaStatus['label'] }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-6">Tidak ada data rawat inap ditemukan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $rawatInap->links() }}
            </div>

        </div>
    </div>

</div>
@endsection