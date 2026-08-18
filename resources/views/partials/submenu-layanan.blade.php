@php
    $menuLayanan = [
        ['label' => 'Ringkasan', 'route' => 'divisi.dashboard', 'aktif' => true, 'submenu' => null],
        ['label' => 'Data Pasien', 'route' => 'divisi.layanan.pasien', 'aktif' => true, 'submenu' => 'pasien'],
        ['label' => 'Kunjungan', 'route' => 'divisi.layanan.kunjungan', 'aktif' => true, 'submenu' => 'kunjungan'],
        ['label' => 'Rawat Inap', 'route' => 'divisi.layanan.rawat-inap', 'aktif' => true, 'submenu' => 'rawat-inap'],
        ['label' => 'Operasi', 'route' => 'divisi.layanan.operasi', 'aktif' => true, 'submenu' => 'operasi'],
        ['label' => 'Laboratorium', 'route' => 'divisi.layanan.laboratorium', 'aktif' => true, 'submenu' => 'laboratorium'],
        ['label' => 'Radiologi', 'route' => 'divisi.layanan.radiologi', 'aktif' => true, 'submenu' => 'radiologi'],
        ['label' => 'Resep', 'route' => 'divisi.layanan.resep', 'aktif' => true, 'submenu' => 'resep'],
    ];

    // Kalau yang login Operator, saring cuma tab yang diizinkan (Ringkasan otomatis ke-skip karena submenu=null)
    if (auth()->user()->role === 'operator') {
        $menuLayanan = array_filter($menuLayanan, function ($menu) {
            return $menu['submenu'] && auth()->user()->bisaAksesSubmenu($menu['submenu']);
        });
    }
@endphp

<style>
    .submenu-layanan {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(0,0,0,.06);
        overflow-x: auto;
        margin-bottom: 24px;
    }
    .submenu-layanan .submenu-track {
        display: flex;
        min-width: max-content;
        padding: 8px;
        gap: 4px;
    }
    .submenu-item {
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        white-space: nowrap;
        color: #7e8299;
    }
    .submenu-item.aktif {
        background: #EEF3FF;
        color: #4D6FE0;
    }
    .submenu-item.nonaktif {
        color: #c4c7d5;
        cursor: not-allowed;
    }
    .submenu-item:not(.nonaktif):not(.aktif):hover {
        background: #f4f6f9;
        color: #464E5F;
    }
</style>

<div class="submenu-layanan">
    <div class="submenu-track">
        @foreach ($menuLayanan as $menu)
            @if ($menu['route'])
                <a href="{{ route($menu['route'], $division->slug) }}"
                   class="submenu-item {{ request()->routeIs($menu['route']) ? 'aktif' : '' }}">
                    {{ $menu['label'] }}
                </a>
            @else
                <span class="submenu-item nonaktif" title="Segera hadir">
                    {{ $menu['label'] }}
                </span>
            @endif
        @endforeach
    </div>
</div>