@extends('layouts.dashboard')

@section('title', 'Kelola Operator')

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

    .modern-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); }
    .filter-card { background: #fff; border-radius: 14px; box-shadow: 0 4px 18px rgba(0,0,0,.06); border: none; }

    .table-modern thead th { border: none; color: #a1a5b7; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
    .table-modern td { border-color: #f1f1f4; vertical-align: middle; }
    .table-modern tbody tr:hover { background: #f9f9fb; }

    .badge-modern { border-radius: 20px; padding: 5px 12px; font-weight: 600; font-size: 11px; }
    .status-box { background: #E8FFF3; color: #1BC5BD; padding: 12px 18px; border-radius: 10px; margin-bottom: 24px; font-weight: 600; }

    .avatar-circle {
        width: 38px; height: 38px; border-radius: 50%; background: #EEF3FF; color: #6993FF;
        display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px;
        flex-shrink: 0;
    }

    .btn-icon-action {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 14px; border-radius: 8px; font-weight: 600; font-size: 12.5px;
        text-decoration: none; border: none; cursor: pointer;
    }
    .btn-edit-modern { background: #FFF6E0; color: #FFA800; }
    .btn-edit-modern:hover { background: #FFEFC7; color: #FFA800; }
    .btn-delete-modern { background: #FFE9EA; color: #F64E60; }
    .btn-delete-modern:hover { background: #FFD9DB; color: #F64E60; }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div>
            <h1 class="font-weight-bolder mb-1"><i class="fas fa-user-gear fa-user-cog mr-2"></i>Kelola Operator</h1>
            <span class="sub font-weight-bold">
                Login sebagai <strong>{{ auth()->user()->name }}</strong>
                <span class="badge badge-modern ml-1" style="background:rgba(255,255,255,.2); color:#fff;">{{ ucfirst(auth()->user()->role) }}</span>
                @if (auth()->user()->role === 'manajer')
                    &middot; kelola operator divisi <strong>{{ auth()->user()->division->name }}</strong>
                @endif
            </span>
        </div>
        <a href="{{ route('admin.operator.create') }}" class="btn btn-light font-weight-bold px-6">
            <i class="fas fa-plus mr-2"></i>Tambah Operator
        </a>
    </div>

    @if (session('status'))
        <div class="status-box"><i class="fas fa-check-circle mr-2"></i>{{ session('status') }}</div>
    @endif

    @if (auth()->user()->role === 'admin')
        <form method="GET" class="filter-card d-flex align-items-end flex-wrap p-4 mb-6">
            <div class="form-group mb-0 mr-4">
                <label class="font-weight-bold mb-1 font-size-sm text-muted">Filter Divisi</label>
                <select name="division_id" class="form-control form-control-solid" style="width: 220px;" onchange="this.form.submit()">
                    <option value="">Semua Divisi</option>
                    @foreach ($divisions as $d)
                        <option value="{{ $d->id }}" @selected($filterDivisionId == $d->id)>{{ $d->name }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    @endif

    <div class="card modern-card">
        <div class="card-body p-5">
            <h3 class="font-weight-bolder mb-4">Daftar Akun Operator</h3>

            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Divisi</th>
                            <th>Akses Sub-menu</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($operators as $op)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle mr-3">{{ strtoupper(substr($op->name, 0, 1)) }}</div>
                                    <span class="font-weight-bold text-dark">{{ $op->name }}</span>
                                </div>
                            </td>
                            <td class="text-muted">{{ $op->email }}</td>
                            <td>
                                <span class="badge badge-modern" style="background:#EEF3FF; color:#6993FF;">
                                    {{ $op->division->name ?? '-' }}
                                </span>
                            </td>
                            <td>
                                @if ($op->division->slug === 'layanan')
                                    @forelse ($op->submenuAkses as $akses)
                                        <span class="badge badge-modern mr-1 mb-1" style="background:#F1E9FF; color:#8950FC;">
                                            {{ \App\Models\User::daftarSubmenuLayanan()[$akses->submenu] ?? $akses->submenu }}
                                        </span>
                                    @empty
                                        <span class="text-danger font-size-sm font-weight-bold">Belum ada akses</span>
                                    @endforelse
                                @else
                                    <span class="text-muted font-size-sm">&mdash;</span>
                                @endif
                            </td>
                            <td class="text-right">
                                <a href="{{ route('admin.operator.edit', $op->id) }}" class="btn-icon-action btn-edit-modern">
                                    <i class="fas fa-pen"></i>Edit
                                </a>
                                <form method="POST" action="{{ route('admin.operator.destroy', $op->id) }}" class="d-inline"
                                      onsubmit="return confirm('Yakin mau hapus akun {{ $op->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-icon-action btn-delete-modern">
                                        <i class="fas fa-trash"></i>Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-6">
                                <i class="fas fa-inbox mb-2 d-block" style="font-size: 28px; opacity:.4;"></i>
                                Belum ada operator
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($operators->hasPages())
            <div class="mt-4">
                {{ $operators->links() }}
            </div>
            @endif
        </div>
    </div>

    <div class="mt-5">
        <a href="{{ auth()->user()->role === 'admin' ? route('admin.dashboard') : route('divisi.dashboard', auth()->user()->division->slug) }}"
           class="font-weight-bold text-muted">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Dashboard
        </a>
    </div>

</div>
@endsection