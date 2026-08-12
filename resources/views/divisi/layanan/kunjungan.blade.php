@extends('layouts.dashboard')

@section('title', 'Kunjungan')

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
            <h1 class="font-weight-bolder text-dark mb-0">Kunjungan</h1>
            <span class="text-muted font-weight-bold">Daftar seluruh kunjungan pasien rawat jalan, rawat inap, dan IGD</span>
        </div>
        <x-modal-pdf id="modalPdfKunjungan" title="Data Kunjungan" :action="route('divisi.layanan.kunjungan.pdf', $division->slug)" />
    </div>

    @include('partials.submenu-layanan')

    {{-- Ringkasan kecil --}}
    <div class="row mb-2">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value text-dark">{{ number_format($ringkasan['total']) }}</div>
                <div class="stat-label">Total Kunjungan</div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value" style="color:#FFA800;">{{ number_format($ringkasan['menunggu']) }}</div>
                <div class="stat-label">Sedang Menunggu</div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value" style="color:#1BC5BD;">{{ number_format($ringkasan['selesai']) }}</div>
                <div class="stat-label">Selesai Dilayani</div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value" style="color:#F64E60;">{{ number_format($ringkasan['igd']) }}</div>
                <div class="stat-label">Kunjungan IGD</div>
            </div></div>
        </div>
    </div>

    <div class="card modern-card">
        <div class="card-body p-5">

            {{-- Filter --}}
            <form method="GET" class="d-flex flex-wrap align-items-end mb-5" style="gap: 12px;">
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Cari</label>
                    <input type="text" name="cari" value="{{ $cari }}" class="form-control form-control-solid" style="width: 220px;" placeholder="No kunjungan / nama / no RM">
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Dari</label>
                    <input type="date" name="awal" value="{{ $awal->format('Y-m-d') }}" class="form-control form-control-solid" style="width: 150px;">
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Sampai</label>
                    <input type="date" name="akhir" value="{{ $akhir->format('Y-m-d') }}" class="form-control form-control-solid" style="width: 150px;">
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Jenis</label>
                    <select name="jenis" class="form-control form-control-solid" style="width: 160px;">
                        <option value="">Semua Jenis</option>
                        <option value="rawat_jalan" @selected($jenis == 'rawat_jalan')>Rawat Jalan</option>
                        <option value="rawat_inap" @selected($jenis == 'rawat_inap')>Rawat Inap</option>
                        <option value="igd" @selected($jenis == 'igd')>IGD</option>
                    </select>
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Status</label>
                    <select name="status" class="form-control form-control-solid" style="width: 150px;">
                        <option value="">Semua Status</option>
                        <option value="menunggu" @selected($status == 'menunggu')>Menunggu</option>
                        <option value="dilayani" @selected($status == 'dilayani')>Dilayani</option>
                        <option value="selesai" @selected($status == 'selesai')>Selesai</option>
                        <option value="batal" @selected($status == 'batal')>Batal</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary font-weight-bold px-6">Terapkan</button>
                @if ($cari || $jenis || $status)
                    <a href="{{ route('divisi.layanan.kunjungan', $division->slug) }}" class="btn btn-light font-weight-bold px-4">Reset</a>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>No. Kunjungan</th>
                            <th>Pasien</th>
                            <th>Poli</th>
                            <th>Dokter</th>
                            <th>Jenis</th>
                            <th>Waktu Daftar</th>
                            <th>Status</th>
                            <th>Operator</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kunjungan as $k)
                        <tr>
                            <td class="font-weight-bold">{{ $k->no_kunjungan }}</td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $k->pasien->nama ?? '-' }}</div>
                                <div class="text-muted font-size-sm">{{ $k->pasien->no_rm ?? '-' }}</div>
                            </td>
                            <td>{{ $k->poli->nama_poli ?? '-' }}</td>
                            <td>{{ $k->dokter->nama ?? '-' }}</td>
                            <td>
                                @php
                                    $warnaJenis = match($k->jenis_kunjungan) {
                                        'rawat_inap' => ['bg' => '#FFE9EA', 'text' => '#F64E60'],
                                        'igd' => ['bg' => '#FFF6E0', 'text' => '#FFA800'],
                                        default => ['bg' => '#EEF3FF', 'text' => '#6993FF'],
                                    };
                                    $labelJenis = match($k->jenis_kunjungan) {
                                        'rawat_inap' => 'Rawat Inap',
                                        'igd' => 'IGD',
                                        default => 'Rawat Jalan',
                                    };
                                @endphp
                                <span class="badge-modern" style="background:{{ $warnaJenis['bg'] }}; color:{{ $warnaJenis['text'] }};">{{ $labelJenis }}</span>
                            </td>
                            <td>{{ $k->waktu_daftar->format('d M Y, H:i') }}</td>
                            <td>
                                @php
                                    $warnaStatus = match($k->status) {
                                        'selesai' => ['bg' => '#E8FFF3', 'text' => '#1BC5BD'],
                                        'dilayani' => ['bg' => '#EEF3FF', 'text' => '#6993FF'],
                                        'batal' => ['bg' => '#FFE9EA', 'text' => '#F64E60'],
                                        default => ['bg' => '#FFF6E0', 'text' => '#FFA800'],
                                    };
                                @endphp
                                <span class="badge-modern" style="background:{{ $warnaStatus['bg'] }}; color:{{ $warnaStatus['text'] }};">{{ ucfirst($k->status) }}</span>
                            </td>
                            <td>{{ $k->operator->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-6">Tidak ada data kunjungan ditemukan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $kunjungan->links() }}
            </div>

        </div>
    </div>

</div>
@endsection