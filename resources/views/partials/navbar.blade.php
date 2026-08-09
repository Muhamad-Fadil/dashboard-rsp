<nav class="navbar navbar-expand navbar-light bg-white shadow-sm px-4 py-3">
    <a class="navbar-brand font-weight-bolder text-primary mb-0" href="{{ url('/') }}">
        RSP Goenawan Cisarua
        <span class="text-dark-50 font-weight-normal font-size-sm">| SIMRS</span>
    </a>

    <div class="ml-auto d-flex align-items-center">
        <span class="badge badge-light-primary font-weight-bold mr-3 text-capitalize px-3 py-2">
            {{ auth()->user()->role }}
        </span>

        <div class="dropdown">
            <button class="btn btn-light-primary btn-sm dropdown-toggle font-weight-bold" type="button" data-toggle="dropdown">
                {{ auth()->user()->name }}
            </button>
            <div class="dropdown-menu dropdown-menu-right">
                @if (auth()->user()->role === 'direktur')
                    <a class="dropdown-item" href="{{ route('direktur.dashboard') }}">Pilih Divisi Lain</a>
                    <div class="dropdown-divider"></div>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item text-danger">Logout</button>
                </form>
            </div>
        </div>
    </div>
</nav>