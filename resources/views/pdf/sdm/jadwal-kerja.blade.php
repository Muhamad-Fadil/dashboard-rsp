@extends('pdf.layout')

@section('title', 'Jadwal Kerja Pegawai')

@section('content')
<p style="font-size:12px; font-weight:bold; margin-bottom:10px;">
    Tanggal: {{ $tanggal->translatedFormat('l, d F Y') }}
</p>

<table>
    <thead>
        <tr>
            <th>NIP</th>
            <th>Nama</th>
            <th>Unit Kerja</th>
            <th>Jenis Shift</th>
            <th>Jam Masuk</th>
            <th>Jam Keluar</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($jadwal as $j)
        <tr>
            <td>{{ $j['nip'] }}</td>
            <td>{{ $j['nama'] }}</td>
            <td>{{ $j['unit_kerja'] }}</td>
            <td>{{ $j['jenis_shift'] }}</td>
            <td>{{ $j['jam_masuk'] !== '-' ? \Illuminate\Support\Carbon::parse($j['jam_masuk'])->format('H:i') : '-' }}</td>
            <td>{{ $j['jam_keluar'] !== '-' ? \Illuminate\Support\Carbon::parse($j['jam_keluar'])->format('H:i') : '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;">Belum ada data pegawai</td></tr>
        @endforelse
    </tbody>
</table>
<p style="margin-top:10px; font-size:10px; color:#888;">Total: {{ $jadwal->count() }} pegawai aktif</p>
@endsection