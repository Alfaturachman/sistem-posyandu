@extends('frontend.layouts.app') @section('content')
<section id="hero" class="hero section">
    <div class="hero-bg">
      <img src="{{ asset('frontend/assets/img/45667547.webp') }}" alt="">
    </div>
    <div class="container text-center">
      <div class="d-flex flex-column justify-content-center align-items-center">
        <h1 data-aos="fade-up">Selamat Datang Ananda <span>{{ $anak->nama_anak }}</span></h1>
        <p data-aos="fade-up" data-aos-delay="100">Hasil data pemeriksaan buah hati bisa dilihat di bawah<br></p>
      </div>
    </div>
  </section>
<!-- Services Section -->
<section id="detail-anak" class="detail-anak section">
    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>Detail Informasi</h2>
    </div>
    <!-- End Section Title -->

    <div class="container">
        <div class="row g-5 d-flex justify-content-center">
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card">
                    <div class="card-body">
                        <table class="table table-borderless m-0">
                            <tbody>
                                <tr>
                                    <td class="text-sm-start fw-bold">NIK</td>
                                    <td>:</td>
                                    <td>{{ $anak->nik }}</td>
                                </tr>
                                <tr>
                                    <td class="text-sm-start fw-bold">Nama</td>
                                    <td>:</td>
                                    <td>{{ $anak->nama_anak }}</td>
                                </tr>
                                <tr>
                                    <td class="text-sm-start fw-bold">Tanggal Lahir</td>
                                    <td>:</td>
                                    <td>{{ $anak->tanggal_lahir }}</td>
                                </tr>
                                <tr>
                                    <td class="text-sm-start fw-bold">Jenis Kelamin</td>
                                    <td>:</td>
                                    <td>{{ $anak->jenis_kelamin }}</td>
                                </tr>
                                <tr>
                                    <td class="text-sm-start fw-bold">Alamat</td>
                                    <td>:</td>
                                    <td>{{ $anak->alamat }}</td>
                                </tr>
                                <tr>
                                    <td class="text-sm-start fw-bold">Nama Orang Tua/Wali</td>
                                    <td>:</td>
                                    <td>{{ $anak->nama_orang_tua }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</section>

<!-- Services Section -->
<section id="riwayat-anak" class="riwayat-anak section">
    <!-- Section Title -->
    <div class="container section-title" data-aos="fade-up">
        <h2>Riwayat Pemeriksaan</h2>
    </div>
    <!-- End Section Title -->

    <div class="container">
        <div class="row g-5">
            <div class="col" data-aos="fade-up" data-aos-delay="100">
                
                @if($pemeriksaans->isEmpty())
                <div class="alert alert-warning" role="alert">
                    Belum ada pemeriksaan.
                  </div>
                @else
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="background: var(--accent-color); color: white; padding: 10px;">No</th>
                                <th style="background: var(--accent-color); color: white; padding: 10px;">Tanggal Pemeriksaan</th>
                                <th style="background: var(--accent-color); color: white; padding: 10px;">Aksi</th>
                            </tr>
                        </thead>                        
                        <tbody>
                            @foreach($pemeriksaans as $pemeriksaan)
                            <tr>
                                <td style="vertical-align: middle;">
                                    {{ $loop->iteration }} <!-- Perulangan nomor -->
                                </td>                                
                                <td style="vertical-align: middle;">
                                    {{ \Carbon\Carbon::parse($pemeriksaan->tanggal_periksa)->translatedFormat('l, d F Y, H:i') }} WIB
                                </td>                                
                                <td>
                                    <!-- Tombol Detail -->
                                    <button class="btn" style="background: var(--accent-color); color: white;" data-bs-toggle="modal" data-bs-target="#detailModal{{ $pemeriksaan->id }}">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>                        
                    </table>
                </div>
                
                <!-- Modal untuk Detail -->
                @foreach($pemeriksaans as $pemeriksaan)
                <div class="modal fade" id="detailModal{{ $pemeriksaan->id }}" tabindex="-1" aria-labelledby="detailModalLabel{{ $pemeriksaan->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="detailModalLabel{{ $pemeriksaan->id }}">Detail Pemeriksaan</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="table-responsive">    
                                    <table class="table table-bordered">
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($pemeriksaan->tanggal_periksa)->translatedFormat('l, d F Y, H:i') }} WIB</td>
                                        </tr>
                                    </table>     
                                </div>
                                <h6><strong>Hasil Periksa</strong></h6>
                                <div class="table-responsive">    
                                    <table class="table table-bordered">
                                        <tr>
                                            <th>Berat Badan</th>
                                            <td>{{ $pemeriksaan->berat_badan }} kg</td>
                                        </tr>
                                        <tr>
                                            <th>Tinggi Badan</th>
                                            <td>{{ $pemeriksaan->tinggi_badan }} cm</td>
                                        </tr>
                                        <tr>
                                            <th>Lingkar Lengan</th>
                                            <td>{{ $pemeriksaan->lingkar_lengan }} cm</td>
                                        </tr>
                                        <tr>
                                            <th>Lingkar Kepala</th>
                                            <td>{{ $pemeriksaan->lingkar_kepala }} cm</td>
                                        </tr>
                                    </table>     
                                </div>
                            </div>                            
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @endif
            </div>
        </div>
    </div>
</section>
@endsection