<nav class="navbar navbar-expand navbar-light bg-white shadow-sm px-4 py-3 " style="background-image: url({{ asset('assets/media/bg/bg-10.jpg') }});">
    <img src="{{ asset('assets/media/logos/logo-kemenkes.png') }}" alt="Logo" class="max-h-60px my-2 mx-3" />
    <a class="navbar-brand font-weight-bolder text-white mb-0 font-size-h2" href="{{ url('/') }}">
        RSP Goenawan Cisarua
        <span class="text-white font-weight-normal font-size-h3">| SIMRS</span>
    </a>

    <div class="ml-auto d-flex align-items-center text-white">
        <span class="badge badge-light-primary font-weight-bold mr-3 text-capitalize px-3 py-2 font-size-h4">
            {{ auth()->user()->role }}
        </span>

        <div class="dropdown">
            <button class="btn btn-success btn-sm dropdown-toggle font-weight-bold font-size-lg   " type="button" data-toggle="dropdown">
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