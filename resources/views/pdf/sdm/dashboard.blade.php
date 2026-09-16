@extends('pdf.layout')

@section('title', 'Ringkasan Dashboard SDM')

@section('content')
<style>
    .summary-table { margin-bottom: 18px; }
    .summary-box { width: 100%; padding: 10px; background: #f6f8ff; border: 1px solid #e1e7ff; }
    .summary-label { display: block; color: #7e8299; font-size: 9px; text-transform: uppercase; }
    .summary-value { display: block; margin-top: 4px; color: #181c32; font-size: 18px; }
    h2 { margin: 16px 0 7px; color: #181c32; font-size: 12px; }
    .period { margin-top: 12px; color: #888; font-size: 10px; }
</style>
@php
    $status = $data['status_kepegawaian'];
    $komposisi = $data['komposisi_sdm'];
    $totalPegawai = max($data['total_pegawai'], 1);
    $persen = fn($jumlah) => round(($jumlah / $totalPegawai) * 100, 1);
@endphp

<table class="summary-table">
    <tr>
        <td class="summary-box">
            <span class="summary-label">Total Pegawai Aktif</span>
            <strong class="summary-value">{{ number_format($data['total_pegawai']) }}</strong>
        </td>
    </tr>
</table>

<h2>Status Kepegawaian</h2>
<table>
    <thead>
        <tr>
            <th>Status</th>
            <th>Jumlah</th>
            <th>Persentase</th>
        </tr>
    </thead>
    <tbody>
        @foreach (['pns' => 'PNS', 'pppk' => 'PPPK', 'blu' => 'BLU', 'mitra' => 'Mitra', 'magang' => 'Magang'] as $key => $label)
        <tr>
            <td>{{ $label }}</td>
            <td>{{ number_format($status[$key]) }}</td>
            <td>{{ $persen($status[$key]) }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h2>Komposisi Tenaga Kerja</h2>
<table>
    <thead>
        <tr>
            <th>Kelompok</th>
            <th>Jumlah</th>
            <th>Persentase</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($komposisi as $item)
        <tr>
            <td>{{ $item['label'] }}</td>
            <td>{{ number_format($item['total']) }}</td>
            <td>{{ $item['persentase'] }}%</td>
        </tr>
        @empty
        <tr><td colspan="3" style="text-align:center;">Belum ada data komposisi SDM</td></tr>
        @endforelse
    </tbody>
</table>

<h2>Distribusi Pegawai per Unit Kerja</h2>
<table>
    <thead>
        <tr>
            <th>Unit Kerja</th>
            <th>Jumlah Pegawai</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($data['distribusi_per_unit'] as $unit)
        <tr>
            <td>{{ $unit->nama_unit }}</td>
            <td>{{ number_format($unit->total) }}</td>
        </tr>
        @empty
        <tr><td colspan="2" style="text-align:center;">Belum ada data distribusi pegawai</td></tr>
        @endforelse
    </tbody>
</table>

@endsection
