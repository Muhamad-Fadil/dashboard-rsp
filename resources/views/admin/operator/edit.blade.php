<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Operator - RSP Goenawan Cisarua</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 24px; background: #f4f6f9; }
        form { background: #fff; padding: 24px; border-radius: 10px; max-width: 480px; }
        .form-group { margin-bottom: 16px; }
        label { display: block; font-weight: bold; margin-bottom: 4px; font-size: 14px; }
        input[type=text], input[type=email], input[type=password] {
            width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box;
        }
        .checkbox-item { margin-bottom: 6px; }
        .btn-primary { background: #6993FF; color: #fff; border: none; padding: 10px 20px; border-radius: 6px; font-weight: bold; cursor: pointer; }
        .hint { color: #888; font-size: 12px; margin-top: 4px; }
    </style>
</head>
<body>
    <h1>Edit Akun Operator</h1>

    @if ($errors->any())
        <div style="background:#FFE9EA; color:#F64E60; padding:12px 16px; border-radius:8px; max-width:480px; margin-bottom:16px;">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.operator.update', $operator->id) }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label>Nama</label>
            <input type="text" name="name" value="{{ old('name', $operator->name) }}" required>
        </div>

        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" value="{{ old('email', $operator->email) }}" required>
        </div>

        <div class="form-group">
            <label>Password Baru</label>
            <input type="password" name="password">
            <div class="hint">Kosongkan kalau tidak mau ganti password</div>
        </div>

        <div class="form-group">
            <label>Divisi</label>
            <input type="text" value="{{ $operator->division->name }}" disabled>
        </div>

        @if ($operator->division->slug === 'layanan')
            <div class="form-group">
                <label>Akses Sub-menu (centang yang boleh diakses)</label>
                @foreach ($daftarSubmenu as $slug => $label)
                    <div class="checkbox-item">
                        <label style="font-weight:normal;">
                            <input type="checkbox" name="submenu[]" value="{{ $slug }}" @checked(in_array($slug, old('submenu', $aksesSaatIni)))>
                            {{ $label }}
                        </label>
                    </div>
                @endforeach
            </div>
        @endif

        <button type="submit" class="btn-primary">Simpan Perubahan</button>
        <a href="{{ route('admin.operator.index') }}" style="margin-left:12px;">Batal</a>
    </form>
</body>
</html>