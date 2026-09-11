@extends('layouts.dashboard')

@section('title', 'Rawat Jalan')

@push('styles')
@include('partials.dashboard-styles')
@endpush

@section('content')
<div class="container-fluid px-6 py-6">

    @include('partials.submenu-layanan')

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6" style="background: linear-gradient(135deg, #6993FF 0%, #4D6FE0 100%); border-radius:18px; padding:28px 32px; color:#fff;">
        <div>
            <h1 class="font-weight-bolder mb-1">Rawat Jalan</h1>
            <span class="text-muted-light font-weight-bold">Daftar kunjungan rawat jalan pasien</span>
        </div>
        <x-modal-pdf id="modalPdfRawatJalan" title="Data Rawat Jalan" :action="route('divisi.layanan.rawat-jalan.pdf', $division->slug)" />
    </div>

    <div class="row mb-2">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value text-dark">{{ number_format($ringkasan['total']) }}</div>
                <div class="stat-label">Total Rawat Jalan</div>
            </div></div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value" style="color:#1BC5BD;">{{ number_format($ringkasan['selesai']) }}</div>
                <div class="stat-label">Selesai Berobat</div>
            </div></div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value" style="color:#F64E60;">{{ number_format($ringkasan['batal']) }}</div>
                <div class="stat-label">Batal Berobat</div>
            </div></div>
        </div>
    </div>

    <div class="card modern-card">
        <div class="card-body p-5">
            <form method="GET" class="d-flex flex-wrap align-items-end mb-5" style="gap: 12px;">
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Cari</label>
                    <input type="text" name="cari" value="{{ $cari }}" class="form-control form-control-solid" style="width: 220px;" placeholder="No kunjungan / nama / no RM">
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Dari Tanggal</label>
                    <input type="date" name="awal" value="{{ $awal->format('Y-m-d') }}" class="form-control form-control-solid" style="width: 150px;">
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Sampai Tanggal</label>
                    <input type="date" name="akhir" value="{{ $akhir->format('Y-m-d') }}" class="form-control form-control-solid" style="width: 150px;">
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
                @if ($cari || $status)
                    <a href="{{ route('divisi.layanan.rawat-jalan', $division->slug) }}" class="btn btn-light font-weight-bold px-4">Reset</a>
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
                            <th>Tipe Pembayaran</th>
                            <th>Waktu Daftar</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kunjungan as $k)
                        <tr>
                            <td class="font-weight-bold nowrap">{{ $k->no_kunjungan }}</td>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $k->pasien->nama ?? '-' }}</div>
                                <div class="text-muted font-size-sm">{{ $k->pasien->no_rm ?? '-' }}</div>
                            </td>
                            <td>{{ $k->poli->nama_poli ?? '-' }}</td>
                            <td>{{ $k->dokter->nama ?? '-' }}</td>
                            <td>
                                @php
                                    $warnaTipe = match($k->pasien->jenisPembayaran->kode ?? null) {
                                        'bpjs' => ['bg' => '#E8FFF3', 'text' => '#1BC5BD'],
                                        'asuransi' => ['bg' => '#F1E9FF', 'text' => '#8950FC'],
                                        default => ['bg' => '#FFF6E0', 'text' => '#FFA800'],
                                    };
                                @endphp
                                <span class="badge-modern" style="background:{{ $warnaTipe['bg'] }}; color:{{ $warnaTipe['text'] }};">
                                    {{ $k->pasien->jenisPembayaran->nilai ?? '-' }}
                                </span>
                            </td>
                            <td class="nowrap">{{ $k->waktu_daftar->format('d M Y, H:i') }}</td>
                            <td class="nowrap">
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
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-6">Tidak ada data ditemukan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">{{ $kunjungan->links() }}</div>
        </div>
    </div>

</div>
@endsection