<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kelola Operator - RSP Goenawan Cisarua</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; background: #f4f6f9; }
        table { width: 100%; border-collapse: collapse; background: #fff; margin-top: 16px; }
        th, td { padding: 10px 14px; border-bottom: 1px solid #eee; text-align: left; }
        th { background: #f9f9f9; }
        .btn { display: inline-block; padding: 6px 14px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: bold; }
        .btn-primary { background: #6993FF; color: #fff; }
        .btn-edit { background: #FFF6E0; color: #FFA800; margin-right: 6px; }
        .btn-delete { background: #FFE9EA; color: #F64E60; border: none; cursor: pointer; }
        .badge { display: inline-block; background: #EEF3FF; color: #6993FF; padding: 3px 10px; border-radius: 12px; font-size: 11px; margin: 2px; }
        .status { background: #E8FFF3; color: #1BC5BD; padding: 10px 16px; border-radius: 8px; margin-bottom: 16px; }
    </style>
</head>
<body>
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h1>Kelola Operator</h1>
        <a href="{{ route('admin.operator.create') }}" class="btn btn-primary">+ Tambah Operator</a>
    </div>
    <p>Login sebagai: <strong>{{ auth()->user()->name }}</strong> ({{ auth()->user()->role }})</p>

    @if (auth()->user()->role === 'manajer')
        <p style="color:#888;">Kamu cuma bisa kelola operator di divisi <strong>{{ auth()->user()->division->name }}</strong>.</p>
    @endif

    @if (session('status'))
        <div class="status">{{ session('status') }}</div>
    @endif

    @if (auth()->user()->role === 'admin')
        <form method="GET" style="margin-bottom:8px;">
            <label>Filter Divisi:</label>
            <select name="division_id" onchange="this.form.submit()">
                <option value="">Semua Divisi</option>
                @foreach ($divisions as $d)
                    <option value="{{ $d->id }}" @selected($filterDivisionId == $d->id)>{{ $d->name }}</option>
                @endforeach
            </select>
        </form>
    @endif

    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Email</th>
                <th>Divisi</th>
                <th>Akses Sub-menu</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($operators as $op)
            <tr>
                <td>{{ $op->name }}</td>
                <td>{{ $op->email }}</td>
                <td>{{ $op->division->name ?? '-' }}</td>
                <td>
                    @if ($op->division->slug === 'layanan')
                        @forelse ($op->submenuAkses as $akses)
                            <span class="badge">{{ \App\Models\User::daftarSubmenuLayanan()[$akses->submenu] ?? $akses->submenu }}</span>
                        @empty
                            <span style="color:#F64E60; font-size:12px;">Belum ada akses</span>
                        @endforelse
                    @else
                        <span style="color:#888; font-size:12px;">-</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.operator.edit', $op->id) }}" class="btn btn-edit">Edit</a>
                    <form method="POST" action="{{ route('admin.operator.destroy', $op->id) }}" style="display:inline;" onsubmit="return confirm('Yakin mau hapus akun {{ $op->name }}?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-delete">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="text-align:center; color:#888;">Belum ada operator</td></tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top:16px;">{{ $operators->links() }}</div>

    <p style="margin-top:24px;"><a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('divisi.dashboard', auth()->user()->division->slug) }}">&larr; Kembali ke Dashboard</a></p>
</body>
</html>