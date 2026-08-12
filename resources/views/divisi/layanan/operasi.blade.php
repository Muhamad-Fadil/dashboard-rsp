@extends('layouts.dashboard')

@section('title', 'Operasi')

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
            <h1 class="font-weight-bolder text-dark mb-0">Operasi</h1>
            <span class="text-muted font-weight-bold">Jadwal dan riwayat tindakan operasi</span>
        </div>
    </div>

    @include('partials.submenu-layanan')

    {{-- Ringkasan --}}
    <div class="row mb-2">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value text-dark">{{ $ringkasan['total'] }}</div>
                <div class="stat-label">Total Operasi</div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value" style="color:#FFA800;">{{ $ringkasan['dijadwalkan'] }}</div>
                <div class="stat-label">Dijadwalkan</div>
            </div></div>
        </div>
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value" style="color:#F64E60;">{{ $ringkasan['berlangsung'] }}</div>
                <div class="stat-label">Sedang Berlangsung</div>
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
                    <input type="text" name="cari" value="{{ $cari }}" class="form-control form-control-solid" style="width: 240px;" placeholder="Jenis operasi / nama pasien / no RM">
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Status</label>
                    <select name="status" class="form-control form-control-solid" style="width: 170px;">
                        <option value="">Semua Status</option>
                        <option value="dijadwalkan" @selected($status == 'dijadwalkan')>Dijadwalkan</option>
                        <option value="berlangsung" @selected($status == 'berlangsung')>Berlangsung</option>
                        <option value="selesai" @selected($status == 'selesai')>Selesai</option>
                        <option value="batal" @selected($status == 'batal')>Batal</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary font-weight-bold px-6">Terapkan</button>
                @if ($cari || $status)
                    <a href="{{ route('divisi.layanan.operasi', $division->slug) }}" class="btn btn-light font-weight-bold px-4">Reset</a>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Pasien</th>
                            <th>Jenis Operasi</th>
                            <th>Dokter Bedah</th>
                            <th>Ruang</th>
                            <th>Waktu Mulai</th>
                            <th>Waktu Selesai</th>
                            <th>Durasi</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($operasi as $o)
                        <tr>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $o->kunjungan->pasien->nama ?? '-' }}</div>
                                <div class="text-muted font-size-sm">{{ $o->kunjungan->pasien->no_rm ?? '-' }}</div>
                            </td>
                            <td class="font-weight-bold">{{ $o->jenis_operasi }}</td>
                            <td>{{ $o->dokter->nama ?? '-' }}</td>
                            <td>{{ $o->ruang_operasi ?? '-' }}</td>
                            <td>{{ $o->waktu_mulai->format('d M Y, H:i') }}</td>
                            <td>{{ $o->waktu_selesai ? $o->waktu_selesai->format('d M Y, H:i') : '-' }}</td>
                            <td>
                                @if ($o->waktu_selesai)
                                    {{ $o->waktu_mulai->diffInMinutes($o->waktu_selesai) }} menit
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $warnaStatus = match($o->status) {
                                        'dijadwalkan' => ['bg' => '#FFF6E0', 'text' => '#FFA800', 'label' => 'Dijadwalkan'],
                                        'berlangsung' => ['bg' => '#FFE9EA', 'text' => '#F64E60', 'label' => 'Berlangsung'],
                                        'selesai' => ['bg' => '#E8FFF3', 'text' => '#1BC5BD', 'label' => 'Selesai'],
                                        'batal' => ['bg' => '#F3F6F9', 'text' => '#464E5F', 'label' => 'Batal'],
                                    };
                                @endphp
                                <span class="badge-modern" style="background:{{ $warnaStatus['bg'] }}; color:{{ $warnaStatus['text'] }};">{{ $warnaStatus['label'] }}</span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-6">Tidak ada data operasi ditemukan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $operasi->links() }}
            </div>

        </div>
    </div>

</div>
@endsection