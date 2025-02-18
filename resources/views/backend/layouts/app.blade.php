<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Dashboard')</title>
  <link rel="stylesheet" href="{{ asset('backend/src/assets/images/logos/favicon.png') }}" />
  <link rel="stylesheet" href="{{ asset('backend/src/assets/css/styles.min.css') }}" />
</head>

<body>
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
    data-sidebar-position="fixed" data-header-position="fixed">
    @include('backend.layouts.sidebar')
        <div class="body-wrapper">
            @include('backend.layouts.header')
            @yield('content')
            @include('backend.layouts.footer')
        </div>
    </div>

    <script src="{{ asset('backend/src/assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('backend/src/assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('backend/src/assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('backend/src/assets/js/app.min.js') }}"></script>
    <script src="{{ asset('backend/src/assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script src="{{ asset('backend/src/assets/libs/simplebar/dist/simplebar.js') }}"></script>
    <script src="{{ asset('backend/src/assets/js/dashboard.js') }}"></script>
</body>
</html>
