@extends('layouts.dashboard')

@section('title', 'Pendapatan')

@push('styles')
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .modern-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #edf0f5;
            box-shadow: 0 8px 26px rgba(15, 23, 42, .05);
        }

        .stat-card {
            background: #fff;
            border-radius: 18px;
            border: 1px solid #edf0f5;
            box-shadow: 0 8px 26px rgba(15, 23, 42, .05);
            height: 100%;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 14px 32px rgba(15, 23, 42, .09);
        }

        .stat-value {
            font-size: 26px;
            font-weight: 800;
            line-height: 1.1;
        }

        .stat-label {
            font-size: 13px;
            font-weight: 600;
            color: #7e8299;
            margin-top: 4px;
        }

        .page-header {
            background: linear-gradient(135deg, #0f766e 0%, #134e4a 100%);
            width: 100%;
            border-radius: 18px;
            padding: 30px 32px;
            color: #fff;
            box-shadow: 0 12px 30px rgba(15, 118, 110, .2);
            overflow: hidden;
            position: relative;
        }

        .page-header::after {
            content: '';
            position: absolute;
            width: 190px;
            height: 190px;
            border: 24px solid rgba(255, 255, 255, .08);
            border-radius: 50%;
            right: -45px;
            top: -80px;
        }

        .page-header h1 {
            color: #fff;
        }

        .page-header .text-muted-light {
            color: rgba(255, 255, 255, .85) !important;
        }

        .filter-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 8px 26px rgba(15, 23, 42, .05);
            border: 1px solid #edf0f5;
        }

        .summary-icon {
            width: 44px;
            height: 44px;
            border-radius: 13px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
        }

        .summary-caption,
        .summary-note {
            color: #7e8299;
            font-size: 12px;
            font-weight: 600;
        }

        .section-kicker {
            color: #0f766e;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .8px;
            text-transform: uppercase;
        }

        .table-modern thead th {
            border: none;
            color: #a1a5b7;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        .table-modern td {
            border-color: #f1f1f4;
            vertical-align: middle;
        }

        .table-modern tbody tr:hover {
            background: #f9f9fb;
        }

        .category-name {
            font-weight: 700;
            color: #181c32;
        }

        .category-progress {
            height: 6px;
            background: #eef1f5;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 7px;
        }

        .category-progress-bar {
            height: 100%;
            min-width: 4px;
            background: linear-gradient(90deg, #1aa99d, #0f766e);
            border-radius: 10px;
        }

        .ds-dep-section {
            border: 1px solid #edf0f5;
            border-radius: 14px;
            overflow: hidden;
        }

        .ds-dep-heading {
            background: #f8fafc;
            border-bottom: 1px solid #edf0f5;
            padding: 15px 18px;
        }

        .ds-dep-heading h4 {
            font-size: 15px;
            font-weight: 800;
            color: #181c32;
            margin: 0;
        }

        .ds-dep-total {
            color: #0f766e;
            font-size: 13px;
            font-weight: 800;
        }

        .empty-state {
            padding: 58px 20px;
            text-align: center;
            color: #7e8299;
        }

        @media (max-width: 767.98px) {
            .page-header { padding: 24px; }
            .page-header .btn { margin-top: 18px; width: 100%; }
            .filter-card .form-group,
            .filter-card .form-control,
            .filter-card select { width: 100% !important; margin-right: 0 !important; }
            .filter-card .btn { width: 100%; margin-top: 12px; }
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid px-6 py-6">

        @include('partials.submenu-keuangan')

        @php
            $jumlahTransaksi = $pendapatan->count();
            $rataRataTransaksi = $jumlahTransaksi > 0 ? $pendapatan->avg('jumlah') : 0;
            $namaTampilan = $tabList[$tab] ?? 'Ringkasan';
            $pendapatanPerKategori = $pendapatan
                ->groupBy(fn ($item) => implode('|', [
                    $item->kategori->ds_dep ?? $item->ds_dep ?? 'Tanpa ds_dep',
                    $item->kategori->nama_kategori ?? 'Tanpa kategori',
                ]))
                ->map(function ($items) {
                    $itemPertama = $items->first();

                    return [
                        'ds_dep' => $itemPertama->kategori->ds_dep ?? $itemPertama->ds_dep ?? 'Tanpa ds_dep',
                        'nama' => $itemPertama->kategori->nama_kategori ?? 'Tanpa kategori',
                        'jumlah_transaksi' => $items->count(),
                        'total' => $items->sum('jumlah'),
                    ];
                })
                ->sortByDesc('total')
                ->values();
        @endphp

        {{-- Header --}}
        <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
            <div class="position-relative" style="z-index: 1;">
                <div class="text-uppercase font-size-sm font-weight-bold mb-2" style="letter-spacing: 1px; color: #99f6e4;">
                    Ringkasan keuangan
                </div>
                <h1 class="font-weight-bolder mb-2">Pendapatan</h1>
                <span class="text-muted-light font-weight-bold">
                    Ikhtisar penerimaan rumah sakit untuk periode terpilih
                </span>
            </div>

            <a
                href="{{ route('divisi.keuangan.pendapatan.pdf', ['division' => $division->slug, 'tab' => $tab, 'awal' => $awal, 'akhir' => $akhir]) }}"
                target="_blank"
                class="btn font-weight-bold px-5 position-relative"
                style="background-color: #fff; color: #115e59; border: none; z-index: 1;"
            >
                <i class="fas fa-file-download mr-2"></i>Download PDF
            </a>
        </div>

        {{-- Filter --}}
        <form method="GET" class="filter-card d-flex align-items-end flex-wrap p-4 mb-6">

            <div class="w-100 mb-3">
                <div class="section-kicker">Periode laporan</div>
                <div class="summary-note">Atur rentang tanggal dan kategori untuk memperbarui ringkasan.</div>
            </div>

            <div class="form-group mb-0 mr-4">
                <label class="font-weight-bold mb-1 font-size-sm text-muted">
                    Dari Tanggal
                </label>

                <input
                    type="date"
                    name="awal"
                    value="{{ $awal }}"
                    class="form-control form-control-solid"
                    style="width: 170px;"
                >
            </div>

            <div class="form-group mb-0 mr-4">
                <label class="font-weight-bold mb-1 font-size-sm text-muted">
                    Sampai Tanggal
                </label>

                <input
                    type="date"
                    name="akhir"
                    value="{{ $akhir }}"
                    class="form-control form-control-solid"
                    style="width: 170px;"
                >
            </div>

            <div class="form-group mb-0 mr-4">
                <label class="font-weight-bold mb-1 font-size-sm text-muted">
                    Tampilkan
                </label>

                <select
                    name="tab"
                    class="form-control form-control-solid"
                    style="width: 250px;"
                >
                    @foreach ($tabList as $kunci => $label)
                        <option
                            value="{{ $kunci }}"
                            @selected($tab === $kunci)
                        >
                            {{ $label }}

                            @if (in_array($kunci, $tabBelumTersedia, true))
                                (segera)
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <button
                type="submit"
                class="btn btn-primary font-weight-bold px-6"
            >
                <i class="fas fa-filter mr-2"></i>
                Terapkan
            </button>

        </form>

        {{-- Statistik --}}
        <div class="row mb-2">

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="summary-icon" style="background: #ccfbf1; color: #0f766e;"><i class="fas fa-wallet"></i></div>
                            <span class="summary-caption">{{ $namaTampilan }}</span>
                        </div>
                        <div class="stat-value text-dark">
                            Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                        </div>
                        <div class="stat-label">Total pendapatan</div>
                        <div class="summary-note mt-3"><i class="fas fa-calendar-alt mr-1"></i>{{ $awal }} s/d {{ $akhir }}</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="summary-icon" style="background: #dbeafe; color: #2563eb;"><i class="fas fa-receipt"></i></div>
                            <span class="summary-caption">Aktivitas</span>
                        </div>
                        <div class="stat-value text-dark">{{ number_format($jumlahTransaksi, 0, ',', '.') }}</div>
                        <div class="stat-label">Transaksi tercatat</div>
                        <div class="summary-note mt-3">Pada periode yang dipilih</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="summary-icon" style="background: #fef3c7; color: #b45309;"><i class="fas fa-chart-line"></i></div>
                            <span class="summary-caption">Rata-rata</span>
                        </div>
                        <div class="stat-value text-dark">Rp {{ number_format($rataRataTransaksi, 0, ',', '.') }}</div>
                        <div class="stat-label">Nilai per transaksi</div>
                        <div class="summary-note mt-3">Dari seluruh transaksi</div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="summary-icon" style="background: #fce7f3; color: #be185d;"><i class="fas fa-layer-group"></i></div>
                            <span class="summary-caption">Tampilan</span>
                        </div>
                        <div class="stat-value text-dark" style="font-size: 20px;">{{ $namaTampilan }}</div>
                        <div class="stat-label">Kategori laporan</div>
                        <div class="summary-note mt-3">Data pendapatan terpilih</div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Transaksi Pendapatan --}}
        <div class="card modern-card mb-6">

            <div class="card-body p-5">

                <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                    <div>
                        <div class="section-kicker mb-1">Rincian penerimaan</div>
                        <h3 class="font-weight-bolder text-dark mb-0">Transaksi Pendapatan</h3>
                    </div>
                    <span class="badge badge-light-primary px-3 py-2 mt-2 mt-md-0">
                        {{ number_format($pendapatanPerKategori->count(), 0, ',', '.') }} kategori
                    </span>
                </div>

                @if (in_array($tab, $tabBelumTersedia, true))

                    <div class="empty-state">

                        <div class="mb-3">
                            <i class="fas fa-database fa-3x text-muted"></i>
                        </div>

                        <strong>
                            Data untuk tampilan
                            {{ $tabList[$tab] ?? $tab }}
                            belum tersedia.
                        </strong>

                        <div class="mt-2">
                            Pilihan ini sudah tersedia dan akan menampilkan
                            data setelah kategori tersebut memiliki data
                            pendapatan.
                        </div>

                    </div>

                @else

                    @if ($pendapatanPerKategori->isEmpty())

                        <div class="empty-state">
                            <div class="mb-3">
                                <i class="fas fa-chart-pie fa-3x text-muted"></i>
                            </div>
                            <strong>Belum ada pendapatan pada periode ini.</strong>
                            <div class="mt-2">Coba pilih rentang tanggal yang berbeda untuk melihat daftar pendapatan.</div>
                        </div>

                    @else

                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th>Nama Pendapatan</th>
                                    <th>ds_dep</th>
                                    <th>Transaksi</th>
                                    <th>Total Pendapatan</th>
                                    <th class="text-right">Kontribusi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pendapatanPerKategori as $kategori)
                                    @php
                                        $persentase = $totalPendapatan > 0
                                            ? ($kategori['total'] / $totalPendapatan) * 100
                                            : 0;
                                    @endphp
                                    <tr>
                                        <td>
                                            <div class="category-name">{{ $kategori['nama'] }}</div>
                                            <div class="category-progress">
                                                <div class="category-progress-bar" style="width: {{ min($persentase, 100) }}%;"></div>
                                            </div>
                                        </td>
                                        <td><span class="badge badge-light-primary px-3 py-2">{{ $kategori['ds_dep'] }}</span></td>
                                        <td>
                                            <span class="badge badge-light-info px-3 py-2">
                                                {{ number_format($kategori['jumlah_transaksi'], 0, ',', '.') }} transaksi
                                            </span>
                                        </td>
                                        <td class="font-weight-bolder text-dark">Rp {{ number_format($kategori['total'], 0, ',', '.') }}</td>
                                        <td class="text-right font-weight-bold text-primary">{{ number_format($persentase, 1, ',', '.') }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @endif

                @endif

            </div>

        </div>

    </div>
@endsection