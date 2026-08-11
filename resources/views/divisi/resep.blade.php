@extends('layouts.dashboard')

@section('title', 'Resep')

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
    .btn-expand { background: #f4f6f9; border: none; border-radius: 8px; padding: 6px 12px; font-weight: 600; font-size: 12px; color: #6993FF; cursor: pointer; }
    .btn-expand:hover { background: #EEF3FF; }
    .row-detail { display: none; background: #f9f9fb; }
    .row-detail.show { display: table-row; }
    .table-obat { width: 100%; font-size: 13px; }
    .table-obat th { text-align: left; color: #a1a5b7; font-weight: 600; padding: 4px 12px; }
    .table-obat td { padding: 6px 12px; }
    nav[role="navigation"] svg { width: 16px !important; height: 16px !important; }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">

    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <div>
            <h1 class="font-weight-bolder text-dark mb-0">Resep</h1>
            <span class="text-muted font-weight-bold">Daftar resep obat yang diterbitkan dokter</span>
        </div>
    </div>

    @include('partials.submenu-layanan')

    {{-- Ringkasan --}}
    <div class="row mb-2">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value text-dark">{{ $ringkasan['total'] }}</div>
                <div class="stat-label">Total Resep</div>
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
                    <input type="text" name="cari" value="{{ $cari }}" class="form-control form-control-solid" style="width: 240px;" placeholder="No resep / nama pasien / no RM">
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Status</label>
                    <select name="status" class="form-control form-control-solid" style="width: 170px;">
                        <option value="">Semua Status</option>
                        <option value="menunggu" @selected($status == 'menunggu')>Menunggu</option>
                        <option value="diproses" @selected($status == 'diproses')>Diproses</option>
                        <option value="selesai" @selected($status == 'selesai')>Selesai</option>
                        <option value="batal" @selected($status == 'batal')>Batal</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary font-weight-bold px-6">Terapkan</button>
                @if ($cari || $status)
                    <a href="{{ route('divisi.resep', $division->slug) }}" class="btn btn-light font-weight-bold px-4">Reset</a>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Pasien</th>
                            <th>No. Resep</th>
                            <th>Dokter</th>
                            <th>Tanggal Resep</th>
                            <th>Jumlah Obat</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($resep as $r)
                        @php
                            $totalHarga = $r->detail->sum(fn ($d) => $d->jumlah * $d->harga_saat_itu);
                        @endphp
                        <tr>
                            <td>
                                <div class="font-weight-bold text-dark">{{ $r->kunjungan->pasien->nama ?? '-' }}</div>
                                <div class="text-muted font-size-sm">{{ $r->kunjungan->pasien->no_rm ?? '-' }}</div>
                            </td>
                            <td class="font-weight-bold">{{ $r->no_resep }}</td>
                            <td>{{ $r->dokter->nama ?? '-' }}</td>
                            <td>{{ $r->tanggal_resep->format('d M Y, H:i') }}</td>
                            <td>{{ $r->detail->count() }} jenis</td>
                            <td class="font-weight-bold">Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
                            <td>
                                @php
                                    $warnaStatus = match($r->status) {
                                        'menunggu' => ['bg' => '#FFF6E0', 'text' => '#FFA800', 'label' => 'Menunggu'],
                                        'diproses' => ['bg' => '#EEF3FF', 'text' => '#6993FF', 'label' => 'Diproses'],
                                        'selesai' => ['bg' => '#E8FFF3', 'text' => '#1BC5BD', 'label' => 'Selesai'],
                                        'batal' => ['bg' => '#F3F6F9', 'text' => '#464E5F', 'label' => 'Batal'],
                                    };
                                @endphp
                                <span class="badge-modern" style="background:{{ $warnaStatus['bg'] }}; color:{{ $warnaStatus['text'] }};">{{ $warnaStatus['label'] }}</span>
                            </td>
                            <td>
                                <button type="button" class="btn-expand" onclick="toggleDetail({{ $r->id }})">Lihat Obat</button>
                            </td>
                        </tr>
                        <tr class="row-detail" id="detail-{{ $r->id }}">
                            <td colspan="8">
                                <table class="table-obat">
                                    <thead>
                                        <tr>
                                            <th>Nama Obat</th>
                                            <th>Jumlah</th>
                                            <th>Aturan Pakai</th>
                                            <th>Harga Satuan</th>
                                            <th>Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($r->detail as $d)
                                        <tr>
                                            <td class="font-weight-bold">{{ $d->obat->nama_obat ?? '-' }}</td>
                                            <td>{{ $d->jumlah }} {{ $d->obat->satuan ?? '' }}</td>
                                            <td>{{ $d->aturan_pakai ?? '-' }}</td>
                                            <td>Rp {{ number_format($d->harga_saat_itu, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($d->jumlah * $d->harga_saat_itu, 0, ',', '.') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center text-muted py-6">Tidak ada data resep ditemukan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">
                {{ $resep->links() }}
            </div>

        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    function toggleDetail(id) {
        document.getElementById('detail-' + id).classList.toggle('show');
    }
</script>
@endpush