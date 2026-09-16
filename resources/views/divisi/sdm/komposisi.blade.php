@extends('layouts.dashboard')

@section('title', 'Komposisi Pegawai')

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
    .table-modern thead th { border: none; color: #a1a5b7; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
    .table-modern td { border-color: #f1f1f4; vertical-align: middle; }
    .table-modern tbody tr:hover { background: #f9f9fb; }
    .badge-modern { border-radius: 20px; padding: 5px 12px; font-weight: 600; font-size: 11px; }
    .avatar-circle {
        width: 36px; height: 36px; border-radius: 50%; background: #F1E9FF; color: #8950FC;
        display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 13px;
        flex-shrink: 0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">
    @include('partials.submenu-sdm')

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div>
            <h1 class="font-weight-bolder mb-1"><i class="fas fa-user-md mr-2"></i>Komposisi Pegawai</h1>
            <span class="text-muted-light font-weight-bold">Total {{ $komposisi->sum('total') }} pegawai aktif, terbagi 5 kelompok</span>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5 mb-6">
            <div class="card modern-card h-100">
                <div class="card-body p-5">
                    <h3 class="font-weight-bolder mb-4">Grafik Komposisi</h3>
                    <canvas id="chartKomposisi" height="220"></canvas>
                    <div class="mt-4">
                        @foreach ($komposisi as $item)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="font-weight-bold text-dark font-size-sm">{{ $item['label'] }}</span>
                            <span class="font-weight-bolder text-primary font-size-sm">{{ $item['total'] }} ({{ $item['persentase'] }}%)</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 mb-6">
            <div class="card modern-card h-100">
                <div class="card-body p-5">
                    <h3 class="font-weight-bolder mb-4">Filter Daftar Pegawai</h3>
                    <form method="GET" class="d-flex flex-wrap align-items-end">
                        <div class="form-group mb-0 mr-3">
                            <label class="font-weight-bold mb-1 font-size-sm text-muted">Kelompok</label>
                            <select name="kelompok" class="form-control form-control-solid" style="width: 200px;" onchange="this.form.submit()">
                                <option value="">Semua Kelompok</option>
                                @foreach ($komposisi as $item)
                                    <option value="{{ $item['kelompok'] }}" @selected($kelompokFilter === $item['kelompok'])>{{ $item['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group mb-0 mr-3">
                            <label class="font-weight-bold mb-1 font-size-sm text-muted">Cari Nama/NIP</label>
                            <input type="text" name="cari" value="{{ $cari }}" placeholder="Cari..." class="form-control form-control-solid" style="width: 200px;">
                        </div>
                        <button type="submit" class="btn btn-primary font-weight-bold px-6"><i class="fas fa-search mr-2"></i>Cari</button>
                        @if ($kelompokFilter || $cari)
                            <a href="{{ route('divisi.sdm.komposisi', $division->slug) }}" class="ml-3 text-muted font-weight-bold">Reset</a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card modern-card mb-6">
        <div class="card-body p-5">
            <h3 class="font-weight-bolder mb-4">Daftar Pegawai ({{ $pegawai->count() }})</h3>
            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>NIP</th>
                            <th>Profesi</th>
                            <th>Kelompok</th>
                            <th>Unit Kerja</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pegawai as $p)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle mr-3">{{ strtoupper(substr($p->nama, 0, 1)) }}</div>
                                    <span class="font-weight-bold text-dark">{{ $p->nama }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $p->nip }}</td>
                            <td>{{ $p->profesi->nama_profesi ?? '-' }}</td>
                            <td><span class="badge badge-modern" style="background:#F1E9FF; color:#8950FC;">{{ $p->kelompok_label }}</span></td>
                            <td>{{ $p->unitKerja->nama_unit ?? '-' }}</td>
                            <td class="text-muted font-size-sm">{{ str_replace('_', ' ', ucfirst($p->status_kepegawaian ?? '-')) }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center text-muted py-6">Tidak ada pegawai yang cocok dengan filter</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const komposisiLabels = {!! json_encode($komposisi->pluck('label')) !!};
    const komposisiTotals = {!! json_encode($komposisi->pluck('total')) !!};

    new Chart(document.getElementById('chartKomposisi'), {
        type: 'doughnut',
        data: {
            labels: komposisiLabels,
            datasets: [{
                data: komposisiTotals,
                backgroundColor: ['#8950FC', '#1BC5BD', '#FFA800', '#6993FF', '#F64E60'],
                borderWidth: 0,
            }]
        },
        options: { responsive: true, cutout: '65%', plugins: { legend: { display: false } } }
    });
</script>
@endpush