@extends('layouts.dashboard')

@section('title', 'Distribusi Pegawai')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Poppins', sans-serif; }
    .page-header {
        background: linear-gradient(135deg, #8950FC 0%, #6236DB 100%);
        border-radius: 18px; padding: 28px 32px; color: #fff;
        box-shadow: 0 10px 30px rgba(137,80,252,.25);
    }
    .page-header h1 { color: #fff; }
    .page-header .text-muted-light { color: rgba(255,255,255,.8) !important; }
    .modern-card { background: #fff; border-radius: 16px; border: none; box-shadow: 0 4px 18px rgba(0,0,0,.06); }
    .badge-modern { border-radius: 20px; padding: 5px 12px; font-weight: 600; font-size: 11px; }

    .unit-toggle {
        display: flex; justify-content: space-between; align-items: center;
        padding: 18px 22px; cursor: pointer; user-select: none;
    }
    .unit-toggle:hover { background: #f9f9fb; }
    .unit-toggle .chevron { transition: transform .2s ease; color: #a1a5b7; }
    .unit-toggle.open .chevron { transform: rotate(180deg); }
    .unit-body { display: none; padding: 0 22px 20px; }
    .unit-body.open { display: block; }

    .pegawai-chip {
        display: inline-flex; align-items: center; gap: 8px;
        background: #F9F9FB; border-radius: 10px; padding: 8px 12px; margin: 4px;
        font-size: 13px; font-weight: 500; color: #464E5F;
    }
    .pegawai-chip .dot { width: 6px; height: 6px; border-radius: 50%; background: #8950FC; }
</style>
@endpush

@section('content')
<div class="container-fluid px-6 py-6">
    @include('partials.submenu-sdm')

    <div class="page-header d-flex justify-content-between align-items-center flex-wrap mb-6">
        <div>
            <h1 class="font-weight-bolder mb-1"><i class="fas fa-building mr-2"></i>Distribusi Pegawai</h1>
            <span class="text-muted-light font-weight-bold">Jumlah pegawai per unit kerja, {{ $distribusi->count() }} unit total</span>
        </div>
    </div>

    <div class="card modern-card mb-6">
        @foreach ($distribusi as $i => $unit)
        <div class="unit-toggle {{ $loop->first ? 'open' : '' }} {{ !$loop->first ? 'border-top' : '' }}" onclick="toggleUnit({{ $i }})" style="border-color:#f1f1f4;">
            <div class="d-flex align-items-center">
                <span class="font-weight-bolder text-dark mr-3" style="font-size: 15px;">{{ $unit['nama_unit'] }}</span>
                <span class="badge badge-modern" style="background:#F1E9FF; color:#8950FC;">{{ $unit['total'] }} pegawai</span>
            </div>
            <i class="fas fa-chevron-down chevron"></i>
        </div>
        <div class="unit-body {{ $loop->first ? 'open' : '' }}" id="unit-body-{{ $i }}">
            @forelse ($unit['pegawai'] as $p)
                <span class="pegawai-chip"><span class="dot"></span>{{ $p->nama }} <span class="text-muted">&middot; {{ $p->profesi->nama_profesi ?? '-' }}</span></span>
            @empty
                <p class="text-muted">Belum ada pegawai di unit ini</p>
            @endforelse
        </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleUnit(i) {
        const body = document.getElementById('unit-body-' + i);
        const toggle = body.previousElementSibling;
        body.classList.toggle('open');
        toggle.classList.toggle('open');
    }
</script>
@endpush