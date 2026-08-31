@extends('layouts.dashboard')

@section('title', 'Data Pegawai')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; }
    .page-header {
        background: linear-gradient(135deg, #8950FC 0%, #6236DB 100%);
        border-radius: 18px; padding: 28px 32px; color: #fff;
        box-shadow: 0 10px 30px rgba(137,80,252,.25);
    }
    .page-header h1 { color: #fff; }
    .page-header .text-muted-light { color: rgba(255,255,255,.8) !important; }
    .modern-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); }
    .filter-card { background: #fff; border-radius: 14px; box-shadow: 0 4px 18px rgba(0,0,0,.06); border: none; }
    .table-modern thead th { border: none; color: #a1a5b7; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
    .table-modern td { border-color: #f1f1f4; vertical-align: middle; white-space: nowrap; }
    .table-modern tbody tr:hover { background: #f9f9fb; }
    .badge-modern { border-radius: 20px; padding: 5px 12px; font-weight: 600; font-size: 11px; }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">
    @include('partials.submenu-sdm')

        <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div>
            <h1 class="font-weight-bolder mb-1"><i class="fas fa-id-card mr-2"></i>Data Pegawai</h1>
            <span class="text-muted-light font-weight-bold">Data induk kepegawaian lengkap, {{ $pegawai->count() }} pegawai aktif</span>
        </div>
        <a href="{{ route('divisi.sdm.data-pegawai.pdf', array_filter(['division' => $division->slug, 'cari' => $cari])) }}"
           target="_blank" class="btn btn-dark font-weight-bold">
            Download PDF
        </a>
    </div>

    <form method="GET" class="filter-card d-flex align-items-end flex-wrap p-4 mb-6">
        <div class="form-group mb-0 mr-3">
            <label class="font-weight-bold mb-1 font-size-sm text-muted">Cari Nama/NIP/NIK</label>
            <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari..." class="form-control form-control-solid" style="width: 250px;">
        </div>
        <button type="submit" class="btn btn-primary font-weight-bold px-6"><i class="fas fa-search mr-2"></i>Cari</button>
        @if ($cari)
            <a href="{{ route('divisi.sdm.data-pegawai', $division->slug) }}" class="ml-3 text-muted font-weight-bold">Reset</a>
        @endif
    </form>

    <div class="card modern-card mb-6">
        <div class="card-body p-5">
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>NIK</th>
                            <th>NIP</th>
                            <th>Nama</th>
                            <th>Tanggal Lahir</th>
                            <th>Tempat Lahir</th>
                            <th>JK</th>
                            <th>Pendidikan</th>
                            <th>Jabatan</th>
                            <th>Golongan</th>
                            <th>Unit Kerja</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pegawai as $p)
                        <tr>
                            <td class="text-muted">{{ $p->nik ?? '-' }}</td>
                            <td class="font-weight-bold text-dark">{{ $p->nip }}</td>
                            <td class="font-weight-bold text-dark">{{ $p->nama }}</td>
                            <td>{{ $p->tanggal_lahir ? $p->tanggal_lahir->translatedFormat('d M Y') : '-' }}</td>
                            <td>{{ $p->tempat_lahir ?? '-' }}</td>
                            <td>
                                <span class="badge badge-modern" style="background:{{ $p->jenis_kelamin === 'L' ? '#EEF3FF' : '#FFE9EA' }}; color:{{ $p->jenis_kelamin === 'L' ? '#6993FF' : '#F64E60' }};">
                                    {{ $p->jenis_kelamin }}
                                </span>
                            </td>
                            <td>{{ $p->pendidikan ?? '-' }}</td>
                            <td>{{ $p->jabatan ?? '-' }}</td>
                            <td>{{ $p->golongan ?? '-' }}</td>
                            <td>{{ $p->unitKerja->nama_unit ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="text-center text-muted py-6">Tidak ada pegawai yang cocok dengan pencarian</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
