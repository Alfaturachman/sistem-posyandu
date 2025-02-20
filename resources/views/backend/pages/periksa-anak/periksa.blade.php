@extends('backend.layouts.app')

@section('title', 'Periksa Anak')

@section('content')

<div class="container-fluid">
    <div class="row pt-2">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h4 class="fw-bolder mb-3">Periksa Anak</h4>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Silahkan untuk periksa data anak dengan mengisi form di bawah ini.
                        </div>
                    </div>

                    <form action="{{ route('anak.store') }}" method="POST">
                        @csrf
                        <style>
                            .select2-container .select2-selection--single {
                                border: 1px solid #dfe5ef !important;
                                height: 42px !important; 
                                display: flex !important;
                                align-items: center !important;
                            }
                        
                            .select2-selection__arrow {
                                display: flex !important;
                                align-items: center !important;
                                height: 100% !important;
                            }
                        
                            .select2-selection__clear {
                                margin-left: 10px !important;
                                display: flex !important;
                                align-items: center !important;
                            }
                        </style>
                        
                        <div class="form-group mb-3">
                            <label for="search_nik" class="form-label">NIK</label>
                            <select id="search_nik" name="search_nik" class="form-control" required>
                                <option value="">- Pilih NIK Anak -</option>
                                <option value="3205012001010001">3205012001010001 - Ahmad Ramadhan</option>
                                <option value="3205022002020002">3205022002020002 - Putri Maharani</option>
                            </select>
                        </div>
                    
                        <div class="form-group mb-3">
                            <label for="nama_anak" class="form-label">Nama Anak</label>
                            <input type="text" class="form-control" id="nama_anak" name="nama_anak" placeholder="Masukkan Nama Anak" required>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label for="berat_badan" class="form-label">Berat Badan</label>
                                <input type="text" class="form-control" id="berat_badan" name="berat_badan" placeholder="Masukkan Berat Badan" required>
                            </div>

                            <div class="col-lg-6 mb-3">
                                <label for="tinggi_badan" class="form-label">Tinggi Badan</label>
                                <input type="text" class="form-control" id="tinggi_badan" name="tinggi_badan" placeholder="Masukkan Tinggi Badan" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label for="lingkar_lengan" class="form-label">Lingkar Lengan</label>
                                <input type="text" class="form-control" id="lingkar_lengan" name="lingkar_lengan" placeholder="Masukkan Lingkar Lengan" required>
                            </div>

                            <div class="col-lg-6 mb-3">
                                <label for="lingkar_kepala" class="form-label">Lingkar Kepala</label>
                                <input type="text" class="form-control" id="lingkar_kepala" name="lingkar_kepala" placeholder="Masukkan Lingkar Kepala" required>
                            </div>
                        </div>

                        <div class="form-group mb-2">
                            <label for="citra_telapak_kaki" class="form-label">Citra Telapak Kaki</label>
                            <input type="file" class="form-control" id="citra_telapak_kaki" name="citra_telapak_kaki" accept="image/png, image/jpeg, image/jpg" required onchange="previewImage(event)">
                            <p class="mt-2" style="color: #ff5757!important;">*Format file: JPG, PNG, JPEG</p>
                        </div>
                        
                        <!-- Preview gambar -->
                        <div class="mt-3">
                            <img id="preview" src="#" alt="Preview Gambar" class="img-thumbnail d-none" style="max-width: 200px;">
                        </div>                        

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary">Simpan Periksa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    function previewImage(event) {
        var input = event.target;
        var preview = document.getElementById('preview');

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none'); // Tampilkan gambar
            }

            reader.readAsDataURL(input.files[0]); // Convert gambar ke URL
        }
    }
</script>