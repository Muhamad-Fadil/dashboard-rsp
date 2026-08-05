<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Direktur - RSP Goenawan Cisarua</title>
</head>
<body>
    <h1>Dashboard Direktur</h1>
    <p>Login sebagai: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->role }})</p>
    <p>Sebagai direktur, kamu bisa buka dashboard divisi mana saja:</p>

    <ul>
        @foreach ($divisions as $division)
            <li>
                <a href="{{ route('divisi.dashboard', $division->slug) }}">
                    Dashboard {{ $division->name }}
                </a>
            </li>
        @endforeach
    </ul>

    <hr>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>
