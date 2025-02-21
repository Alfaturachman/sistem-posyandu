@extends('backend.layouts.app')

@section('title', 'Detail Periksa')

@section('content')

<div class="container-fluid">
    <div class="row pt-2">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h4 class="fw-bolder mb-3">Edit Identitas - {{ $anak->nama_anak }}</h4>
                        <div id="alert" class="alert alert-success" role="alert">
                            Silahkan untuk periksa data anak dengan mengisi form di bawah ini.
                        </div>
                    </div>
                    <form action="{{ route('update-identitas', $anak->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">NIK</label>
                            <input type="text" name="nik" class="form-control" value="{{ $anak->nik }}" placeholder="Masukkan NIK" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Nama Anak</label>
                            <input type="text" name="nama_anak" class="form-control" value="{{ $anak->nama_anak }}" placeholder="Masukkan Nama Anak" required>
                        </div>
                
                        <div class="mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input type="date" name="tanggal_lahir" class="form-control" value="{{ $anak->tanggal_lahir }}" required>
                        </div>
                
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select name="jenis_kelamin" class="form-control" required>
                                <option value="Laki-laki" {{ $anak->jenis_kelamin == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="Perempuan" {{ $anak->jenis_kelamin == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>
                
                        <div class="mb-3">
                            <label class="form-label">Nama Orang Tua/Wali</label>
                            <input type="text" name="nama_ibu" class="form-control" placeholder="Masukkan Nama Ibu" value="{{ $anak->nama_ibu }}" required>
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-between pt-3">
                            <a href="{{ route('anak.detail', $anak->id) }}" class="btn btn-secondary">Batal</a>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Success -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="successModalLabel">Sukses Edit Identitas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Identitas anak berhasil diperbarui.
            </div>
            <div class="modal-footer">
                <a href="{{ route('anak.detail', $anak->id) }}" class="btn btn-primary">OK</a>
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

@endsection