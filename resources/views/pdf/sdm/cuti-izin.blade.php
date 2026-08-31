@extends('pdf.layout')

@section('title', 'Laporan Cuti & Izin Pegawai')

@section('content')
<p style="font-size:12px; font-weight:bold; margin-bottom:6px;">Pengajuan Cuti</p>
<table>
    <thead>
        <tr>
            <th>Nama Pegawai</th>
            <th>NIP</th>
            <th>Jenis Cuti</th>
            <th>Tanggal Mulai</th>
            <th>Tanggal Selesai</th>
            <th>Jumlah Hari</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($cuti as $c)
        <tr>
            <td>{{ $c->pegawai->nama ?? '-' }}</td>
            <td>{{ $c->pegawai->nip ?? '-' }}</td>
            <td>{{ $c->jenis_cuti }}</td>
            <td>{{ $c->tanggal_mulai->format('d M Y') }}</td>
            <td>{{ $c->tanggal_selesai->format('d M Y') }}</td>
            <td>{{ $c->jumlah_hari }} hari</td>
            <td>{{ ucfirst($c->status) }}</td>
        </tr>
        @empty
        <tr><td colspan="7" style="text-align:center;">Tidak ada pengajuan cuti pada periode ini</td></tr>
        @endforelse
    </tbody>
</table>
<p style="margin-top:6px; margin-bottom:16px; font-size:10px; color:#888;">Total: {{ $cuti->count() }} pengajuan cuti</p>

<p style="font-size:12px; font-weight:bold; margin-bottom:6px;">Izin Harian (dari Absensi)</p>
<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Nama Pegawai</th>
            <th>NIP</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($izinHarian as $i)
        <tr>
            <td>{{ $i->tanggal->format('d M Y') }}</td>
            <td>{{ $i->pegawai->nama ?? '-' }}</td>
            <td>{{ $i->pegawai->nip ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="3" style="text-align:center;">Tidak ada izin harian pada periode ini</td></tr>
        @endforelse
    </tbody>
</table>
<p style="margin-top:6px; font-size:10px; color:#888;">Total: {{ $izinHarian->count() }} izin harian</p>
@endsection