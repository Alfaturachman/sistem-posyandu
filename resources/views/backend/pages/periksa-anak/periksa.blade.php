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

                    <form id="form-periksa" action="{{ route('periksa.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group mb-3">
                            <label for="id_anak" class="form-label">NIK</label>
                            <select id="id_anak" name="id_anak" class="form-control" required>
                                <option value="">- Pilih NIK Anak -</option>
                                @foreach($anakList as $anak)
                                <option value="{{ $anak->id }}">{{ $anak->nik }} - {{ $anak->nama_anak }}</option>
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
                            <div class="input-group">
                                <input type="file" class="form-control" id="citra_telapak_kaki" name="citra_telapak_kaki" accept="image/*" style="display: none;" onchange="updateFileInfo()">
                                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('citra_telapak_kaki').click()">
                                    <i class="fas fa-upload"></i> Unggah File
                                </button>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#cameraModal">
                                    <i class="fas fa-camera"></i> Ambil Foto
                                </button>
                            </div>
                            <small id="fileInfo" class="text-muted">Belum ada file yang dipilih</small>
                            <div id="fileNameDisplay" class="mt-1" style="display: none;">
                                <span class="badge bg-info">
                                    <i class="fas fa-file-image me-1"></i>
                                    <span id="selectedFileName"></span>
                                    <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 0.7rem;" onclick="clearFileSelection()"></button>
                                </span>
                            </div>
                            <!-- Image preview container -->
                            <div id="imagePreviewContainer" class="mt-2 text-center" style="display: none;">
                                <img id="imagePreview" class="img-thumbnail" style="max-width: 100%; max-height: 200px;">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary">Simpan Periksa</button>
                        </div>
                    </form>

                    <!-- Camera Modal -->
                    <div class="modal fade" id="cameraModal" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Ambil Foto Telapak Kaki</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <div id="cameraView">
                                        <video id="video" width="100%" height="auto" autoplay playsinline></video>
                                        <div class="mt-2 text-muted">Arahkan kamera ke telapak kaki dan pastikan gambar jelas</div>
                                    </div>
                                    <div id="previewView" style="display: none;">
                                        <img id="photoPreview" style="max-width: 100%;" />
                                        <div class="mt-2 text-muted">Pratinjau foto</div>
                                    </div>
                                    <canvas id="canvas" width="320" height="240" style="display:none;"></canvas>
                                    <input type="hidden" id="photoData" name="photo_data">
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                    <button type="button" class="btn btn-warning" id="retakePhotoBtn" style="display: none;" onclick="retakePhoto()">
                                        <i class="fas fa-redo me-1"></i> Ambil Ulang
                                    </button>
                                    <button type="button" class="btn btn-primary" id="takePhotoBtn" onclick="takePhoto()">
                                        <i class="fas fa-camera me-1"></i> Ambil Foto
                                    </button>
                                    <button type="button" class="btn btn-success" id="usePhotoBtn" style="display: none;" onclick="usePhoto()">
                                        <i class="fas fa-check me-1"></i> Gunakan Foto Ini
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <script>
                        let stream = null;

                        // Start camera when modal is shown
                        document.getElementById('cameraModal').addEventListener('shown.bs.modal', function() {
                            const video = document.getElementById('video');

                            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                                navigator.mediaDevices.getUserMedia({
                                        video: {
                                            facingMode: 'environment', // Gunakan kamera belakang jika tersedia
                                            width: {
                                                ideal: 1280
                                            },
                                            height: {
                                                ideal: 720
                                            }
                                        }
                                    })
                                    .then(function(mediaStream) {
                                        stream = mediaStream;
                                        video.srcObject = mediaStream;
                                        video.play();
                                    })
                                    .catch(function(error) {
                                        console.error("Error accessing camera: ", error);
                                        alert("Tidak dapat mengakses kamera. Pastikan izin kamera telah diberikan.");
                                    });
                            } else {
                                alert("Browser tidak mendukung akses kamera.");
                            }
                        });

                        // Stop camera when modal is hidden
                        document.getElementById('cameraModal').addEventListener('hidden.bs.modal', function() {
                            stopCamera();
                            resetCameraUI();
                        });

                        function stopCamera() {
                            if (stream) {
                                stream.getTracks().forEach(track => {
                                    track.stop();
                                });
                                stream = null;
                            }
                        }

                        function resetCameraUI() {
                            const video = document.getElementById('video');
                            const photoPreview = document.getElementById('photoPreview');
                            const takePhotoBtn = document.getElementById('takePhotoBtn');
                            const usePhotoBtn = document.getElementById('usePhotoBtn');
                            const retakePhotoBtn = document.getElementById('retakePhotoBtn');
                            const cameraView = document.getElementById('cameraView');
                            const previewView = document.getElementById('previewView');

                            // Show camera view
                            cameraView.style.display = 'block';
                            previewView.style.display = 'none';
                            video.style.display = 'block';

                            // Reset buttons
                            takePhotoBtn.style.display = 'block';
                            usePhotoBtn.style.display = 'none';
                            retakePhotoBtn.style.display = 'none';
                        }

                        function retakePhoto() {
                            // Start camera again
                            const video = document.getElementById('video');
                            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                                navigator.mediaDevices.getUserMedia({
                                        video: {
                                            facingMode: 'environment',
                                            width: {
                                                ideal: 1280
                                            },
                                            height: {
                                                ideal: 720
                                            }
                                        }
                                    })
                                    .then(function(mediaStream) {
                                        // Stop previous stream if exists
                                        if (stream) {
                                            stopCamera();
                                        }

                                        stream = mediaStream;
                                        video.srcObject = mediaStream;
                                        video.play();

                                        // Reset UI
                                        resetCameraUI();
                                    })
                                    .catch(function(error) {
                                        console.error("Error accessing camera: ", error);
                                        alert("Tidak dapat mengakses kamera kembali.");
                                    });
                            }
                        }

                        function takePhoto() {
                            const video = document.getElementById('video');
                            const canvas = document.getElementById('canvas');
                            const photoPreview = document.getElementById('photoPreview');
                            const takePhotoBtn = document.getElementById('takePhotoBtn');
                            const usePhotoBtn = document.getElementById('usePhotoBtn');
                            const retakePhotoBtn = document.getElementById('retakePhotoBtn');
                            const cameraView = document.getElementById('cameraView');
                            const previewView = document.getElementById('previewView');

                            // Pause video to take still photo
                            video.pause();

                            canvas.width = video.videoWidth;
                            canvas.height = video.videoHeight;
                            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

                            const imageDataUrl = canvas.toDataURL('image/jpeg');
                            photoPreview.src = imageDataUrl;

                            // Switch to preview view
                            cameraView.style.display = 'none';
                            previewView.style.display = 'block';

                            // Update buttons
                            takePhotoBtn.style.display = 'none';
                            usePhotoBtn.style.display = 'block';
                            retakePhotoBtn.style.display = 'block';
                        }

                        function updateFileInfo() {
                            const fileInput = document.getElementById('citra_telapak_kaki');
                            const fileInfo = document.getElementById('fileInfo');
                            const fileNameDisplay = document.getElementById('fileNameDisplay');
                            const selectedFileName = document.getElementById('selectedFileName');
                            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
                            const imagePreview = document.getElementById('imagePreview');

                            if (fileInput.files && fileInput.files.length > 0) {
                                fileInfo.textContent = "File dipilih:";
                                selectedFileName.textContent = fileInput.files[0].name;
                                fileNameDisplay.style.display = 'block';

                                // Show image preview
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    imagePreview.src = e.target.result;
                                    imagePreviewContainer.style.display = 'block';
                                }
                                reader.readAsDataURL(fileInput.files[0]);
                            } else {
                                fileInfo.textContent = "Belum ada file yang dipilih";
                                fileNameDisplay.style.display = 'none';
                                imagePreviewContainer.style.display = 'none';
                            }
                        }

                        function clearFileSelection() {
                            const fileInput = document.getElementById('citra_telapak_kaki');
                            fileInput.value = '';
                            updateFileInfo();
                        }

                        function usePhoto() {
                            const canvas = document.getElementById('canvas');
                            const fileInput = document.getElementById('citra_telapak_kaki');
                            const imagePreviewContainer = document.getElementById('imagePreviewContainer');
                            const imagePreview = document.getElementById('imagePreview');

                            canvas.toBlob(function(blob) {
                                const file = new File([blob], 'telapak_kaki_' + new Date().toISOString().slice(0, 10) + '.jpg', {
                                    type: 'image/jpeg'
                                });

                                const dataTransfer = new DataTransfer();
                                dataTransfer.items.add(file);
                                fileInput.files = dataTransfer.files;

                                // Show the captured image preview
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    imagePreview.src = e.target.result;
                                    imagePreviewContainer.style.display = 'block';
                                }
                                reader.readAsDataURL(file);

                                updateFileInfo();

                                const modal = bootstrap.Modal.getInstance(document.getElementById('cameraModal'));
                                modal.hide();

                                // Stop camera after using photo
                                stopCamera();
                            }, 'image/jpeg', 0.9);
                        }
                    </script>
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

@endsection

<script>
    document.getElementById("form-periksa").addEventListener("submit", function(event) {
        event.preventDefault();

        let formData = new FormData(this);
        formData.append("_token", document.querySelector('meta[name="csrf-token"]').getAttribute("content"));

        fetch("{{ route('periksa.store') }}", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'Processing') {
                    alert('Gambar sedang diproses di background.');
                    window.location.href = "{{ route('periksa') }}";
                }
            })
            .catch(error => console.error("Gagal menyimpan:", error));
    });
</script>

<!-- Cek Session untuk Menampilkan Modal -->
@if(session()->pull('success'))
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
        reader.onload = function() {
            var preview = document.getElementById('preview');
            preview.src = reader.result;
            preview.classList.remove('d-none');
        };
        reader.readAsDataURL(event.target.files[0]);
    }
</script>