<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title')</title>
    <style>
        @page { margin: 25px 30px; }
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { border-bottom: 2px solid #6993FF; padding-bottom: 10px; margin-bottom: 16px; }
        .header h1 { font-size: 16px; margin: 0 0 2px 0; color: #181C32; }
        .header .subtitle { font-size: 11px; color: #7e8299; }
        .meta { display: table; width: 100%; margin-bottom: 14px; font-size: 10px; color: #555; }
        .meta .kiri { display: table-cell; }
        .meta .kanan { display: table-cell; text-align: right; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #EEF3FF; color: #4D6FE0; text-align: left; padding: 6px 8px; font-size: 10px; text-transform: uppercase; }
        tbody td { padding: 6px 8px; border-bottom: 1px solid #eee; font-size: 10.5px; }
        tbody tr:nth-child(even) { background: #fafafa; }
        .footer { position: fixed; bottom: -15px; left: 0; right: 0; text-align: center; font-size: 9px; color: #999; }
    </style>
</head>
<body>
    <div class="header">
        <h1>RSP Goenawan Cisarua - SIMRS</h1>
        <div class="subtitle">@yield('title')</div>
    </div>

    <div class="meta">
        <div class="kiri">Periode: {{ $awal->format('d M Y') }} - {{ $akhir->format('d M Y') }}</div>
        <div class="kanan">Dicetak: {{ now()->format('d M Y, H:i') }} WIB oleh {{ auth()->user()->name }}</div>
    </div>

    @yield('content')

    <div class="footer">RSP Goenawan Cisarua &middot; Dicetak otomatis dari SIMRS</div>
</body>
</html>