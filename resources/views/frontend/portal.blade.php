@extends('frontend.layouts.app')

@section('content')
<!-- Hero Section -->
<section id="hero" class="hero section">
    <div class="hero-bg">
      <img src="{{ asset('frontend/assets/img/45667547.webp') }}" alt="">
    </div>
    <div class="container text-center">
      <div class="d-flex flex-column justify-content-center align-items-center">
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
<section id="portal" class="services section my-5">
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
                    <!-- Hasil pencarian akan ditampilkan di sini -->
                  </div>
                </div>
            </div>
        </div>
      </div>
    </div>
</section>

<script>
    // Data dummy untuk simulasi pencarian
    const dummyData = [
      { nik: '1234567890', nama: 'John Doe', alamat: 'Jl. Merdeka No. 123', halaman: '/halaman/john' },
      { nik: '0987654321', nama: 'Jane Doe', alamat: 'Jl. Sudirman No. 456', halaman: '/halaman/jane' },
      { nik: '1122334455', nama: 'Alice Smith', alamat: 'Jl. Gatot Subroto No. 789', halaman: '/halaman/alice' },
    ];
  
    document.getElementById('searchForm').addEventListener('submit', function(event) {
      event.preventDefault(); // Mencegah form submit
  
      const searchInput = document.getElementById('searchInput').value.toLowerCase();
      const searchResults = document.getElementById('searchResults');
  
      // Filter data berdasarkan NIK
      const filteredData = dummyData.filter(item => item.nik.includes(searchInput));
  
      // Kosongkan hasil pencarian sebelumnya
      searchResults.innerHTML = '';
  
      if (filteredData.length > 0) {
        // Buat elemen ul untuk menampilkan hasil pencarian
        const ulElement = document.createElement('ul');
        ulElement.classList.add('list-group');
  
        // Tambahkan item hasil pencarian ke dalam ul
        filteredData.forEach(item => {
          const liElement = document.createElement('li');
          liElement.classList.add('list-group-item');
  
          // Buat elemen anchor (tautan)
          const aElement = document.createElement('a');
          aElement.href = item.halaman; // Tautan ke halaman yang diinginkan
          aElement.textContent = `${item.nik} - ${item.nama}`;
          aElement.classList.add('text-decoration-none', 'text-dark');
  
          // Masukkan tautan ke dalam li
          liElement.appendChild(aElement);
          ulElement.appendChild(liElement);
        });
  
        // Tambahkan ul ke dalam div hasil pencarian
        searchResults.appendChild(ulElement);
      } else {
        // Tampilkan pesan jika tidak ada hasil
        searchResults.innerHTML = '<span class="badge bg-warning py-2 px-3">Tidak ada NIK yang ditemukan. Coba lagi!</span>';
      }
    });
  </script>  
@endsection