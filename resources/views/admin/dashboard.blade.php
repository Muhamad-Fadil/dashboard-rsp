@extends('layouts.dashboard')

@section('title', 'Admin Panel')

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
    .page-header .sub { color: rgba(255,255,255,.8) !important; }
    .stat-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); height: 100%; }
    .stat-value { font-size: 28px; font-weight: 800; }
    .stat-label { font-size: 13px; font-weight: 600; color: #7e8299; margin-top: 4px; }
    .modern-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); }
    .table-modern thead th { border: none; color: #a1a5b7; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
    .table-modern td { border-color: #f1f1f4; vertical-align: middle; }
    .table-modern tbody tr:hover { background: #f9f9fb; }
    .badge-modern { border-radius: 20px; padding: 6px 14px; font-weight: 600; font-size: 12px; }
    .status-box { background: #E8FFF3; color: #1BC5BD; padding: 12px 18px; border-radius: 10px; margin-bottom: 20px; font-weight: 600; }
    .avatar-circle {
        width: 36px; height: 36px; border-radius: 50%; background: #EEF3FF; color: #6993FF;
        display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div>
            <h1 class="font-weight-bolder mb-1">Admin Panel</h1>
            <span class="sub font-weight-bold">Kelola seluruh akun & divisi SIMRS RSP Goenawan Cisarua</span>
        </div>
    </div>

    @if (session('status'))
        <div class="status-box">{{ session('status') }}</div>
    @endif

    {{-- Ringkasan --}}
    <div class="row mb-2">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value text-dark">{{ number_format($totalUsers) }}</div>
                <div class="stat-label">Total User Terdaftar</div>
            </div></div>
        </div>
        @foreach ($divisions as $div)
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card stat-card"><div class="card-body">
                <div class="stat-value text-dark">{{ $div->users_count }}</div>
                <div class="stat-label">{{ $div->name }}</div>
            </div></div>
        </div>
        @endforeach
    </div>

    {{-- Tabel semua user --}}
    <div class="card modern-card">
        <div class="card-body p-5">
            <h3 class="font-weight-bolder mb-4">Semua Akun</h3>

            <form method="GET" class="d-flex flex-wrap align-items-end mb-5" style="gap: 12px;">
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Cari</label>
                    <input type="text" name="cari" value="{{ $cari }}" class="form-control form-control-solid" style="width: 220px;" placeholder="Nama / email">
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold mb-1 font-size-sm text-muted">Role</label>
                    <select name="role" class="form-control form-control-solid" style="width: 160px;">
                        <option value="">Semua Role</option>
                        <option value="admin" @selected($roleFilter == 'admin')>Admin</option>
                        <option value="direktur" @selected($roleFilter == 'direktur')>Direktur</option>
                        <option value="manajer" @selected($roleFilter == 'manajer')>Manajer</option>
                        <option value="operator" @selected($roleFilter == 'operator')>Operator</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary font-weight-bold px-6">Terapkan</button>
                @if ($cari || $roleFilter)
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-light font-weight-bold px-4">Reset</a>
                @endif
            </form>

            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Divisi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $u)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle mr-3">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
                                    <span class="font-weight-bold text-dark">{{ $u->name }}</span>
                                </div>
                            </td>
                            <td>{{ $u->email }}</td>
                            <td>
                                @php
                                    $warnaRole = match($u->role) {
                                        'admin' => ['bg' => '#F1E9FF', 'text' => '#8950FC'],
                                        'direktur' => ['bg' => '#E8FFF3', 'text' => '#1BC5BD'],
                                        'manajer' => ['bg' => '#EEF3FF', 'text' => '#6993FF'],
                                        default => ['bg' => '#FFF6E0', 'text' => '#FFA800'],
                                    };
                                @endphp
                                <span class="badge-modern" style="background:{{ $warnaRole['bg'] }}; color:{{ $warnaRole['text'] }};">{{ ucfirst($u->role) }}</span>
                            </td>
                            <td>{{ $u->division->name ?? '-' }}</td>
                            <td>
                                <a href="{{ route('admin.users.edit', $u->id) }}" class="btn btn-sm btn-light-primary font-weight-bold">Edit / Reset Password</a>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-6">Tidak ada user ditemukan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">{{ $users->links() }}</div>
        </div>
    </div>

</div>
@endsection