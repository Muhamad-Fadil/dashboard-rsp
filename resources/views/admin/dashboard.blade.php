<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - RSP Goenawan Cisarua</title>
</head>
<body>
    <h1>Admin Panel</h1>
    <p>Login sebagai: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->role }})</p>

    <hr>

    <h3>Ringkasan</h3>
    <p>Total user terdaftar: {{ $totalUsers }}</p>

    <h3>Divisi</h3>
    <ul>
        @foreach ($divisions as $division)
            <li>{{ $division->name }} — {{ $division->users_count }} user</li>
        @endforeach
    </ul>

    <hr>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit">Logout</button>
    </form>
</body>
</html>
