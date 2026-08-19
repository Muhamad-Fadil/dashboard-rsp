@extends('layouts.dashboard')

@section('title', 'Edit Operator')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; }

    .page-header {
        background: linear-gradient(135deg, #FFA800 0%, #E08B00 100%);
        border-radius: 18px; padding: 28px 32px; color: #fff;
        box-shadow: 0 10px 30px rgba(255,168,0,.25);
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
    .submenu-check input { width: 17px; height: 17px; accent-color: #FFA800; cursor: pointer; }
    .hint-text { color: #a1a5b7; font-size: 12.5px; margin-top: 4px; }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div>
            <h1 class="font-weight-bolder mb-1"><i class="fas fa-user-pen fa-user-edit mr-2"></i>Edit Akun Operator</h1>
            <span class="sub font-weight-bold">{{ $operator->name }} &middot; {{ $operator->division->name }}</span>
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
            <form method="POST" action="{{ route('admin.operator.update', $operator->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="font-weight-bold mb-2">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $operator->name) }}" class="form-control form-control-solid" required>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold mb-2">Email</label>
                    <input type="email" name="email" value="{{ old('email', $operator->email) }}" class="form-control form-control-solid" required>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold mb-2">Password Baru</label>
                    <input type="password" name="password" class="form-control form-control-solid">
                    <div class="hint-text">Kosongkan kalau tidak mau ganti password</div>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold mb-2">Divisi</label>
                    <input type="text" value="{{ $operator->division->name }}" class="form-control form-control-solid" disabled>
                </div>

                @if ($operator->division->slug === 'layanan')
                    <div class="form-group">
                        <label class="font-weight-bold mb-2">Akses Sub-menu (centang yang boleh diakses)</label>
                        <div class="submenu-box">
                            @foreach ($daftarSubmenu as $slug => $label)
                                <label class="submenu-check mb-0">
                                    <input type="checkbox" name="submenu[]" value="{{ $slug }}" @checked(in_array($slug, old('submenu', $aksesSaatIni)))>
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="d-flex align-items-center mt-6">
                    <button type="submit" class="btn btn-warning font-weight-bold px-8 text-white">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.operator.index') }}" class="font-weight-bold text-muted ml-4">Batal</a>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection