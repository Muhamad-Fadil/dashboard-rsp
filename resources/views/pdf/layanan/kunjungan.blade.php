@extends('pdf.layout')

@section('title', 'Data Kunjungan')

@section('content')
<table>
    <thead>
        <tr>
            <th>No. Kunjungan</th>
            <th>Pasien</th>
            <th>No. RM</th>
            <th>Poli</th>
            <th>Dokter</th>
            <th>Jenis</th>
            <th>Waktu Daftar</th>
            <th>Status</th>
            <th>Operator</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($kunjungan as $k)
        <tr>
            <td>{{ $k->no_kunjungan }}</td>
            <td>{{ $k->pasien->nama ?? '-' }}</td>
            <td>{{ $k->pasien->no_rm ?? '-' }}</td>
            <td>{{ $k->poli->nama_poli ?? '-' }}</td>
            <td>{{ $k->dokter->nama ?? '-' }}</td>
            <td>{{ ucwords(str_replace('_', ' ', $k->jenis_kunjungan)) }}</td>
            <td>{{ $k->waktu_daftar->format('d M Y, H:i') }}</td>
            <td>{{ ucfirst($k->status) }}</td>
            <td>{{ $k->operator->name ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="9" style="text-align:center;">Tidak ada data pada periode ini</td></tr>
        @endforelse
    </tbody>
</table>
<p style="margin-top:10px; font-size:10px; color:#888;">Total: {{ $kunjungan->count() }} kunjungan pada periode ini</p>
@endsection