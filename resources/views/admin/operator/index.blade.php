@extends('layouts.dashboard')

@section('title', 'Kelola Operator')

@push('styles')
@include('partials.dashboard-styles')
@endpush

@section('content')
<div class="container-fluid px-6 py-6">

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div>
            <h1 class="font-weight-bolder mb-1">Kelola Operator</h1>
            <span class="text-muted-light font-weight-bold">
                @if (auth()->user()->role === 'manajer')
                    Kelola akun operator divisi {{ auth()->user()->division->name }}
                @else
                    Kelola seluruh akun operator
                @endif
            </span>
        </div>
        <div>
            @if (auth()->user()->role === 'manajer')
                <a href="{{ route('divisi.dashboard', auth()->user()->division->slug) }}" class="btn btn-light font-weight-bold px-6 mr-2">&larr; Kembali ke Dashboard</a>
            @else
                <a href="{{ route('admin.dashboard') }}" class="btn btn-light font-weight-bold px-6 mr-2">&larr; Kembali ke Admin Panel</a>
            @endif
            <a href="{{ route('admin.operator.create') }}" class="btn btn-light font-weight-bold px-6">+ Tambah Operator</a>
        </div>
    </div>

    @if (session('status'))
        <div class="data-freshness" style="background:#E8FFF3; color:#1BC5BD;">{{ session('status') }}</div>
    @endif

    <div class="card modern-card">
        <div class="card-body p-5">

            @if (auth()->user()->role === 'admin')
            <form method="GET" class="mb-5">
                <label class="font-weight-bold font-size-sm text-muted mr-2">Filter Divisi</label>
                <select name="division_id" onchange="this.form.submit()" class="form-control form-control-solid d-inline-block" style="width: 220px;">
                    <option value="">Semua Divisi</option>
                    @foreach ($divisions as $d)
                        <option value="{{ $d->id }}" @selected($filterDivisionId == $d->id)>{{ $d->name }}</option>
                    @endforeach
                </select>
            </form>
            @endif

            <div class="table-responsive">
                <table class="table table-modern">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Divisi</th>
                            <th>Akses Sub-menu</th>
                            <th></th>
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
                            <td>{{ $op->email }}</td>
                            <td>{{ $op->division->name ?? '-' }}</td>
                            <td>
                                @if ($op->division->slug === 'layanan')
                                    @forelse ($op->submenuAkses as $akses)
                                        <span class="badge-modern" style="background:#EEF3FF; color:#6993FF;">{{ \App\Models\User::daftarSubmenuLayanan()[$akses->submenu] ?? $akses->submenu }}</span>
                                    @empty
                                        <span class="badge-modern" style="background:#FFE9EA; color:#F64E60;">Belum ada akses</span>
                                    @endforelse
                                @else
                                    <span class="text-muted font-size-sm">-</span>
                                @endif
                            </td>
                            <td class="nowrap">
                                <a href="{{ route('admin.operator.edit', $op->id) }}" class="btn btn-sm btn-light-primary font-weight-bold mr-2">Edit</a>
                                <form method="POST" action="{{ route('admin.operator.destroy', $op->id) }}" style="display:inline;" onsubmit="return confirm('Yakin mau hapus akun {{ $op->name }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm font-weight-bold" style="background:#FFE9EA; color:#F64E60; border:none;">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-6">Belum ada operator</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-5">{{ $operators->links() }}</div>
        </div>
    </div>

</div>
@endsection