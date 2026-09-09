@extends('layouts.dashboard')

@section('title', 'Laboratorium')

@push('styles')
@include('partials.dashboard-styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #fafafa 0%, #646565 100%);
        width: 100%;
        border-radius: 18px;
        padding: 28px 32px;
        color: #000;
        box-shadow: 0 10px 30px rgba(105,147,255,.25);
    }
    .page-header h1 { color: #000; }
    .page-header .text-muted-light { color: rgba(255,255,255,.8) !important; }
</style>
@endpush


@section('content')
<div class="container-fluid px-6 py-6">

    @include('partials.submenu-layanan')

    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
            <div>
                <h1 class="font-weight-bolder mb-1">Laboratorium</h1>
                <span class=" font-weight-bold">Daftar pemeriksaan laboratorium pasien</span>
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
                    <a href="{{ route('divisi.layanan.laboratorium', $division->slug) }}" class="btn btn-light font-weight-bold px-4">Reset</a>
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
                        @forelse ($laboratorium as $l)
                        <tr>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $l->kunjungan->pasien->nama ?? '-' }}</div>
                                <div class="text-muted font-size-sm">{{ $l->kunjungan->pasien->no_rm ?? '-' }}</div>
                            </td>
                            <td class="font-weight-bold">{{ $l->jenis_pemeriksaan }}</td>
                            <td>{{ $l->waktu_periksa->format('d M Y, H:i') }}</td>
                            <td>{{ $l->petugas->name ?? '-' }}</td>
                            <td>{{ $l->hasil ?? '-' }}</td>
                            <td>
                                @php
                                    $warnaStatus = match($l->status) {
                                        'menunggu' => ['bg' => '#FFF6E0', 'text' => '#FFA800', 'label' => 'Menunggu'],
                                        'diproses' => ['bg' => '#EEF3FF', 'text' => '#6993FF', 'label' => 'Diproses'],
                                        'selesai' => ['bg' => '#E8FFF3', 'text' => '#1BC5BD', 'label' => 'Selesai'],
                                    };
                                @endphp
                                <span class="badge-modern" style="background:{{ $warnaStatus['bg'] }}; color:{{ $warnaStatus['text'] }};">{{ $warnaStatus['label'] }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-6">Tidak ada data laboratorium ditemukan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $laboratorium->links() }}
            </div>

        </div>
    </div>

</div>
@endsection