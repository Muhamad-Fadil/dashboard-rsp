<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard {{ $division->name }} - RSP Goenawan Cisarua</title>
</head>
<body>
    <h1>Dashboard {{ $division->name }}</h1>
    <p>Login sebagai: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->role }})</p>

    <p>(Ini masih halaman kosong / placeholder — nanti diisi konten dashboard {{ $division->name }} yang sebenarnya)</p>

    @if (auth()->user()->role === 'direktur')
        <p><a href="{{ route('direktur.dashboard') }}">&larr; Kembali ke pilihan divisi</a></p>
    @endif

    <hr>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>
