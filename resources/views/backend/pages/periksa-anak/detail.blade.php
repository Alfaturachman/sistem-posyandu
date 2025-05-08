@extends('backend.layouts.app')

@section('title', 'Detail Periksa')

@section('content')

<div class="container-fluid">
    <div class="row pt-2">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h4 class="fw-bolder mb-3">Detail Hasil Periksa Anak</h4>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5>Detail Identitas Anak</h5>
                        <a class="btn btn-warning" href="{{ route('edit-identitas', $anak->id) }}">
                            <i class="ti ti-edit"></i> Edit Identitas
                        </a>
                    </div>
                    <div class="table-responsive mb-3">
                        <table class="table">
                            <tr>
                                <th>NIK</th>
                                <td>{{ $anak->nik }}</td>
                            </tr>
                            <tr>
                                <th>Nama</th>
                                <td>{{ $anak->nama_anak }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Lahir</th>
                                <td>{{ \Carbon\Carbon::parse($anak->tanggal_lahir)->translatedFormat('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>Jenis Kelamin</th>
                                <td>{{ $anak->jenis_kelamin }}</td>
                            </tr>
                            <tr>
                                <th>Nama Orang Tua/Wali</th>
                                <td>{{ $anak->nama_ibu }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="d-block align-items-center justify-content-center mb-3">
                        <h5>Riwayat Pemeriksaan Anak</h5>
                    </div>
                    <div class="table-responsive">
                        @if($anak->pemeriksaans->isEmpty())
                        <div class="alert alert-warning text-center" role="alert">
                            Belum ada data pemeriksaan untuk ananda {{ $anak->nama_anak }}.
                        </div>
                        @else
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tanggal Periksa</th>
                                    <th>Berat Badan (kg)</th>
                                    <th>Tinggi Badan (cm)</th>
                                    <th>Lingkar Lengan (cm)</th>
                                    <th>Lingkar Kepala (cm)</th>
                                    <th>Citra Kaki</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($anak->pemeriksaans as $periksa)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($periksa->tanggal_periksa)->translatedFormat('d F Y') }}</td>
                                    <td>{{ $periksa->berat_badan }}</td>
                                    <td>{{ $periksa->tinggi_badan }}</td>
                                    <td>{{ $periksa->lingkar_lengan }}</td>
                                    <td>{{ $periksa->lingkar_kepala }}</td>
                                    <td>
                                        @if(optional($periksa->citraTelapakKaki)->path_citra)
                                        <img src="{{ asset('storage/' . $periksa->citraTelapakKaki->path_citra) }}"
                                            alt="Citra Telapak Kaki"
                                            class="img-fluid"
                                            width="300">
                                        @else
                                        <p>Tidak ada gambar</p>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" 
                                                class="btn btn-danger btn-sm delete-btn"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#confirmDeleteModal"
                                                data-id="{{ $periksa->id }}">
                                                <i class="ti ti-trash"></i> Hapus
                                        </button>
                                        
                                        <form id="deleteForm-{{ $periksa->id }}" 
                                              action="{{ route('pemeriksaan.destroy', $periksa->id) }}" 
                                              method="POST" 
                                              class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @endif
                    </div>
                    <a href="{{ route('hasil') }}" class="btn btn-secondary mt-3">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteModal">Hapus Periksa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Anda yakin ingin menghapus data ini?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Success -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="fas fa-check-circle text-success fs-1 mb-3"></i>
                <h5>Data berhasil dihapus!</h5>
                <div class="mt-4">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Modal Konfirmasi Hapus
        const confirmModal = new bootstrap.Modal('#confirmDeleteModal');
        const successModal = new bootstrap.Modal('#successModal');
        let deleteFormId = null;
        
        // Tangkap klik tombol delete
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                deleteFormId = this.getAttribute('data-id');
            });
        });
        
        // Konfirmasi hapus
        document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
            if (deleteFormId) {
                fetch(document.getElementById('deleteForm-' + deleteFormId).action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        _method: 'DELETE'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        confirmModal.hide();
                        successModal.show();
                        // Optional: Refresh data atau update UI
                        setTimeout(() => window.location.reload(), 1500);
                    }
                });
            }
        });

        // Tampilkan modal success jika ada session
        @if(session('success'))
            successModal.show();
        @endif
    });
</script>
@endsection