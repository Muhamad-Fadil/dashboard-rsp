@extends('pdf.layout')

@section('title', 'Laporan Kehadiran Pegawai')

@section('content')
<p style="font-size:11px; margin-bottom:10px;">
    Persentase kehadiran periode ini: <strong>{{ $persentaseKehadiran }}%</strong>
    &nbsp;|&nbsp;
    Rekap:
    @foreach ($rekapStatus as $status => $total)
        {{ ucfirst($status) }}: <strong>{{ $total }}</strong>{{ !$loop->last ? ', ' : '' }}
    @endforeach
</p>

<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Nama Pegawai</th>
            <th>NIP</th>
            <th>Jam Masuk</th>
            <th>Jam Pulang</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($absensi as $a)
        <tr>
            <td>{{ $a->tanggal->format('d M Y') }}</td>
            <td>{{ $a->pegawai->nama ?? '-' }}</td>
            <td>{{ $a->pegawai->nip ?? '-' }}</td>
            <td>{{ $a->jam_masuk ? $a->jam_masuk->format('H:i') : '-' }}</td>
            <td>{{ $a->jam_pulang ? $a->jam_pulang->format('H:i') : '-' }}</td>
            <td>{{ ucfirst($a->status) }}</td>
        </tr>
        @empty
        <tr><td colspan="6" style="text-align:center;">Tidak ada data absensi pada periode ini</td></tr>
        @endforelse
    </tbody>
</table>
<p style="margin-top:10px; font-size:10px; color:#888;">Total: {{ $absensi->count() }} record absensi pada periode ini</p>
@endsection