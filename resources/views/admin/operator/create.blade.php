@extends('layouts.dashboard')

@section('title', 'Tambah Operator')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; }

    .page-header {
        background: linear-gradient(135deg, #6993FF 0%, #4D6FE0 100%);
        border-radius: 18px; padding: 28px 32px; color: #fff;
        box-shadow: 0 10px 30px rgba(105,147,255,.25);
    }
    .page-header h1 { color: #fff; }
    .page-header .sub { color: rgba(255,255,255,.85) !important; }

    .modern-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); max-width: 560px; }
    .error-box { background:#FFE9EA; color:#F64E60; padding:14px 18px; border-radius:12px; max-width:560px; margin-bottom:20px; }

    .submenu-box {
        background: #F9F9FB;
        border-radius: 12px;
        padding: 14px 16px;
    }
    .submenu-check {
        display: flex; align-items: center; gap: 10px;
        padding: 8px 4px;
        font-weight: 500; font-size: 14px; color: #464E5F;
    }
    .submenu-check input { width: 17px; height: 17px; accent-color: #6993FF; cursor: pointer; }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div>
            <h1 class="font-weight-bolder mb-1"><i class="fas fa-user-plus mr-2"></i>Tambah Akun Operator</h1>
            <span class="sub font-weight-bold">Buat akun baru untuk operator divisi</span>
        </div>
    </div>

    @if ($errors->any())
        <div class="error-box">
            <ul class="mb-0 pl-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card modern-card">
        <div class="card-body p-6">
            <form method="POST" action="{{ route('admin.operator.store') }}">
                @csrf

                <div class="form-group">
                    <label class="font-weight-bold mb-2">Nama</label>
                    <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-solid" required>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-solid" required>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold mb-2">Password</label>
                    <input type="password" name="password" class="form-control form-control-solid" required>
                </div>

                @if ($division)
                    {{-- Manajer: divisi otomatis terkunci --}}
                    <div class="form-group">
                        <label class="font-weight-bold mb-2">Divisi</label>
                        <input type="text" value="{{ $division->name }}" class="form-control form-control-solid" disabled>
                    </div>

                    @if ($division->slug === 'layanan')
                        <div class="form-group">
                            <label class="font-weight-bold mb-2">Akses Sub-menu (centang yang boleh diakses)</label>
                            <div class="submenu-box">
                                @foreach ($daftarSubmenu as $slug => $label)
                                    <label class="submenu-check mb-0">
                                        <input type="checkbox" name="submenu[]" value="{{ $slug }}" @checked(in_array($slug, old('submenu', [])))>
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @else
                    {{-- Admin: bebas pilih divisi --}}
                    <div class="form-group">
                        <label class="font-weight-bold mb-2">Divisi</label>
                        <select name="division_id" id="division_id" class="form-control form-control-solid" required onchange="toggleSubmenu()">
                            <option value="">Pilih divisi</option>
                            @foreach ($divisions as $d)
                                <option value="{{ $d->id }}" data-slug="{{ $d->slug }}" @selected(old('division_id') == $d->id)>{{ $d->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" id="submenu-wrapper" style="display:none;">
                        <label class="font-weight-bold mb-2">Akses Sub-menu (centang yang boleh diakses)</label>
                        <div class="submenu-box">
                            @foreach ($daftarSubmenu as $slug => $label)
                                <label class="submenu-check mb-0">
                                    <input type="checkbox" name="submenu[]" value="{{ $slug }}" @checked(in_array($slug, old('submenu', [])))>
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <script>
                        function toggleSubmenu() {
                            const select = document.getElementById('division_id');
                            const selected = select.options[select.selectedIndex];
                            const wrapper = document.getElementById('submenu-wrapper');
                            wrapper.style.display = (selected.dataset.slug === 'layanan') ? 'block' : 'none';
                        }
                    </script>
                @endif

                <div class="d-flex align-items-center mt-6">
                    <button type="submit" class="btn btn-primary font-weight-bold px-8">
                        <i class="fas fa-save mr-2"></i>Simpan Operator
                    </button>
                    <a href="{{ route('admin.operator.index') }}" class="font-weight-bold text-muted ml-4">Batal</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection