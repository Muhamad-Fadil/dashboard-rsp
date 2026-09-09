<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">

    <title>Data Klaim BPJS</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #333;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0 0 5px 0;
            font-size: 18px;
        }

        .header p {
            margin: 0;
            font-size: 11px;
        }

        .periode {
            margin-bottom: 15px;
        }

        .summary {
            width: 100%;
            margin-bottom: 20px;
        }

        .summary td {
            width: 33.33%;
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .summary .label {
            font-size: 10px;
            color: #777;
            margin-bottom: 5px;
        }

        .summary .value {
            font-size: 14px;
            font-weight: bold;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
        }

        table.data th {
            background: #f1f1f1;
            border: 1px solid #ccc;
            padding: 7px;
            text-align: left;
            font-size: 10px;
        }

        table.data td {
            border: 1px solid #ccc;
            padding: 7px;
            vertical-align: middle;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .footer {
            margin-top: 20px;
            font-size: 9px;
            color: #777;
        }
    </style>
</head>

<body>

    <div class="header">
        <h2>DATA KLAIM BPJS</h2>
        <p>Divisi Keuangan</p>
    </div>

    <div class="periode">
        <strong>Periode:</strong>
        {{ \Carbon\Carbon::parse($awal)->format('d/m/Y') }}
        -
        {{ \Carbon\Carbon::parse($akhir)->format('d/m/Y') }}
    </div>

    <table class="summary">
        <tr>
            <td>
                <div class="label">Jumlah Pengajuan</div>
                <div class="value">
                    {{ $jumlahPengajuan }}
                </div>
            </td>

            <td>
                <div class="label">Total Klaim</div>
                <div class="value">
                    Rp {{ number_format($totalKlaim, 0, ',', '.') }}
                </div>
            </td>

            <td>
                <div class="label">Total Disetujui</div>
                <div class="value">
                    Rp {{ number_format($totalDisetujui, 0, ',', '.') }}
                </div>
            </td>
        </tr>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th width="10%">Tanggal Pengajuan</th>
                <th width="20%">Pasien</th>
                <th width="20%">Kunjungan</th>
                <th width="20%">Jumlah Klaim</th>
                <th width="20%">Jumlah Disetujui</th>
                <th width="10%">Status</th>
            </tr>
        </thead>

        <tbody>

            @forelse ($klaim as $item)

                <tr>
                    <td>
                        {{ $item->tanggal_pengajuan
                            ? \Carbon\Carbon::parse($item->tanggal_pengajuan)->format('d/m/Y')
                            : '-' }}
                    </td>

                    <td>
                        {{ $item->pasien->nama ?? '-' }}
                    </td>

                    <td>
                        {{ $item->kunjungan->id ?? '-' }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($item->jumlah_klaim, 0, ',', '.') }}
                    </td>

                    <td class="text-right">
                        Rp {{ number_format($item->jumlah_disetujui, 0, ',', '.') }}
                    </td>

                    <td class="text-center">
                        {{ $item->status ?? '-' }}
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="6" class="text-center">
                        Tidak ada data klaim BPJS pada periode ini.
                    </td>
                </tr>

            @endforelse

        </tbody>
    </table>

    <div class="footer">
        Dicetak pada:
        {{ now()->format('d/m/Y H:i') }}
    </div>

</body>
</html>