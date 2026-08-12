@extends('layouts.dashboard')

@section('title', 'Radiologi')

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
    
    .page-header {
        background: linear-gradient(135deg, #6f0000 0%, #f80404 100%);
        width: 100%;
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

    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
            <div>
                <h1 class="font-weight-bolder mb-1">Radiologi</h1>
                <span class="text-muted-light font-weight-bold">Daftar pemeriksaan radiologi pasien</span>
            </div>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="row mb-2">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value text-dark">{{ $ringkasan['total'] }}</div>
                <div class="stat-label">Total Pemeriksaan</div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value" style="color:#FFA800;">{{ $ringkasan['menunggu'] }}</div>
                <div class="stat-label">Menunggu</div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value" style="color:#6993FF;">{{ $ringkasan['diproses'] }}</div>
                <div class="stat-label">Diproses</div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value" style="color:#1BC5BD;">{{ $ringkasan['selesai'] }}</div>
                <div class="stat-label">Selesai</div>
            </div></div>
        </div>
    </div>

    <div class="card modern-card">
        <div class="card-body p-5">

            {{-- Filter --}}
            <form method="GET" class="d-flex flex-wrap align-items-end mb-5" style="gap: 12px;">
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Cari</label>
                    <input type="text" name="cari" value="{{ $cari }}" class="form-control form-control-solid" style="width: 240px;" placeholder="Jenis pemeriksaan / nama pasien / no RM">
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Status</label>
                    <select name="status" class="form-control form-control-solid" style="width: 170px;">
                        <option value="">Semua Status</option>
                        <option value="menunggu" @selected($status == 'menunggu')>Menunggu</option>
                        <option value="diproses" @selected($status == 'diproses')>Diproses</option>
                        <option value="selesai" @selected($status == 'selesai')>Selesai</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary font-weight-bold px-6">Terapkan</button>
                @if ($cari || $status)
                    <a href="{{ route('divisi.layanan.radiologi', $division->slug) }}" class="btn btn-light font-weight-bold px-4">Reset</a>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Pasien</th>
                            <th>Jenis Pemeriksaan</th>
                            <th>Waktu Periksa</th>
                            <th>Petugas</th>
                            <th>Hasil</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($radiologi as $r)
                        <tr>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $r->kunjungan->pasien->nama ?? '-' }}</div>
                                <div class="text-muted font-size-sm">{{ $r->kunjungan->pasien->no_rm ?? '-' }}</div>
                            </td>
                            <td class="font-weight-bold">{{ $r->jenis_pemeriksaan }}</td>
                            <td>{{ $r->waktu_periksa->format('d M Y, H:i') }}</td>
                            <td>{{ $r->petugas->name ?? '-' }}</td>
                            <td>{{ $r->hasil ?? '-' }}</td>
                            <td>
                                @php
                                    $warnaStatus = match($r->status) {
                                        'menunggu' => ['bg' => '#FFF6E0', 'text' => '#FFA800', 'label' => 'Menunggu'],
                                        'diproses' => ['bg' => '#EEF3FF', 'text' => '#6993FF', 'label' => 'Diproses'],
                                        'selesai' => ['bg' => '#E8FFF3', 'text' => '#1BC5BD', 'label' => 'Selesai'],
                                    };
                                @endphp
                                <span class="badge-modern" style="background:{{ $warnaStatus['bg'] }}; color:{{ $warnaStatus['text'] }};">{{ $warnaStatus['label'] }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-6">Tidak ada data radiologi ditemukan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $radiologi->links() }}
            </div>

        </div>
    </div>

</div>
@endsection