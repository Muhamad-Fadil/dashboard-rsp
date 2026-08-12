@extends('pdf.layout')

@section('title', 'Data Rawat Inap' . ($bangsal ? ' - Bangsal ' . $bangsal : ''))

@section('content')
<table>
    <thead>
        <tr>
            <th>Pasien</th>
            <th>No. RM</th>
            <th>Bangsal</th>
            <th>Kamar / Bed</th>
            <th>Dokter</th>
            <th>Tgl Masuk</th>
            <th>Tgl Keluar</th>
            <th>Lama Rawat</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($rawatInap as $ri)
        <tr>
            <td>{{ $ri->kunjungan->pasien->nama ?? '-' }}</td>
            <td>{{ $ri->kunjungan->pasien->no_rm ?? '-' }}</td>
            <td>{{ $ri->bed->kamar->nama_bangsal ?? '-' }}</td>
            <td>{{ $ri->bed->kamar->nomor_kamar ?? '-' }} / {{ $ri->bed->nomor_bed ?? '-' }}</td>
            <td>{{ $ri->dokter->nama ?? '-' }}</td>
            <td>{{ $ri->tanggal_masuk->format('d M Y, H:i') }}</td>
            <td>{{ $ri->tanggal_keluar ? $ri->tanggal_keluar->format('d M Y, H:i') : '-' }}</td>
            <td>{{ $ri->tanggal_keluar ? $ri->lamaRawatHari() . ' hari' : 'Masih dirawat' }}</td>
            <td>{{ ucfirst($ri->status) }}</td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;">Tidak ada data pada periode ini</td></tr>
        @endforelse
    </tbody>
</table>
<p style="margin-top:10px; font-size:10px; color:#888;">Total: {{ $rawatInap->count() }} pasien rawat inap pada periode ini</p>
@endsection