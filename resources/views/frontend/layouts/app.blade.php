<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <title>@yield('title', 'Home')</title>
    <meta name="description" content="">
    <meta name="keywords" content="">

    <!-- Favicons -->
    <link href="assets/img/favicon.png" rel="icon">
    <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Comic+Neue:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://fonts.googleapis.com" rel="preconnect">
    <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Plus+Jakarta+Sans:wght@100;200;300;400;500;600;700;800;900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <link href="{{ asset('frontend/assets/vendor/bootstrap/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/aos/aos.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/glightbox/css/glightbox.min.css') }}" rel="stylesheet">
    <link href="{{ asset('frontend/assets/vendor/swiper/swiper-bundle.min.css') }}" rel="stylesheet">

    <!-- Tambahkan ini di <head> jika belum ada -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Main CSS File -->
    <link href="{{ asset('frontend/assets/css/main.css') }}" rel="stylesheet">
</head>

<style>
    :root {
        --primary-color: #FF6B9D;
        --secondary-color: #4ECDC4;
        --accent-color: #45B7D1;
        --success-color: #96CEB4;
        --warning-color: #FFEAA7;
        --purple-color: #A29BFE;
        --orange-color: #FD79A8;
        --yellow-color: #FDCB6E;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Fredoka', cursive;
        background: linear-gradient(135deg, #00bfff 0%, #0066ff 100%) !important;
        min-height: 100vh;
        position: relative;
        overflow-x: hidden;
    }

    /* Animated background elements */
    body::before {
        content: '';
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image:
            radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255, 107, 157, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 40% 40%, rgba(78, 205, 196, 0.3) 0%, transparent 50%);
        z-index: -2;
    }

    /* Floating shapes */
    .floating-shape {
        position: absolute;
        opacity: 0.1;
        animation: float 6s ease-in-out infinite;
        z-index: 0;
    }

    .floating-shape:nth-child(1) {
        top: 10%;
        left: 10%;
        animation-delay: 0s;
    }

    .floating-shape:nth-child(2) {
        top: 20%;
        right: 10%;
        animation-delay: 2s;
    }

    .floating-shape:nth-child(3) {
        bottom: 20%;
        left: 20%;
        animation-delay: 4s;
    }

    .floating-shape:nth-child(4) {
        top: 40%;
        left: 5%;
        animation-delay: 1s;
    }

    .floating-shape:nth-child(5) {
        top: 50%;
        right: 15%;
        animation-delay: 3s;
    }

    .floating-shape:nth-child(6) {
        bottom: 30%;
        right: 25%;
        animation-delay: 5s;
    }

    .floating-shape:nth-child(7) {
        top: 70%;
        left: 30%;
        animation-delay: 1.5s;
    }

    .floating-shape:nth-child(8) {
        top: 15%;
        left: 50%;
        animation-delay: 2.5s;
    }

    .floating-shape:nth-child(9) {
        bottom: 15%;
        left: 5%;
        animation-delay: 4.5s;
    }

    .floating-shape:nth-child(10) {
        bottom: 5%;
        right: 10%;
        animation-delay: 0.5s;
    }

    .floating-shape:nth-child(11) {
        top: 25%;
        right: 40%;
        animation-delay: 3.5s;
    }

    .floating-shape:nth-child(12) {
        bottom: 40%;
        left: 40%;
        animation-delay: 5.5s;
    }

    .floating-shape:nth-child(13) {
        top: 60%;
        right: 5%;
        animation-delay: 1.2s;
    }

    .floating-shape:nth-child(14) {
        bottom: 60%;
        left: 10%;
        animation-delay: 2.8s;
    }

    .floating-shape:nth-child(15) {
        top: 35%;
        left: 45%;
        animation-delay: 4.2s;
    }

    .floating-shape:nth-child(16) {
        bottom: 45%;
        right: 35%;
        animation-delay: 0.8s;
    }

    .floating-shape:nth-child(17) {
        top: 80%;
        left: 15%;
        animation-delay: 3.8s;
    }

    .floating-shape:nth-child(18) {
        bottom: 25%;
        right: 50%;
        animation-delay: 5.2s;
    }

    .floating-shape:nth-child(19) {
        top: 5%;
        right: 30%;
        animation-delay: 1.8s;
    }

    .floating-shape:nth-child(20) {
        bottom: 10%;
        left: 60%;
        animation-delay: 4.8s;
    }

    @keyframes float {

        0%,
        100% {
            transform: translateY(0px) rotate(0deg);
        }

        33% {
            transform: translateY(-20px) rotate(10deg);
        }

        66% {
            transform: translateY(-10px) rotate(-10deg);
        }
    }

    /* Header styles */
    .navbar {
        background: rgba(255, 255, 255, 0.95) !important;
        backdrop-filter: blur(10px);
        padding: 1rem 0;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand {
        font-weight: 700;
        font-size: 2rem;
        background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Hero section */
    .hero {
        /* padding: 100px 0; */
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero h1 {
        font-size: 3.5rem;
        font-weight: 700;
        color: white;
        margin-bottom: 1.5rem;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .hero .highlight {
        background: linear-gradient(45deg, var(--primary-color), var(--orange-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        display: inline-block;
        transform: rotate(-2deg);
        padding: 0 10px;
    }

    .hero p {
        font-size: 1.3rem;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 3rem;
        font-family: 'Comic Neue', cursive;
    }

    .btn-get-started {
        background: linear-gradient(45deg, var(--primary-color), var(--orange-color));
        color: white;
        padding: 15px 40px;
        border: none;
        border-radius: 50px;
        font-size: 1.2rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(255, 107, 157, 0.4);
        position: relative;
        overflow: hidden;
    }

    .btn-get-started::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s;
    }

    .btn-get-started:hover::before {
        left: 100%;
    }

    .btn-get-started:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 12px 35px rgba(255, 107, 157, 0.5);
        color: white;
    }

    /* Hero images */
    .hero-img {
        max-width: 300px;
        margin: 2rem 0;
        position: relative;
        z-index: 99;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        animation: bounce 3s ease-in-out infinite;
    }

    @keyframes bounce {

        0%,
        100% {
            transform: translateY(0px);
        }

        50% {
            transform: translateY(-10px);
        }
    }

    /* Portal section */
    .portal-section {
        padding: 80px 0;
        position: relative;
    }

    .section-title h2 {
        font-size: 3rem;
        font-weight: 700;
        text-align: center;
        margin-bottom: 1rem;
        background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
        -webkit-background-clip: text;
        /* -webkit-text-fill-color: transparent; */
        background-clip: text;
    }

    .section-title p {
        text-align: center;
        font-size: 1.2rem;
        color: rgba(255, 255, 255, 0.9);
        margin-bottom: 3rem;
        font-family: 'Comic Neue', cursive;
    }

    /* Card styles */
    .search-card {
        background: rgba(255, 255, 255, 0.95);
        border: none;
        border-radius: 25px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        position: relative;
        transform: translateY(0);
        transition: all 0.3s ease;
    }

    .search-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 30px 60px rgba(0, 0, 0, 0.15);
    }

    .search-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(45deg, var(--primary-color), var(--secondary-color), var(--accent-color));
    }

    .card-header {
        background: var(--accent-color) !important;
        color: white !important;
        padding: 1.5rem;
        border: none !important;
        position: relative;
        overflow: hidden;
    }

    .card-header::before {
        position: absolute;
        right: 20px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 2rem;
        opacity: 0.3;
    }

    .card-header h5 {
        color: white !important;
        font-weight: 600;
        font-size: 1.4rem;
        margin: 0;
    }

    .card-body {
        padding: 2rem;
    }

    /* Form styles */
    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.8rem;
        font-size: 1.1rem;
    }

    .form-control {
        border: 3px solid #e1e8ed;
        border-radius: 15px;
        padding: 15px 20px;
        font-size: 1.1rem;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
    }

    .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(255, 107, 157, 0.25);
        background: white;
    }

    .btn-search {
        background: linear-gradient(45deg, var(--secondary-color), var(--accent-color));
        border: none;
        border-radius: 15px;
        padding: 15px 25px;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-search::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s;
    }

    .btn-search:hover::before {
        left: 100%;
    }

    .btn-search:hover {
        transform: scale(1.05);
        box-shadow: 0 5px 15px rgba(78, 205, 196, 0.4);
    }

    /* Search results */
    .search-results {
        margin-top: 2rem;
    }

    .list-group-item {
        border: none;
        border-radius: 15px !important;
        margin-bottom: 10px;
        background: linear-gradient(45deg, rgba(78, 205, 196, 0.1), rgba(69, 183, 209, 0.1));
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .list-group-item::before {
        content: '👶';
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.2rem;
    }

    .list-group-item a {
        padding-left: 40px;
        display: block;
        padding: 15px 15px 15px 50px;
        color: #333 !important;
        text-decoration: none !important;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .list-group-item:hover {
        transform: translateX(10px);
        background: linear-gradient(45deg, rgba(78, 205, 196, 0.2), rgba(69, 183, 209, 0.2));
    }

    .badge {
        border-radius: 25px;
        padding: 10px 20px;
        font-size: 1rem;
        font-weight: 500;
    }

    .bg-warning {
        background: linear-gradient(45deg, var(--warning-color), var(--yellow-color)) !important;
        color: #333 !important;
    }

    /* Footer */
    .footer {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        padding-top: 5rem;
        position: relative;
    }

    .footer::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(45deg, var(--primary-color), var(--secondary-color), var(--accent-color), var(--purple-color));
    }

    .sitename {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(45deg, var(--primary-color), var(--accent-color));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .footer-contact p {
        color: #666;
        margin-bottom: 0.5rem;
        font-family: 'Comic Neue', cursive;
    }

    .copyright {
        background: rgba(0, 0, 0, 0.05);
        padding: 1rem 0;
        margin-top: 2rem;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .fade-in-up {
        animation: fadeInUp 0.8s ease forwards;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .hero h1 {
            font-size: 2.5rem;
        }

        .hero p {
            font-size: 1.1rem;
        }

        .section-title h2 {
            font-size: 2.2rem;
        }

        .navbar-brand {
            font-size: 1.5rem;
        }
    }
</style>

<body style="background: linear-gradient(135deg, #00bfff 0%, #0066ff 100%) !important;">
    <!-- Floating Shapes -->
    <div class="floating-shape">
        <i class="fas fa-star" style="font-size: 2.5rem; color: var(--warning-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-heart" style="font-size: 3rem; color: var(--primary-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-smile" style="font-size: 3.5rem; color: var(--secondary-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-heart" style="font-size: 2.8rem; color: var(--primary-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-star" style="font-size: 3rem; color: var(--warning-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-smile" style="font-size: 3rem; color: var(--secondary-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-heart" style="font-size: 3.2rem; color: var(--primary-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-star" style="font-size: 2.3rem; color: var(--warning-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-smile" style="font-size: 3.4rem; color: var(--secondary-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-heart" style="font-size: 3.1rem; color: var(--primary-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-star" style="font-size: 2.7rem; color: var(--warning-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-smile" style="font-size: 3rem; color: var(--secondary-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-heart" style="font-size: 2.9rem; color: var(--primary-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-star" style="font-size: 3.2rem; color: var(--warning-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-smile" style="font-size: 3.3rem; color: var(--secondary-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-heart" style="font-size: 3rem; color: var(--primary-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-star" style="font-size: 2.6rem; color: var(--warning-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-smile" style="font-size: 3.1rem; color: var(--secondary-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-heart" style="font-size: 3.4rem; color: var(--primary-color);"></i>
    </div>
    <div class="floating-shape">
        <i class="fas fa-star" style="font-size: 2.4rem; color: var(--warning-color);"></i>
    </div>

    @include('frontend.layouts.header')
    <main class="main">
        @yield('content')
    </main>
    @include('frontend.layouts.footer')
    <!-- Scroll Top -->
    <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

    <!-- Preloader -->
    <div id="preloader"></div>

    <!-- Vendor JS Files -->
    <script src="{{ asset('frontend/assets/vendor/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/php-email-form/validate.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/aos/aos.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/glightbox/js/glightbox.min.js') }}"></script>
    <script src="{{ asset('frontend/assets/vendor/swiper/swiper-bundle.min.js') }}"></script>

    <!-- Main JS File -->
    <script src="{{ asset('frontend/assets/js/main.js') }}"></script>

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

    <style></style>

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
                        first: "<i class='fas fa-angle-double-left'></i>", // Ikon untuk "Pertama"
                        last: "<i class='fas fa-angle-double-right'></i>", // Ikon untuk "Terakhir"
                        next: "<i class='fas fa-chevron-right'></i>", // Ikon untuk "Berikutnya"
                        previous: "<i class='fas fa-chevron-left'></i>" // Ikon untuk "Sebelumnya"
                    }
                }
            });
        });
    </script>
</body>

</html>