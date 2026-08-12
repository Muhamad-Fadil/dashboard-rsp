@php
    $menuKeuangan = [
        ['label' => 'Ringkasan', 'route' => 'divisi.dashboard', 'aktif' => true],
        ['label' => 'Pendapatan', 'route' => 'divisi.keuangan.pendapatan', 'aktif' => true],
        ['label' => 'Pengeluaran', 'route' => 'divisi.keuangan.pengeluaran', 'aktif' => true],
        ['label' => 'Cash Flow', 'route' => 'divisi.keuangan.cash-flow', 'aktif' => true],
        ['label' => 'Klaim BPJS', 'route' => 'divisi.keuangan.klaim-bpjs', 'aktif' => true],
    ];
@endphp

<style>
    .submenu-keuangan {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(0,0,0,.06);
        overflow-x: auto;
        margin-bottom: 24px;
    }

    .submenu-keuangan .submenu-track {
        display: flex;
        min-width: max-content;
        padding: 8px;
        gap: 4px;
    }

    .submenu-keuangan .submenu-item {
        padding: 10px 18px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        white-space: nowrap;
        color: #7e8299;
    }

    .submenu-keuangan .submenu-item.aktif {
        background: #E8FFF3;
        color: #1BC5BD;
    }

    .submenu-keuangan .submenu-item.nonaktif {
        color: #c4c7d5;
        cursor: not-allowed;
    }

    .submenu-keuangan .submenu-item:not(.nonaktif):not(.aktif):hover {
        background: #f4f6f9;
        color: #464E5F;
    }
</style>

<div class="submenu-keuangan">
    <div class="submenu-track">
        @foreach ($menuKeuangan as $menu)
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