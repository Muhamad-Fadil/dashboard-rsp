@extends('layouts.dashboard')

@section('title', 'Data Pasien')

@push('styles')
@include('partials.dashboard-styles')
<style>
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

    .patient-layout {
        background: #f8faff;
    }

    .modern-card {
        border-radius: 18px;
        border: 1px solid rgba(105,147,255,.14);
        box-shadow: 0 10px 30px rgba(0,0,0,.05);
        background: #ffffff;
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

    .patient-search-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .patient-search-wrap .input-group {
        max-width: 460px;
        flex: 1 1 420px;
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

    .avatar-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6993FF, #1BC5BD);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 14px;
    }

    .btn-expand {
        border: 0;
        background: #EAF2FF;
        color: #4D6FE0;
        font-weight: 800;
        font-size: 11px;
        border-radius: 8px;
        padding: 7px 12px;
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

    .table-obat {
        width: 100%;
        background: #f8faff;
        border-radius: 12px;
        border: 1px solid #dde8fa;
    }

    .table-obat thead th {
        background: #eef4ff;
        color: #4D6FE0;
        font-size: 11px;
        font-weight: 800;
        padding: 12px;
        text-transform: uppercase;
    }

    .table-obat tbody td {
        border-top: 1px solid #eef1f7;
        padding: 11px 12px;
        color: #464E5F;
        font-size: 11px;
    }

    .row-detail {
        display: none;
    }

    .row-detail.show {
        display: table-row;
    }

    @media (max-width: 767px) {
        .page-header {
            padding: 24px 16px;
        }

        .filter-card {
            align-items: stretch;
        }

        .filter-card > div,
        .filter-card > button,
        .filter-card > a {
            width: 100%;
        }

        .table-responsive {
            overflow-x: auto;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6 patient-layout">

    @include('partials.submenu-layanan')

    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
            <div>
                <h1 class="font-weight-bolder mb-1">Data Pasien</h1>
                <span class="text-muted-light font-weight-bold">Seluruh data pasien terdaftar di RSP Goenawan Cisarua</span>
                <div class="text-muted-light font-size-sm mt-2">
                    Periode {{ $awal->format('d M Y') }} — {{ $akhir->format('d M Y') }}
                </div>
            </div>
            <x-modal-pdf id="modalPdfPasien" title="Data Pasien" :action="route('divisi.layanan.pasien.pdf', $division->slug)" />
        </div>
    </div>

    <div class="row mb-2">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value text-dark">{{ number_format($ringkasan['total'] ?? 0) }}</div>
                <div class="stat-label">Total Pasien</div>
            </div></div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value" style="color:#1BC5BD;">{{ number_format($ringkasan['lakilaki'] ?? 0) }}</div>
                <div class="stat-label">Laki-laki</div>
            </div></div>
        </div>
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value" style="color:#F64E60;">{{ number_format($ringkasan['perempuan'] ?? 0) }}</div>
                <div class="stat-label">Perempuan</div>
            </div></div>
        </div>
    </div>

    <div class="card modern-card">
        <div class="card-body p-5">

            <form method="GET" class="filter-card d-flex align-items-end flex-wrap p-4 mb-5">
                <div class="form-group mb-0 mr-4">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Dari Tanggal</label>
                    <input type="date" name="awal" value="{{ $awal->format('Y-m-d') }}" class="form-control form-control-solid">
                </div>
                <div class="form-group mb-0 mr-4">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Sampai Tanggal</label>
                    <input type="date" name="akhir" value="{{ $akhir->format('Y-m-d') }}" class="form-control form-control-solid">
                </div>
                <div class="form-group mb-0 mr-4">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Cari</label>
                    <input type="text" name="cari" value="{{ $cari }}" class="form-control form-control-solid" placeholder="Nama / No RM / NIK">
                </div>
                <button type="submit" class="btn btn-primary font-weight-bold px-6">Terapkan</button>
                <a href="{{ route('divisi.layanan.pasien', $division->slug) }}" class="btn btn-light font-weight-bold px-6 ml-2">
                    Reset
                </a>
            </form>

            <div class="patient-search-wrap">
                @if ($cari)
                    <span class="badge badge-light-primary">Pencarian: {{ $cari }}</span>
                @endif
            </div>

            <div class="table-responsive mt-4">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Pasien</th>
                            <th>No. RM</th>
                            <th>L/P</th>
                            <th>Usia</th>
                            <th>Tipe Pembayaran</th>
                            <th>Riwayat Kunjungan</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pasien as $p)
                        @php
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
                                    <div class="font-weight-bold text-dark">{{ $p->nama }}</div>
                                </div>
                            </td>
                            <td class="font-weight-bold nowrap">{{ $p->no_rm }}</td>
                            <td class="nowrap">{{ $p->jenis_kelamin }}</td>
                            <td class="nowrap" title="Usia estimasi, dihitung dari data kunjungan">~{{ $p->tanggal_lahir ? \Carbon\Carbon::parse($p->tanggal_lahir)->age : '-' }} th</td>
                            <td>
                                @php
                                    $warnaTipe = match($p->jenisPembayaran->kode ?? null) {
                                        'bpjs' => ['bg' => '#E8FFF3', 'text' => '#1BC5BD'],
                                        'tunai' => ['bg' => '#EEF3FF', 'text' => '#6993FF'],
                                        default => ['bg' => '#FFF6E0', 'text' => '#FFA800'],
                                    };
                                @endphp
                                <span class="badge-modern" style="background:{{ $warnaTipe['bg'] }}; color:{{ $warnaTipe['text'] }};">
                                    {{ $p->jenisPembayaran?->nilai ?? 'Belum diisi' }}
                                </span>
                                @if (($p->jenisPembayaran->kode ?? null) === 'lainnya' && $p->keterangan_pembayaran)
                                    <div class="text-muted" style="font-size: 10px;">({{ $p->keterangan_pembayaran }})</div>
                                @endif
                            </td>
                            <td class="nowrap">
                                @if ($p->kunjungan->isNotEmpty())
                                    <button type="button" class="btn-expand" onclick="toggleRiwayat({{ $p->id }})">Lihat Riwayat</button>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>

                        @if ($p->kunjungan->isNotEmpty())
                        <tr class="row-detail" id="riwayat-{{ $p->id }}">
                            <td colspan="7">
                                                                <table class="table-obat">
                                    <thead>
                                        <tr>
                                            <th>No. Kunjungan</th>
                                            <th>Jenis</th>
                                            <th>Poli / Ruang</th>
                                            <th>Tanggal</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($p->kunjungan as $k)
                                        <tr>
                                            <td class="font-weight-bold">{{ $k->no_kunjungan }}</td>
                                            <td>
                                                @php
                                                    $jenisKunjungan = $k->jenis_kunjungan ?? null;
                                                    $warnaJenisKunjungan = $warnaJenis[$jenisKunjungan] ?? ['bg' => '#F3F6F9', 'text' => '#464E5F'];
                                                    $labelKunjungan = $labelJenis[$jenisKunjungan] ?? 'Lainnya';
                                                @endphp
                                                <span class="badge-modern" style="background:{{ $warnaJenisKunjungan['bg'] }}; color:{{ $warnaJenisKunjungan['text'] }};">
                                                    {{ $labelKunjungan }}
                                                </span>
                                            </td>
                                            <td>
                                                @if ($jenisKunjungan === 'igd')
                                                    <span class="text-muted">Gawat Darurat</span>
                                                @elseif ($jenisKunjungan === 'rawat_inap')
                                                    {{ $k->rawatInap?->bed?->kamar?->nama_bangsal ?? 'Ruang Rawat Inap' }}
                                                @else
                                                    {{ $k->poli?->nama_poli ?? '-' }}
                                                @endif
                                            </td>
                                            <td>{{ optional($k->waktu_daftar)->format('d M Y') ?? '-' }}</td>
                                            <td>{{ ucfirst($k->status ?? '-') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-6">Tidak ada data pasien ditemukan</td></tr>
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
        const el = document.getElementById('riwayat-' + id);
        if (el) {
            el.classList.toggle('show');
        }
    }
</script>
@endpush
@endsection