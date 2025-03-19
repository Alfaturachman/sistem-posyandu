@extends('frontend.layouts.app')

@section('title', 'Portal Anak')

@section('content')
<!-- Hero Section -->
<section id="hero" class="hero section">
    <div class="hero-bg">
        <img src="{{ asset('frontend/assets/img/45667547.webp') }}" alt="">
    </div>
    <div class="container text-center">
        <div class="d-flex flex-column justify-content-center align-items-center">
            <img src="{{ asset('frontend/assets/img/logo_posyandu.png') }}" class="img-fluid hero-img mb-3" alt="" data-aos="zoom-out" data-aos-delay="300">
            <h1 data-aos="fade-up">Selamat Datang di <span>Posyandu</span></h1>
            <p data-aos="fade-up" data-aos-delay="100">Silahkan untuk melihat hasil pemeriksaan berdasarkan NIK buah hati Anda<br></p>
            <div class="d-flex" data-aos="fade-up" data-aos-delay="200">
                <a href="#portal" class="btn-get-started">Mulai Sekarang</a>
            </div>
            <img src="{{ asset('frontend/assets/img/4978784.webp') }}" class="img-fluid hero-img" alt="" data-aos="zoom-out" data-aos-delay="300">
        </div>
    </div>
</section>

<!-- Services Section -->
<section id="portal" class="services section">
    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>Portal Anak</h2>
        <p>Silahkan untuk melihat hasil pemeriksaan dan mencari berdasarkan NIK buah hati Anda</p>
    </div><!-- End Section Title -->

    <div class="container">
        <div class="row g-5">
            <div class="col" data-aos="fade-up" data-aos-delay="100">
                <div class="card">
                    <div class="card-header" style="color: white !important; background-color: var(--accent-color) !important;">
                        <h5 class="pt-2" style="color: white !important;">Pencarian Data</h5>
                    </div>
                    <div class="card-body">
                        <form id="searchForm">
                            <div class="mb-3">
                                <label for="keyword" class="form-label">Cari Data Periksa Anak berdasarkan NIK</label>
                                <div class="d-flex">
                                    <input type="text" class="form-control" id="searchInput" placeholder="Masukkan NIK" required>
                                    <button type="submit" class="btn ms-2" style="background: var(--accent-color); color: white;">Cari</button>
                                </div>
                            </div>
                        </form>
                        <div id="searchResults" class="mt-3">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.getElementById('searchForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Mencegah form submit secara default

        const searchInput = document.getElementById('searchInput').value;
        const searchResults = document.getElementById('searchResults');

        // Kosongkan hasil pencarian sebelumnya
        searchResults.innerHTML = '';

        // Ambil data dari server
        fetch(`/cari-pemeriksaan?nik=${searchInput}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Data tidak ditemukan');
                }
                return response.json();
            })
            .then(data => {
                if (data.anak) {
                    // Buat elemen ul untuk menampilkan hasil pencarian
                    const ulElement = document.createElement('ul');
                    ulElement.classList.add('list-group');

                    // Tampilkan data anak dengan anchor
                    const anakElement = document.createElement('li');
                    anakElement.classList.add('list-group-item', 'fw-bold');

                    // Buat elemen anchor (tautan)
                    const anakLink = document.createElement('a');
                    anakLink.href = `/portal/show/${data.anak.nik}`;
                    anakLink.textContent = `NIK: ${data.anak.nik} - Nama: ${data.anak.nama_anak}`;
                    anakLink.classList.add('text-decoration-none', 'text-dark', 'w-100', 'd-block');

                    // Tambahkan anchor ke dalam li
                    anakElement.appendChild(anakLink);
                    ulElement.appendChild(anakElement);

                    // Tambahkan ul ke dalam div hasil pencarian
                    searchResults.appendChild(ulElement);
                } else {
                    searchResults.innerHTML = '<span class="badge bg-warning py-2 px-3">Data tidak ditemukan. Coba lagi!</span>';
                }
            })
            .catch(error => {
                searchResults.innerHTML = '<span class="badge bg-warning py-2 px-3">Terjadi kesalahan. Coba lagi!</span>';
            });
    });
</script>
@endsection