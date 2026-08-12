@extends('layouts.dashboard')

@section('title', 'Rawat Inap')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; }
    .modern-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); }
    .stat-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); height: 100%; }
    .stat-value { font-size: 26px; font-weight: 800; line-height: 1.1; }
    .stat-label { font-size: 13px; font-weight: 600; color: #7e8299; margin-top: 4px; }
    .table-modern thead th { border: none; color: #a1a5b7; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
    .table-modern td { border-color: #f1f1f4; vertical-align: middle; }
    .table-modern tbody tr:hover { background: #f9f9fb; }
    .badge-modern { border-radius: 20px; padding: 6px 14px; font-weight: 600; font-size: 12px; }
    nav[role="navigation"] svg { width: 16px !important; height: 16px !important; }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <div>
            <h1 class="font-weight-bolder text-dark mb-0">Rawat Inap</h1>
            <span class="text-muted font-weight-bold">Daftar pasien rawat inap, kamar/bed, dan lama perawatan</span>
        </div>
        <x-modal-pdf id="modalPdfRawatInap" title="Data Rawat Inap" :action="route('divisi.layanan.rawat-inap.pdf', $division->slug)">
            <div class="form-group mb-0">
                <label class="font-weight-bold font-size-sm">Bangsal (opsional)</label>
                <select name="bangsal" class="form-control form-control-solid">
                    <option value="">Semua Bangsal</option>
                    @foreach ($daftarBangsal as $b)
                        <option value="{{ $b }}">{{ $b }}</option>
                    @endforeach
                </select>
            </div>
        </x-modal-pdf>
    </div>

    @include('partials.submenu-layanan')

    {{-- Ringkasan --}}
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

            {{-- Filter --}}
            <form method="GET" class="d-flex flex-wrap align-items-end mb-5" style="gap: 12px;">
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Cari</label>
                    <input type="text" name="cari" value="{{ $cari }}" class="form-control form-control-solid" style="width: 220px;" placeholder="Nama pasien / no RM / no kunjungan">
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Bangsal</label>
                    <select name="bangsal" class="form-control form-control-solid" style="width: 170px;">
                        <option value="">Semua Bangsal</option>
                        @foreach ($daftarBangsal as $b)
                            <option value="{{ $b }}" @selected($bangsal == $b)>{{ $b }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group mb-0">
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
                @if ($cari || $status || $bangsal)
                    <a href="{{ route('divisi.layanan.rawat-inap', $division->slug) }}" class="btn btn-light font-weight-bold px-4">Reset</a>
                @endif
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
                            <td>{{ $ri->dokter->nama ?? '-' }}</td>
                            <td>{{ $ri->tanggal_masuk->format('d M Y, H:i') }}</td>
                            <td>{{ $ri->tanggal_keluar ? $ri->tanggal_keluar->format('d M Y, H:i') : '-' }}</td>
                            <td>
                                @if ($ri->tanggal_keluar)
                                    {{ $ri->lamaRawatHari() }} hari
                                @else
                                    <span class="text-muted">Berjalan ({{ $ri->tanggal_masuk->diffInDays(now()) }} hari)</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $warnaStatus = match($ri->status) {
                                        'dirawat' => ['bg' => '#FFE9EA', 'text' => '#F64E60', 'label' => 'Dirawat'],
                                        'pulang' => ['bg' => '#E8FFF3', 'text' => '#1BC5BD', 'label' => 'Pulang'],
                                        'rujuk' => ['bg' => '#FFF6E0', 'text' => '#FFA800', 'label' => 'Dirujuk'],
                                        'meninggal' => ['bg' => '#F3F6F9', 'text' => '#464E5F', 'label' => 'Meninggal'],
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