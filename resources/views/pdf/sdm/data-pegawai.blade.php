@extends('pdf.layout')

@section('title', 'Data Pegawai')

@section('content')
<table>
    <thead>
        <tr>
            <th>NIK</th>
            <th>NIP</th>
            <th>Nama</th>
            <th>Tanggal Lahir</th>
            <th>Tempat Lahir</th>
            <th>JK</th>
            <th>Pendidikan</th>
            <th>Jabatan</th>
            <th>Golongan</th>
            <th>Unit Kerja</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($pegawai as $p)
        <tr>
            <td>{{ $p->nik ?? '-' }}</td>
            <td>{{ $p->nip }}</td>
            <td>{{ $p->nama }}</td>
            <td>{{ $p->tanggal_lahir ? $p->tanggal_lahir->format('d M Y') : '-' }}</td>
            <td>{{ $p->tempat_lahir ?? '-' }}</td>
            <td>{{ $p->jenis_kelamin }}</td>
            <td>{{ $p->pendidikan ?? '-' }}</td>
            <td>{{ $p->jabatan ?? '-' }}</td>
            <td>{{ $p->golongan ?? '-' }}</td>
            <td>{{ $p->unitKerja->nama_unit ?? '-' }}</td>
        </tr>
        @empty
        <tr><td colspan="10" style="text-align:center;">Tidak ada pegawai yang cocok</td></tr>
        @endforelse
    </tbody>
</table>
<p style="margin-top:10px; font-size:10px; color:#888;">
    Total: {{ $pegawai->count() }} pegawai aktif
    @if ($cari)
        &middot; Kata kunci pencarian: "{{ $cari }}"
    @endif
</p>
@endsection