@extends('backend.layouts.app')

@section('title', 'Periksa Anak')

@section('content')
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

                    <form action="{{ route('periksa.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="id_anak" class="form-label">NIK</label>
                            <select id="id_anak" name="id_anak" class="form-control" required>
                                <option value="">- Pilih NIK Anak -</option>
                                @foreach($anakList as $anak)
                                    <option value="{{ $anak->id }}" data-nama="{{ $anak->nama_anak }}">
                                        {{ $anak->nik }} - {{ $anak->nama_anak }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label for="berat_badan" class="form-label">Berat Badan</label>
                                <input type="number" class="form-control" id="berat_badan" name="berat_badan" placeholder="Masukkan Berat Badan" required>
                            </div>
                    
                            <div class="col-lg-6 mb-3">
                                <label for="tinggi_badan" class="form-label">Tinggi Badan</label>
                                <input type="number" class="form-control" id="tinggi_badan" name="tinggi_badan" placeholder="Masukkan Tinggi Badan" required>
                            </div>
                        </div>
                    
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label for="lingkar_lengan" class="form-label">Lingkar Lengan</label>
                                <input type="number" class="form-control" id="lingkar_lengan" name="lingkar_lengan" placeholder="Masukkan Lingkar Lengan" required>
                            </div>
                    
                            <div class="col-lg-6 mb-3">
                                <label for="lingkar_kepala" class="form-label">Lingkar Kepala</label>
                                <input type="number" class="form-control" id="lingkar_kepala" name="lingkar_kepala" placeholder="Masukkan Lingkar Kepala" required>
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

<!-- Modal Success -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Sukses Periksa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Periksa anak berhasil ditambahkan.
            </div>
            <div class="modal-footer">
                <a href="{{ route('hasil') }}" class="btn btn-secondary">Lihat Hasil</a>
                <a href="{{ route('periksa') }}" class="btn btn-primary">Kembali Periksa</a>
            </div>
        </div>
    </div>
</div>

<!-- Cek Session untuk Menampilkan Modal -->
@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var successModal = new bootstrap.Modal(document.getElementById('successModal'));
        successModal.show();
    });
</script>
@endif


<!-- Script Preview Gambar -->
<script>
    function previewImage(event) {
        var reader = new FileReader();
        reader.onload = function(){
            var preview = document.getElementById('preview');
            preview.src = reader.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>