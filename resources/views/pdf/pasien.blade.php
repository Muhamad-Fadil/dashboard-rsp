@extends('pdf.layout')

@section('title', 'Data Pasien')

@section('content')
<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>No. RM</th>
            <th>No. Registrasi</th>
            <th>L/P</th>
            <th>Usia</th>
            <th>No. HP</th>
            <th>NIK</th>
            <th>Tipe Pasien</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($pasien as $p)
        <tr>
            <td>{{ $p->nama }}</td>
            <td>{{ $p->no_rm }}</td>
            <td>{{ $p->no_registrasi ?? '-' }}</td>
            <td>{{ $p->jenis_kelamin }}</td>
            <td>{{ $p->tanggal_lahir ? \Carbon\Carbon::parse($p->tanggal_lahir)->age . ' th' : '-' }}</td>
            <td>{{ $p->no_hp ?? '-' }}</td>
            <td>{{ $p->nik ?? '-' }}</td>
            <td>{{ $p->jenisPembayaran->nilai ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;">Tidak ada data pada periode ini</td></tr>
        @endforelse
    </tbody>
</table>
<p style="margin-top:10px; font-size:10px; color:#888;">Total: {{ $pasien->count() }} pasien terdaftar pada periode ini</p>
@endsection