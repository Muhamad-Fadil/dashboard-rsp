@extends('layouts.dashboard')

@section('title', 'Edit Akun - ' . $targetUser->name)

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; }
    .modern-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); max-width: 520px; }
    .hint { color: #a1a5b7; font-size: 12px; margin-top: 4px; }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">

    <h1 class="font-weight-bolder text-dark mb-1">Edit Akun</h1>
    <p class="text-muted font-weight-bold mb-6">{{ $targetUser->name }} &middot; {{ ucfirst($targetUser->role) }}{{ $targetUser->division ? ' · ' . $targetUser->division->name : '' }}</p>

    @if ($errors->any())
        <div style="background:#FFE9EA; color:#F64E60; padding:14px 18px; border-radius:10px; max-width:520px; margin-bottom:20px;">
            <ul style="margin:0; padding-left:18px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card modern-card">
        <div class="card-body p-5">
            <form method="POST" action="{{ route('admin.users.update', $targetUser->id) }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="font-weight-bold font-size-sm">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $targetUser->name) }}" class="form-control form-control-solid" required>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold font-size-sm">Email</label>
                    <input type="email" name="email" value="{{ old('email', $targetUser->email) }}" class="form-control form-control-solid" required>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold font-size-sm">Password Baru</label>
                    <input type="password" name="password" class="form-control form-control-solid" placeholder="Kosongkan kalau tidak diganti">
                    <div class="hint">Password lama tidak bisa ditampilkan (dienkripsi). Isi di sini kalau mau mengganti dengan password baru.</div>
                </div>

                <button type="submit" class="btn btn-primary font-weight-bold px-6 mt-3">Simpan Perubahan</button>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-light font-weight-bold px-4 mt-3">Batal</a>
            </form>
        </div>
    </div>

</div>
@endsection