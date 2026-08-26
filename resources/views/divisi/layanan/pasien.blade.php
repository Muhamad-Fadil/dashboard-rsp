@extends('layouts.dashboard')

@section('title', 'Data Pasien')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; }
    .modern-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); }
    .table-modern thead th { border: none; color: #a1a5b7; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
    .table-modern td { border-color: #f1f1f4; vertical-align: middle; }
    .table-modern tbody tr:hover { background: #f9f9fb; }
    .badge-modern { border-radius: 20px; padding: 6px 14px; font-weight: 600; font-size: 12px; }
    .avatar-circle {
        width: 40px; height: 40px; border-radius: 50%;
        background: #EEF3FF; color: #6993FF;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 15px;
    }

        .table-modern th { white-space: nowrap; }
    .table-modern td.nowrap { white-space: nowrap; }
    .pasien-cell { max-width: 220px; }
    .pasien-alamat {
        font-size: 12px; color: #a1a5b7;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        max-width: 220px; display: block;
    }

    .btn-expand { background: #f4f6f9; border: none; border-radius: 8px; padding: 6px 12px; font-weight: 600; font-size: 12px; color: #6993FF; cursor: pointer; }
    .btn-expand:hover { background: #EEF3FF; }
    .row-detail { display: none; background: #f9f9fb; }
    .row-detail.show { display: table-row; }
    .table-obat { width: 100%; font-size: 13px; }
    .table-obat th { text-align: left; color: #a1a5b7; font-weight: 600; padding: 4px 12px; }
    .table-obat td { padding: 6px 12px; }

    .page-header {
        background: linear-gradient(135deg, #005d21 0%, #09c5e6 100%);
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
<div class="container-fluid px-6 py-6" style="">

    @include('partials.submenu-layanan')

    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
            <div>
                <h1 class="font-weight-bolder mb-1">Data Pasien</h1>
                <span class="text-muted-light font-weight-bold">Seluruh data pasien terdaftar di RSP Goenawan Cisarua</span>
            </div>
            <x-modal-pdf id="modalPdfPasien" title="Data Pasien" :action="route('divisi.layanan.pasien.pdf', $division->slug)" />
        </div>
    </div>

    <div class="card modern-card">
        <div class="card-body p-5">

            <form method="GET" class="mb-5">
                <div class="input-group" style="max-width: 420px;">
                    <div class="input-group-prepend">
                        <span class="input-group-text bg-light border-0 text-muted">🔍</span>
                    </div>
                    <input type="text" name="cari" value="{{ $cari }}" class="form-control border-0 bg-light"
                           placeholder="Cari nama, no RM, no registrasi, atau NIK...">
                    @if ($cari)
                        <div class="input-group-append">
                            <a href="{{ route('divisi.layanan.pasien', $division->slug) }}" class="btn btn-light border-0 text-muted">
                                &times;
                            </a>
                        </div>
                    @endif
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Pasien</th>
                            <th>No. RM</th>
                            <th>No. Registrasi</th>
                            <th>L/P</th>
                            <th>Usia</th>
                            <th>Tipe Pasien</th>
                            <th>Riwayat Kunjungan</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pasien as $p)
                        @php
                            $jumlahPerJenis = $p->kunjungan->groupBy('jenis_kunjungan')->map->count();
                            $labelJenis = ['rawat_jalan' => 'Rawat Jalan', 'rawat_inap' => 'Rawat Inap', 'igd' => 'IGD'];
                            $warnaJenis = [
                                'rawat_jalan' => ['bg' => '#EEF3FF', 'text' => '#6993FF'],
                                'rawat_inap' => ['bg' => '#FFE9EA', 'text' => '#F64E60'],
                                'igd' => ['bg' => '#FFF6E0', 'text' => '#FFA800'],
                            ];
                        @endphp
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle mr-3">{{ strtoupper(substr($p->nama, 0, 1)) }}</div>
                                    <div>
                                        <div class="font-weight-bold text-dark">{{ $p->nama }}</div>
                                        <div class="text-muted font-size-sm">{{ $p->no_hp ?? '-' }}</div>
                                        <span class="pasien-alamat" title="{{ $p->alamat }}">
                                            @if ($p->wilayah)
                                                Kec. {{ $p->wilayah->nama_kecamatan }} ({{ $p->wilayah->kabupaten_kota === 'kota' ? 'Kota' : 'Kab.' }} Bogor)
                                            @else
                                                {{ $p->alamat ?? '-' }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="font-weight-bold nowrap">{{ $p->no_rm }}</td>
                            <td class="nowrap">{{ $p->no_registrasi ?? '-' }}</td>
                            <td class="nowrap">{{ $p->jenis_kelamin }}</td>
                            <td class="nowrap">{{ $p->tanggal_lahir ? \Carbon\Carbon::parse($p->tanggal_lahir)->age . ' th' : '-' }}</td>
                            <td>
                                @php
                                    $warnaTipe = match($p->jenisPembayaran->kode ?? null) {
                                        'bpjs' => ['bg' => '#E8FFF3', 'text' => '#1BC5BD'],
                                        'asuransi' => ['bg' => '#F1E9FF', 'text' => '#8950FC'],
                                        default => ['bg' => '#FFF6E0', 'text' => '#FFA800'],
                                    };
                                @endphp
                                <span class="badge-modern" style="background:{{ $warnaTipe['bg'] }}; color:{{ $warnaTipe['text'] }};">
                                    {{ $p->jenisPembayaran->nilai ?? 'Belum diisi' }}
                                </span>
                            </td>
                            <td>
                                @forelse ($jumlahPerJenis as $jenis => $jumlah)
                                    <span class="badge-modern mr-1" style="background:{{ $warnaJenis[$jenis]['bg'] }}; color:{{ $warnaJenis[$jenis]['text'] }};">
                                        {{ $jumlah }}x {{ $labelJenis[$jenis] }}
                                    </span>
                                @empty
                                    <span class="text-muted font-size-sm">Belum pernah berobat</span>
                                @endforelse
                            </td>
                            <td class="nowrap">
                                @if ($p->kunjungan->isNotEmpty())
                                    <button type="button" class="btn-expand" onclick="toggleRiwayat({{ $p->id }})">Lihat Riwayat</button>
                                @endif
                            </td>
                        </tr>
                        @if ($p->kunjungan->isNotEmpty())
                        <tr class="row-detail" id="riwayat-{{ $p->id }}">
                            <td colspan="8">
                                <table class="table-obat">
                                    <thead>
                                        <tr>
                                            <th>No. Kunjungan</th>
                                            <th>Jenis</th>
                                            <th>Poli</th>
                                            <th>Dokter</th>
                                            <th>Diagnosa</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($p->kunjungan as $k)
                                        <tr>
                                            <td class="font-weight-bold">{{ $k->no_kunjungan }}</td>
                                            <td>
                                                <span class="badge-modern" style="background:{{ $warnaJenis[$k->jenis_kunjungan]['bg'] }}; color:{{ $warnaJenis[$k->jenis_kunjungan]['text'] }};">
                                                    {{ $labelJenis[$k->jenis_kunjungan] }}
                                                </span>
                                            </td>
                                            <td>{{ $k->poli->nama_poli ?? '-' }}</td>
                                            <td>{{ $k->dokter->nama ?? '-' }}</td>
                                            <td>{{ $k->diagnosa ?? '-' }}</td>
                                            <td>{{ $k->waktu_daftar->format('d M Y, H:i') }}</td>
                                            <td>{{ ucfirst($k->status) }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-6">Tidak ada data pasien ditemukan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $pasien->links() }}
            </div>

        </div>
    </div>

</div>
@push('scripts')
<script>
    function toggleRiwayat(id) {
        document.getElementById('riwayat-' + id).classList.toggle('show');
    }
</script>
@endpush
@endsection