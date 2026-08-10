<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <title>@yield('title', 'Dashboard') - SIMRS RSP Goenawan Cisarua</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />

    <style>
        .icon-inline {
            width: 1em; height: 1em;
            display: inline-block;
            vertical-align: -0.125em;
        }
        nav[role="navigation"] svg {
            width: 16px !important;
            height: 16px !important;
        }
    </style>

    @stack('styles')
</head>
<body id="kt_body" style="background:#f4f6f9;">

    @include('partials.navbar')

    <div class="d-flex flex-column flex-column-fluid">
        @yield('content')
    </div>

    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    @stack('scripts')
</body>
</html>