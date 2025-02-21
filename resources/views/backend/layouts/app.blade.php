<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="stylesheet" href="{{ asset('backend/src/assets/images/logos/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('backend/src/assets/css/styles.min.css') }}" />
</head>

<body class="bg-light">
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

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/2.2.2/css/dataTables.dataTables.min.css">
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>

    <script>
        $(document).ready(function() {
            $('#id_anak').select2({
                placeholder: "Pilih NIK",
                width: '100%'
            });
        });
    </script>

    <script>
        $(document).ready(function() {
            $('#dataTables').DataTable({
                paging: true,
                searching: true,
                ordering: true,
                info: true,
                language: {
                    search: "Cari:",
                    lengthMenu: "Tampilkan _MENU_ data",
                    info: "Menampilkan _START_ - _END_ dari _TOTAL_ data",
                    infoEmpty: "Tidak ada data yang tersedia",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Berikutnya",
                        previous: "Sebelumnya"
                    }
                }
            });
        });
    </script>
</body>

</html>