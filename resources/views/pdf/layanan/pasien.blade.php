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
            <th>Kecamatan</th>
            <th>Tipe Pasien</th>
            <th>Riwayat Kunjungan</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($pasien as $p)
        @php
            $jumlahPerJenis = $p->kunjungan->groupBy('jenis_kunjungan')->map->count();
            $labelJenis = ['rawat_jalan' => 'RJ', 'rawat_inap' => 'RI', 'igd' => 'IGD'];
        @endphp
        <tr>
            <td>{{ $p->nama }}</td>
            <td>{{ $p->no_rm }}</td>
            <td>{{ $p->no_registrasi ?? '-' }}</td>
            <td>{{ $p->jenis_kelamin }}</td>
            <td>{{ $p->tanggal_lahir ? \Carbon\Carbon::parse($p->tanggal_lahir)->age . ' th' : '-' }}</td>
            <td>
                @if ($p->wilayah)
                    {{ $p->wilayah->nama_kecamatan }} ({{ $p->wilayah->kabupaten_kota === 'kota' ? 'Kota' : 'Kab.' }})
                @else
                    -
                @endif
            </td>
            <td>{{ $p->jenisPembayaran->nilai ?? '-' }}</td>
            <td>
                @forelse ($jumlahPerJenis as $jenis => $jumlah)
                    {{ $jumlah }}x {{ $labelJenis[$jenis] ?? $jenis }}{{ !$loop->last ? ', ' : '' }}
                @empty
                    -
                @endforelse
            </td>
        </tr>
        @empty
        <tr><td colspan="8" style="text-align:center;">Tidak ada data pada periode ini</td></tr>
        @endforelse
    </tbody>
</table>
<p style="margin-top:10px; font-size:10px; color:#888;">Total: {{ $pasien->count() }} pasien terdaftar pada periode ini</p>
@endsection