@extends('pdf.layout')

@section('title', 'Data Pendapatan')

@section('content')

<table>
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Kategori</th>
            <th>Unit Kerja</th>
            <th>Keterangan</th>
            <th>Jumlah</th>
        </tr>
    </thead>

    <tbody>
        @forelse ($pendapatan as $item)
            <tr>
                <td>
                    {{ $item->tanggal?->format('d/m/Y') ?? '-' }}
                </td>

                <td>
                    {{ $item->kategori->nama_kategori ?? '-' }}
                </td>

                <td>
                    {{ $item->unitKerja->nama_unit ?? '-' }}
                </td>

                <td>
                    {{ $item->keterangan ?? '-' }}
                </td>

                <td>
                    Rp {{ number_format($item->jumlah, 0, ',', '.') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" style="text-align: center;">
                    Tidak ada data pendapatan pada periode ini.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

<div style="margin-top: 15px;">
    <strong>
        Total Pendapatan:
        Rp {{ number_format($pendapatan->sum('jumlah'), 0, ',', '.') }}
    </strong>
</div>

@endsection