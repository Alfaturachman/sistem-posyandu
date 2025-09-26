@extends('frontend.layouts.app')
@section('content')

<style>
    canvas {
        max-width: 100%;
        height: auto;
    }
</style>

<section class="hero">
    <div class="container">
        <div class="hero-content fade-in-up text-center">
            <div class="d-flex flex-column justify-content-center align-items-center">
                <h1 data-aos="fade-up">
                    Selamat Datang Ananda
                    <br>
                    <span>{{ $anak->nama_anak }}</span>
                </h1>
                <p data-aos="fade-up" data-aos-delay="100">
                    Hasil data pemeriksaan buah hati bisa dilihat di bawah
                </p>
            </div>
        </div>
    </div>
</section>

<section id="detail-anak" class="detail-anak section">
    <div class="container section-title" data-aos="fade-up">
        <h2 class="text-white">Detail Informasi</h2>
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
                                    <td class="text-sm-start fw-bold">Umur</td>
                                    <td>:</td>
                                    <td>{{ \Carbon\Carbon::parse($anak->tanggal_lahir)->age }} tahun</td>
                                </tr>
                                <tr>
                                    <td class="text-sm-start fw-bold">Jenis Kelamin</td>
                                    <td>:</td>
                                    <td>{{ $anak->jenis_kelamin }}</td>
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
    <div class="container section-title" data-aos="fade-up">
        <h2>Riwayat Pemeriksaan</h2>
    </div>

    <input type="hidden" name="nama_anak" value="{{ $anak->nama_anak }}">
    <input type="hidden" name="jenis_kelamin" value="{{ $anak->jenis_kelamin }}">

    <div class="text-center mb-3">
        <a href="#" class="btn" id="downloadPdfBtn" style="background: var(--accent-color); color: white; text-decoration: none;">
            <i class="fas fa-download me-2"></i> Download Hasil Pemeriksaan
        </a>
    </div>

    <div class="container">
        <div class="row g-5">
            <div class="col" data-aos="fade-up" data-aos-delay="100">

                <div class="table-responsive">
                    <table class="table table-bordered table-striped align-middle">
                        <thead class="table-light">
                            <tr>
                                <th scope="col">Kategori Status Gizi</th>
                                <th scope="col">Ambang Batas (Z-Score)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Berat badan sangat kurang (Severely Underweight)</td>
                                <td>&lt; -3 SD</td>
                            </tr>
                            <tr>
                                <td>Berat badan kurang (Underweight)</td>
                                <td>-3 SD s.d. &lt; -2 SD</td>
                            </tr>
                            <tr>
                                <td>Berat badan normal (Normal)</td>
                                <td>-2 SD s.d. +1 SD</td>
                            </tr>
                            <tr>
                                <td>Risiko berat badan lebih (Risk of Overweight)</td>
                                <td>&gt; +1 SD</td>
                            </tr>
                        </tbody>
                    </table>
                </div>


                @if($pemeriksaans->isEmpty())
                <div class="alert alert-warning" role="alert">
                    Belum ada pemeriksaan.
                </div>
                @else

                <!-- Grafik Berat Badan -->
                <div class="mt-4">
                    <div class="card">
                        <div class="card-body">
                            <div style="position: relative; height:40vh; width:100%;">
                                <canvas id="beratBadanPerempuanChart" height="100"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grafik Tinggi Badan -->
                <div class="mt-4">
                    <div class="card">
                        <div class="card-body">
                            <div style="position: relative; height:40vh; width:100%;">
                                <canvas id="tinggiBadanPerempuanChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grafik Lingkar Kepala -->
                <div class="mt-4">
                    <div class="card">
                        <div class="card-body">
                            <div style="position: relative; height:40vh; width:100%;">
                                <canvas id="lingkarKepalaPerempuanChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Grafik Lingkar Lengan -->
                <div class="mt-4">
                    <div class="card">
                        <div class="card-body">
                            <div style="position: relative; height:40vh; width:100%;">
                                <canvas id="lingkarLenganPerempuanChart" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.getElementById("downloadPdfBtn").addEventListener("click", function(e) {
    e.preventDefault();

    // Ambil semua canvas chart
    const chartIds = [
        "beratBadanPerempuanChart",
        "tinggiBadanPerempuanChart",
        "lingkarKepalaPerempuanChart",
        "lingkarLenganPerempuanChart"
    ];

    const images = [];
    chartIds.forEach(id => {
        const canvas = document.getElementById(id);
        if (canvas && canvas.tagName.toLowerCase() === "canvas") {
            images.push(canvas.toDataURL("image/png", 1.0));
        }
    });

    // Ambil data dari hidden input
    const namaAnak = document.querySelector("[name='nama_anak']").value;
    const jenisKelamin = document.querySelector("[name='jenis_kelamin']").value;

    console.log("Nama Anak:", namaAnak);
    console.log("Jenis Kelamin:", jenisKelamin);

    const formData = new FormData();
    images.forEach(img => formData.append("images[]", img));
    formData.append("nama_anak", namaAnak);
    formData.append("jenis_kelamin", jenisKelamin);

    fetch("{{ route('download.pdf') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: formData
    })
    .then(res => {
        if (!res.ok) throw new Error("Gagal generate PDF (status: " + res.status + ")");
        return res.blob();
    })
    .then(blob => {
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement("a");
        a.href = url;
        a.download = "hasil_pemeriksaan.pdf";
        document.body.appendChild(a);
        a.click();
        a.remove();
        window.URL.revokeObjectURL(url);
    })
    .catch(err => {
        console.error("Download error:", err);
        alert("Terjadi error: " + err.message);
    });
});

</script>

<script>
    // Fungsi untuk membuat label dari angka 24 sampai 60
    function createMonthlyLabels() {
        const bulanLabels = [];
        for (let i = 24; i <= 60; i++) {
            bulanLabels.push(i);
        }
        return bulanLabels;
    }

    const beratAnak = @json(array_values($beratData));
    const tinggiAnak = @json(array_values($tinggiData));
    const lingkarKepalaAnak = @json(array_values($lingkarKepalaData));
    const lingkarLenganAnak = @json(array_values($lingkarLenganData));
    const umurAnak = @json($umurData);
    const anak = @json($anak);

    const bulanLabels = createMonthlyLabels();

    // Fungsi untuk membuat data standar untuk semua umur 24-60
    function createStandardData(standardDataRaw) {
        return standardDataRaw; // Langsung kembalikan data standar karena sudah sesuai dengan 24-60 bulan
    }

    // ===== GRAFIK BERAT BADAN =====
    // Data standar WHO (37 poin untuk 24-60 bulan) - Berat Badan (kg)
    const standarBeratBadan = {
        'Perempuan': {
            minus3SD_BB_raw: [8.1, 8.2, 8.4, 8.5, 8.6, 8.8, 8.9, 9.0, 9.1, 9.3, 9.4, 9.5, 9.6, 9.7, 9.8, 9.9, 10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7, 10.8, 10.9, 11.0, 11.1, 11.2, 11.3, 11.4, 11.5, 11.6, 11.7, 11.8, 11.9, 12.0, 12.1],
            minus2SD_BB_raw: [9.0, 9.2, 9.4, 9.5, 9.7, 9.8, 10.0, 10.1, 10.3, 10.4, 10.5, 10.7, 10.8, 10.9, 11.1, 11.2, 11.3, 11.5, 11.6, 11.7, 11.8, 12.0, 12.1, 12.2, 12.3, 12.4, 12.6, 12.7, 12.8, 12.9, 13.0, 13.2, 13.3, 13.4, 13.5, 13.6, 13.7],
            minus1SD_BB_raw: [10.2, 10.3, 10.5, 10.7, 10.9, 11.1, 11.2, 11.4, 11.6, 11.7, 11.9, 12.0, 12.2, 12.4, 12.5, 12.7, 12.8, 13.0, 13.1, 13.3, 13.4, 13.6, 13.7, 13.9, 14.0, 14.2, 14.3, 14.5, 14.6, 14.8, 14.9, 15.1, 15.2, 15.3, 15.5, 15.6, 15.8],
            median_BB_raw: [11.5, 11.7, 11.9, 12.1, 12.3, 12.5, 12.7, 12.9, 13.1, 13.3, 13.5, 13.7, 13.9, 14.0, 14.2, 14.4, 14.6, 14.8, 15.0, 15.2, 15.3, 15.5, 15.7, 15.9, 16.1, 16.3, 16.4, 16.6, 16.8, 17.0, 17.2, 17.3, 17.5, 17.7, 17.9, 18.0, 18.2],
            plus1SD_BB_raw: [13.0, 13.3, 13.5, 13.7, 14.0, 14.2, 14.4, 14.7, 14.9, 15.1, 15.4, 15.6, 15.8, 16.0, 16.3, 16.5, 16.7, 16.9, 17.2, 17.4, 17.6, 17.8, 18.1, 18.3, 18.5, 18.8, 19.0, 19.2, 19.4, 19.7, 19.9, 20.1, 20.3, 20.6, 20.8, 21.0, 21.2],
            plus2SD_BB_raw: [14.8, 15.1, 15.4, 15.7, 16.0, 16.2, 16.5, 16.8, 17.1, 17.3, 17.6, 17.9, 18.1, 18.4, 18.7, 19.0, 19.2, 19.5, 19.8, 20.1, 20.4, 20.7, 20.9, 21.2, 21.5, 21.8, 22.1, 22.4, 22.6, 22.9, 23.2, 23.5, 23.8, 24.1, 24.4, 24.6, 24.9],
            plus3SD_BB_raw: [17.0, 17.3, 17.7, 18.0, 18.3, 18.7, 19.0, 19.3, 19.6, 20.0, 20.3, 20.6, 20.9, 21.3, 21.6, 22.0, 22.3, 22.7, 23.0, 23.4, 23.7, 24.1, 24.5, 24.8, 25.2, 25.5, 25.9, 26.3, 26.6, 27.0, 27.4, 27.7, 28.1, 28.5, 28.8, 29.2, 29.5],
        },
        'Laki-laki': {
            minus3SD_BB_raw: [8.6, 8.8, 8.9, 9.0, 9.1, 9.2, 9.4, 9.5, 9.6, 9.7, 9.8, 9.9, 10.0, 10.1, 10.2, 10.3, 10.4, 10.5, 10.6, 10.7, 10.8, 10.9, 11.0, 11.1, 11.2, 11.3, 11.4, 11.5, 11.6, 11.7, 11.8, 11.9, 12.0, 12.1, 12.2, 12.3, 12.4],
            minus2SD_BB_raw: [9.7, 9.8, 10.0, 10.1, 10.2, 10.4, 10.5, 10.7, 10.8, 10.9, 11.0, 11.2, 11.3, 11.4, 11.5, 11.6, 11.8, 11.9, 12.0, 12.1, 12.2, 12.4, 12.5, 12.6, 12.7, 12.8, 12.9, 13.1, 13.2, 13.3, 13.4, 13.5, 13.6, 13.7, 13.8, 14.0, 14.1],
            minus1SD_BB_raw: [10.8, 11.0, 11.2, 11.3, 11.5, 11.7, 11.8, 12.0, 12.1, 12.3, 12.4, 12.6, 12.7, 12.9, 13.0, 13.1, 13.3, 13.4, 13.6, 13.7, 13.8, 14.0, 14.1, 14.3, 14.4, 14.5, 14.7, 14.8, 15.0, 15.1, 15.2, 15.4, 15.5, 15.6, 15.8, 15.9, 16.0],
            median_BB_raw: [12.2, 12.4, 12.5, 12.7, 12.9, 13.1, 13.3, 13.5, 13.7, 13.8, 14.0, 14.2, 14.3, 14.5, 14.7, 14.8, 15.0, 15.2, 15.3, 15.5, 15.7, 15.8, 16.0, 16.2, 16.3, 16.5, 16.7, 16.8, 17.0, 17.2, 17.3, 17.5, 17.7, 17.8, 18.0, 18.2, 18.3],
            plus1SD_BB_raw: [13.6, 13.9, 14.1, 14.3, 14.5, 14.8, 15.0, 15.2, 15.4, 15.6, 15.8, 16.0, 16.2, 16.4, 16.6, 16.8, 17.0, 17.2, 17.4, 17.6, 17.8, 18.0, 18.2, 18.4, 18.6, 18.8, 19.0, 19.2, 19.4, 19.6, 19.8, 20.0, 20.2, 20.4, 20.6, 20.8, 21.0],
            plus2SD_BB_raw: [15.3, 15.5, 15.8, 16.1, 16.3, 16.6, 16.9, 17.1, 17.4, 17.6, 17.8, 18.1, 18.3, 18.6, 18.8, 19.0, 19.3, 19.5, 19.7, 20.0, 20.2, 20.5, 20.7, 20.9, 21.2, 21.4, 21.7, 21.9, 22.2, 22.4, 22.7, 22.9, 23.2, 23.4, 23.7, 23.9, 24.2],
            plus3SD_BB_raw: [17.1, 17.5, 17.8, 18.1, 18.4, 18.7, 19.0, 19.3, 19.6, 19.9, 20.2, 20.4, 20.7, 21.0, 21.3, 21.6, 21.9, 22.1, 22.4, 22.7, 23.0, 23.3, 23.6, 23.9, 24.2, 24.5, 24.8, 25.1, 25.4, 25.7, 26.0, 26.3, 26.6, 26.9, 27.2, 27.6, 27.9],
        }
    };

    const standarBB = standarBeratBadan[anak.jenis_kelamin];

    // Buat data standar untuk semua umur 24-60
    const minus3SD_BB = createStandardData(standarBB.minus3SD_BB_raw);
    const minus2SD_BB = createStandardData(standarBB.minus2SD_BB_raw);
    const minus1SD_BB = createStandardData(standarBB.minus1SD_BB_raw);
    const median_BB = createStandardData(standarBB.median_BB_raw);
    const plus1SD_BB = createStandardData(standarBB.plus1SD_BB_raw);
    const plus2SD_BB = createStandardData(standarBB.plus2SD_BB_raw);
    const plus3SD_BB = createStandardData(standarBB.plus3SD_BB_raw);

    // Grafik Berat Anak
    new Chart(document.getElementById('beratBadanPerempuanChart'), {
        type: 'line',
        data: {
            labels: umurAnak, // Gunakan semua umur 24-60 sebagai label
            datasets: [{
                    label: 'Berat Anak',
                    data: beratAnak,
                    borderColor: 'purple',
                    backgroundColor: 'purple',
                    type: 'scatter',
                    showLine: false,
                    pointRadius: 6,
                    pointHoverRadius: 8
                },
                {
                    label: '-3 SD',
                    data: minus3SD_BB,
                    borderColor: 'red',
                    borderWidth: 1.5,
                    fill: '+1',
                    backgroundColor: '#FFD1D1',
                    pointRadius: 0
                },
                {
                    label: '-2 SD',
                    data: minus2SD_BB,
                    borderColor: 'orange',
                    borderWidth: 1.5,
                    fill: '+1',
                    backgroundColor: '#B6FFB6',
                    pointRadius: 0
                },
                {
                    label: '-1 SD',
                    data: minus1SD_BB,
                    borderColor: 'yellowgreen',
                    borderWidth: 1.5,
                    fill: '+1',
                    backgroundColor: '#E2FFB2',
                    pointRadius: 0
                },
                {
                    label: 'Normal',
                    data: median_BB,
                    borderColor: 'green',
                    borderWidth: 2,
                    fill: '+1',
                    backgroundColor: '#FFF7B2',
                    pointRadius: 0
                },
                {
                    label: '+1 SD',
                    data: plus1SD_BB,
                    borderColor: 'yellowgreen',
                    borderWidth: 1.5,
                    fill: '+1',
                    backgroundColor: '#E2FFB2',
                    pointRadius: 0
                },
                {
                    label: '+2 SD',
                    data: plus2SD_BB,
                    borderColor: 'orange',
                    borderWidth: 1.5,
                    fill: '+1',
                    pointRadius: 0
                },
                {
                    label: '+3 SD',
                    data: plus3SD_BB,
                    borderColor: 'red',
                    borderWidth: 1.5,
                    fill: false,
                    pointRadius: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Grafik Berat Badan Pemeriksaan Balita',
                    font: {
                        size: window.innerWidth < 576 ? 14 : 18
                    }
                },
                legend: {
                    labels: {
                        font: {
                            size: window.innerWidth < 576 ? 10 : 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            // Hanya tampilkan tooltip jika ada data
                            if (context.raw !== null) {
                                return context.dataset.label + ': ' + context.raw;
                            }
                            return null;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Umur (bulan)'
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 30,
                        font: {
                            size: window.innerWidth < 576 ? 9 : 12
                        }
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Berat Badan (kg)'
                    },
                    ticks: {
                        font: {
                            size: window.innerWidth < 576 ? 9 : 12
                        }
                    }
                }
            },
            layout: {
                padding: {
                    right: 20
                }
            }
        },
        plugins: [{
            id: 'endLabelPlugin',
            afterDatasetsDraw(chart) {
                const ctx = chart.ctx;
                ctx.save();
                ctx.font = 'bold 12px Arial';
                ctx.fillStyle = 'black';
                ctx.textAlign = 'left';
                const datasets = chart.data.datasets;
                const labelsText = ['-3', '-2', '-1', '0', '1', '2', '3'];

                datasets.forEach((ds, i) => {
                    if (i === 0) return; // Skip dataset pertama (data anak)
                    const meta = chart.getDatasetMeta(i);

                    // Pastikan meta.data ada dan memiliki elemen
                    if (meta.data && meta.data.length > 0) {
                        const lastPoint = meta.data[meta.data.length - 1];
                        if (lastPoint) {
                            const y = lastPoint.y;
                            const x = lastPoint.x + 5;
                            ctx.fillText(labelsText[i - 1], x, y + 4);
                        }
                    }
                });
                ctx.restore();
            }
        }]
    });

    // ===== GRAFIK TINGGI BADAN =====
    // Data standar WHO (37 poin untuk 24-60 bulan) - Tinggi Badan (cm)
    const standarTinggiBadan = {
        'Perempuan': {
            minus3SD_TB_raw: [76.0, 76.8, 77.5, 78.1, 78.8, 79.5, 80.1, 80.7, 81.3, 81.9, 82.5, 83.1, 83.6, 84.2, 84.7, 85.3, 85.8, 86.3, 86.8, 87.4, 87.9, 88.4, 88.9, 89.3, 89.8, 90.3, 90.7, 91.2, 91.7, 92.1, 92.6, 93.0, 93.4, 93.9, 94.3, 94.7, 95.2],
            minus2SD_TB_raw: [79.3, 80.0, 80.8, 81.5, 82.2, 82.9, 83.6, 84.3, 84.9, 85.6, 86.2, 86.8, 87.4, 88.0, 88.6, 89.2, 89.8, 90.4, 90.9, 91.5, 92.0, 92.5, 93.1, 93.6, 94.1, 94.6, 95.1, 95.6, 96.1, 96.6, 97.1, 97.6, 98.1, 98.5, 99.0, 99.5, 99.9],
            minus1SD_TB_raw: [82.5, 83.3, 84.1, 84.9, 85.7, 86.4, 87.1, 87.9, 88.6, 89.3, 89.9, 90.6, 91.2, 91.9, 92.5, 93.1, 93.8, 94.4, 95.0, 95.6, 96.2, 96.7, 97.3, 97.9, 98.4, 99.0, 99.5, 100.1, 100.6, 101.1, 101.6, 102.2, 102.7, 103.2, 103.7, 104.2, 104.7],
            median_TB_raw: [85.7, 86.6, 87.4, 88.3, 89.1, 89.9, 90.7, 91.4, 92.2, 92.9, 93.6, 94.4, 95.1, 95.7, 96.4, 97.1, 97.7, 98.4, 99.0, 99.7, 100.3, 100.9, 101.5, 102.1, 102.7, 103.3, 103.9, 104.5, 105.0, 105.6, 106.2, 106.7, 107.3, 107.8, 108.4, 108.9, 109.4],
            plus1SD_TB_raw: [88.9, 89.9, 90.8, 91.7, 92.5, 93.4, 94.2, 95.0, 95.8, 96.6, 97.4, 98.1, 98.9, 99.6, 100.3, 101.0, 101.7, 102.4, 103.1, 103.8, 104.5, 105.1, 105.8, 106.4, 107.0, 107.7, 108.3, 108.9, 109.5, 110.1, 110.7, 111.3, 111.9, 112.5, 113.0, 113.6, 114.2],
            plus2SD_TB_raw: [92.2, 93.1, 94.1, 95.0, 96.0, 96.9, 97.7, 98.6, 99.4, 100.3, 101.1, 101.9, 102.7, 103.4, 104.2, 105.0, 105.7, 106.4, 107.2, 107.9, 108.6, 109.3, 110.0, 110.7, 111.3, 112.0, 112.7, 113.3, 114.0, 114.6, 115.2, 115.9, 116.5, 117.1, 117.7, 118.3, 118.9],
            plus3SD_TB_raw: [95.4, 96.4, 97.4, 98.4, 99.4, 100.3, 101.3, 102.2, 103.1, 103.9, 104.8, 105.6, 106.5, 107.3, 108.1, 108.9, 109.7, 110.5, 111.2, 112.0, 112.7, 113.5, 114.2, 114.9, 115.7, 116.4, 117.1, 117.7, 118.4, 119.1, 119.8, 120.4, 121.1, 121.8, 122.4, 123.1, 123.7],
        },
        'Laki-laki': {
            minus3SD_TB_raw: [78.0, 78.6, 79.3, 79.9, 80.5, 81.1, 81.7, 82.3, 82.8, 83.4, 83.9, 84.4, 85.0, 85.5, 86.0, 86.5, 87.0, 87.5, 88.0, 88.4, 88.9, 89.4, 89.8, 90.3, 90.7, 91.2, 91.6, 92.1, 92.5, 93.0, 93.4, 93.9, 94.3, 94.7, 95.2, 95.6, 96.1],
            minus2SD_TB_raw: [81.0, 81.7, 82.5, 83.1, 83.8, 84.5, 85.1, 85.7, 86.4, 86.9, 87.5, 88.1, 88.7, 89.2, 89.8, 90.3, 90.9, 91.4, 91.9, 92.4, 93.0, 93.5, 94.0, 94.4, 94.9, 95.4, 95.9, 96.4, 96.9, 97.4, 97.8, 98.3, 98.8, 99.3, 99.7, 100.2, 100.7],
            minus1SD_TB_raw: [84.1, 84.9, 85.6, 86.4, 87.1, 87.8, 88.5, 89.2, 89.9, 90.5, 91.1, 91.8, 92.4, 93.0, 93.6, 94.2, 94.7, 95.3, 95.9, 96.4, 97.0, 97.5, 98.1, 98.6, 99.1, 99.7, 100.2, 100.7, 101.2, 101.7, 102.3, 102.8, 103.3, 103.8, 104.3, 104.8, 105.3],
            median_TB_raw: [87.1, 88.0, 88.8, 89.6, 90.4, 91.2, 91.9, 92.7, 93.4, 94.1, 94.8, 95.4, 96.1, 96.7, 97.4, 98.0, 98.6, 99.2, 99.9, 100.4, 101.0, 101.6, 102.2, 102.8, 103.3, 103.9, 104.4, 105.0, 105.6, 106.1, 106.7, 107.2, 107.8, 108.3, 108.9, 109.4, 110.0],
            plus1SD_TB_raw: [90.2, 91.1, 92.0, 92.9, 93.7, 94.5, 95.3, 96.1, 96.9, 97.6, 98.4, 99.1, 99.8, 100.5, 101.2, 101.8, 102.5, 103.2, 103.8, 104.5, 105.1, 105.7, 106.3, 106.9, 107.5, 108.1, 108.7, 109.3, 109.9, 110.5, 111.1, 111.7, 112.3, 112.8, 113.4, 114.0, 114.6],
            plus2SD_TB_raw: [93.2, 94.2, 95.2, 96.1, 97.0, 97.9, 98.7, 99.6, 100.4, 101.2, 102.0, 102.7, 103.5, 104.2, 105.0, 105.7, 106.4, 107.1, 107.8, 108.5, 109.1, 109.8, 110.4, 111.1, 111.7, 112.4, 113.0, 113.6, 114.2, 114.9, 115.5, 116.1, 116.7, 117.4, 118.0, 118.6, 119.2],
            plus3SD_TB_raw: [96.3, 97.3, 98.3, 99.3, 100.3, 101.2, 102.1, 103.0, 103.9, 104.8, 105.6, 106.4, 107.2, 108.0, 108.8, 109.5, 110.3, 111.0, 111.7, 112.5, 113.2, 113.9, 114.6, 115.2, 115.9, 116.6, 117.3, 117.9, 118.6, 119.2, 119.9, 120.6, 121.2, 121.9, 122.6, 123.2, 123.9],
        }
    };

    const standarTB = standarTinggiBadan[anak.jenis_kelamin];

    // Buat data standar untuk semua umur 24-60
    const minus3SD_TB = createStandardData(standarTB.minus3SD_TB_raw, 48);
    const minus2SD_TB = createStandardData(standarTB.minus2SD_TB_raw, 48);
    const minus1SD_TB = createStandardData(standarTB.minus1SD_TB_raw, 48);
    const median_TB = createStandardData(standarTB.median_TB_raw, 48);
    const plus1SD_TB = createStandardData(standarTB.plus1SD_TB_raw, 48);
    const plus2SD_TB = createStandardData(standarTB.plus2SD_TB_raw, 48);
    const plus3SD_TB = createStandardData(standarTB.plus3SD_TB_raw, 48);

    // Grafik Tinggi Anak
    new Chart(document.getElementById('tinggiBadanPerempuanChart'), {
        type: 'line',
        data: {
            labels: umurAnak, // Gunakan semua umur 24-60 sebagai label
            datasets: [{
                    label: 'Tinggi Anak',
                    data: tinggiAnak,
                    borderColor: 'purple',
                    backgroundColor: 'purple',
                    type: 'scatter',
                    showLine: false,
                    pointRadius: 6,
                    pointHoverRadius: 8
                },
                {
                    label: '-3 SD',
                    data: minus3SD_TB,
                    borderColor: 'red',
                    borderWidth: 1.5,
                    fill: '+1',
                    backgroundColor: '#FFD1D1',
                    pointRadius: 0
                },
                {
                    label: '-2 SD',
                    data: minus2SD_TB,
                    borderColor: 'orange',
                    borderWidth: 1.5,
                    fill: '+1',
                    backgroundColor: '#B6FFB6',
                    pointRadius: 0
                },
                {
                    label: '-1 SD',
                    data: minus1SD_TB,
                    borderColor: 'yellowgreen',
                    borderWidth: 1.5,
                    fill: '+1',
                    backgroundColor: '#E2FFB2',
                    pointRadius: 0
                },
                {
                    label: 'Normal',
                    data: median_TB,
                    borderColor: 'green',
                    borderWidth: 2,
                    fill: '+1',
                    backgroundColor: '#FFF7B2',
                    pointRadius: 0
                },
                {
                    label: '+1 SD',
                    data: plus1SD_TB,
                    borderColor: 'yellowgreen',
                    borderWidth: 1.5,
                    fill: '+1',
                    backgroundColor: '#E2FFB2',
                    pointRadius: 0
                },
                {
                    label: '+2 SD',
                    data: plus2SD_TB,
                    borderColor: 'orange',
                    borderWidth: 1.5,
                    fill: '+1',
                    pointRadius: 0
                },
                {
                    label: '+3 SD',
                    data: plus3SD_TB,
                    borderColor: 'red',
                    borderWidth: 1.5,
                    fill: false,
                    pointRadius: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Grafik Tinggi Badan Pemeriksaan Balita',
                    font: {
                        size: window.innerWidth < 576 ? 14 : 18
                    }
                },
                legend: {
                    labels: {
                        font: {
                            size: window.innerWidth < 576 ? 10 : 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            // Hanya tampilkan tooltip jika ada data
                            if (context.raw !== null) {
                                return context.dataset.label + ': ' + context.raw;
                            }
                            return null;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Umur (bulan)'
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 30,
                        font: {
                            size: window.innerWidth < 576 ? 9 : 12
                        }
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Tinggi Badan (cm)'
                    },
                    ticks: {
                        font: {
                            size: window.innerWidth < 576 ? 9 : 12
                        }
                    }
                }
            },
            layout: {
                padding: {
                    right: 20
                }
            }
        },
        plugins: [{
            id: 'endLabelPlugin',
            afterDatasetsDraw(chart) {
                const ctx = chart.ctx;
                ctx.save();
                ctx.font = 'bold 12px Arial';
                ctx.fillStyle = 'black';
                ctx.textAlign = 'left';
                const datasets = chart.data.datasets;
                const labelsText = ['-3', '-2', '-1', '0', '1', '2', '3'];

                datasets.forEach((ds, i) => {
                    if (i === 0) return; // Skip dataset pertama (data anak)
                    const meta = chart.getDatasetMeta(i);

                    // Pastikan meta.data ada dan memiliki elemen
                    if (meta.data && meta.data.length > 0) {
                        const lastPoint = meta.data[meta.data.length - 1];
                        if (lastPoint) {
                            const y = lastPoint.y;
                            const x = lastPoint.x + 5;
                            ctx.fillText(labelsText[i - 1], x, y + 4);
                        }
                    }
                });
                ctx.restore();
            }
        }]
    });

    // ===== GRAFIK LINGKAR KEPALA =====
    // Data standar WHO (37 poin untuk 24-60 bulan) - Lingkar Kepala (cm)
    const standarLingkarKepala = {
        'Perempuan': {
            minus3SD_LK_raw: [43.7, 43.8, 43.9, 44.0, 44.2, 44.3, 44.4, 44.5, 44.6, 44.7, 44.9, 45.0, 45.1, 45.2, 45.4, 45.5, 45.6, 45.8, 45.9, 46.0, 46.1, 46.3, 46.4, 46.5, 46.6, 46.8, 46.9, 47.0, 47.1, 47.3, 47.4, 47.5, 47.6, 47.8, 47.9, 48.0, 48.1],
            minus2SD_LK_raw: [45.2, 45.3, 45.4, 45.5, 45.7, 45.8, 45.9, 46.0, 46.1, 46.2, 46.4, 46.5, 46.6, 46.7, 46.9, 47.0, 47.1, 47.3, 47.4, 47.5, 47.6, 47.8, 47.9, 48.0, 48.1, 48.3, 48.4, 48.5, 48.6, 48.8, 48.9, 49.0, 49.1, 49.3, 49.4, 49.5, 49.6],
            minus1SD_LK_raw: [46.5, 46.6, 46.7, 46.9, 47.0, 47.1, 47.2, 47.3, 47.5, 47.6, 47.7, 47.8, 47.9, 48.0, 48.2, 48.3, 48.4, 48.5, 48.6, 48.8, 48.9, 49.0, 49.1, 49.2, 49.3, 49.5, 49.6, 49.7, 49.8, 50.0, 50.1, 50.2, 50.3, 50.5, 50.6, 50.7, 50.8],
            median_LK_raw: [47.2, 47.4, 47.6, 47.8, 48.0, 48.1, 48.3, 48.5, 48.7, 48.9, 49.1, 49.3, 49.4, 49.6, 49.8, 50.0, 50.1, 50.3, 50.5, 50.7, 50.8, 51.0, 51.2, 51.4, 51.5, 51.7, 51.9, 52.1, 52.2, 52.4, 52.6, 52.8, 52.9, 53.1, 53.3, 53.5, 53.6],
            plus1SD_LK_raw: [48.0, 48.2, 48.4, 48.6, 48.8, 48.9, 49.1, 49.3, 49.5, 49.7, 49.9, 50.1, 50.2, 50.4, 50.6, 50.8, 50.9, 51.1, 51.3, 51.5, 51.6, 51.8, 52.0, 52.2, 52.3, 52.5, 52.7, 52.9, 53.0, 53.2, 53.4, 53.6, 53.7, 53.9, 54.1, 54.3, 54.4],
            plus2SD_LK_raw: [49.5, 49.7, 49.9, 50.1, 50.3, 50.5, 50.7, 50.9, 51.1, 51.3, 51.5, 51.7, 51.9, 52.1, 52.3, 52.5, 52.7, 52.9, 53.1, 53.3, 53.5, 53.7, 53.9, 54.1, 54.3, 54.5, 54.7, 54.9, 55.1, 55.3, 55.5, 55.7, 55.9, 56.1, 56.3, 56.5, 56.7],
            plus3SD_LK_raw: [51.0, 51.2, 51.4, 51.6, 51.8, 52.0, 52.2, 52.4, 52.6, 52.8, 53.0, 53.2, 53.4, 53.6, 53.8, 54.0, 54.2, 54.4, 54.6, 54.8, 55.0, 55.2, 55.4, 55.6, 55.8, 56.0, 56.2, 56.4, 56.6, 56.8, 57.0, 57.2, 57.4, 57.6, 57.8, 58.0, 58.2],
        },
        'Laki-laki': {
            minus3SD_LK_raw: [44.2, 44.3, 44.4, 44.5, 44.6, 44.7, 44.8, 44.8, 44.9, 45.0, 45.1, 45.1, 45.2, 45.3, 45.3, 45.4, 45.4, 45.5, 45.5, 45.6, 45.6, 45.7, 45.7, 45.8, 45.8, 45.9, 45.9, 45.9, 46.0, 46.0, 46.1, 46.1, 46.1, 46.2, 46.2, 46.2, 46.3],
            minus2SD_LK_raw: [45.5, 45.6, 45.8, 45.9, 46.0, 46.1, 46.1, 46.2, 46.3, 46.4, 46.5, 46.6, 46.6, 46.7, 46.8, 46.8, 46.9, 46.9, 47.0, 47.0, 47.1, 47.1, 47.2, 47.2, 47.3, 47.3, 47.4, 47.4, 47.5, 47.5, 47.5, 47.6, 47.6, 47.6, 47.7, 47.7, 47.7],
            minus1SD_LK_raw: [46.9, 47.0, 47.1, 47.2, 47.3, 47.4, 47.5, 47.6, 47.7, 47.8, 47.9, 48.0, 48.0, 48.1, 48.2, 48.2, 48.3, 48.4, 48.4, 48.5, 48.5, 48.6, 48.7, 48.7, 48.7, 48.8, 48.8, 48.9, 49.0, 49.1, 49.1, 49.1, 49.1, 49.2, 49.2, 49.2, 49.2],
            median_LK_raw: [48.3, 48.4, 48.5, 48.6, 48.7, 48.8, 48.9, 49.0, 49.1, 49.2, 49.3, 49.4, 49.5, 49.5, 49.6, 49.7, 49.7, 49.8, 49.9, 49.9, 50.0, 50.1, 50.1, 50.2, 50.2, 50.3, 50.3, 50.4, 50.4, 50.4, 50.5, 50.5, 50.6, 50.6, 50.7, 50.7, 50.7],
            plus1SD_LK_raw: [49.6, 49.7, 49.9, 50.0, 50.1, 50.2, 50.3, 50.4, 50.5, 50.6, 50.7, 50.8, 50.9, 51.0, 51.0, 51.1, 51.1, 51.2, 51.3, 51.3, 51.4, 51.5, 51.6, 51.6, 51.7, 51.7, 51.8, 51.8, 51.9, 51.9, 52.0, 52.0, 52.0, 52.1, 52.1, 52.2, 52.2],
            plus2SD_LK_raw: [51.0, 51.1, 51.2, 51.4, 51.5, 51.6, 51.7, 51.8, 51.9, 52.0, 52.1, 52.2, 52.3, 52.4, 52.5, 52.5, 52.6, 52.7, 52.8, 52.8, 52.9, 53.0, 53.0, 53.1, 53.1, 53.2, 53.2, 53.3, 53.3, 53.4, 53.4, 53.5, 53.5, 53.6, 53.6, 53.7, 53.7],
            plus3SD_LK_raw: [52.3, 52.5, 52.6, 52.7, 52.9, 53.0, 53.1, 53.2, 53.3, 53.4, 53.5, 53.6, 53.7, 53.8, 53.9, 54.0, 54.0, 54.1, 54.2, 54.3, 54.3, 54.4, 54.5, 54.5, 54.6, 54.6, 54.7, 54.7, 54.8, 54.9, 54.9, 55.0, 55.0, 55.1, 55.1, 55.2, 55.2],
        }
    };

    const standarLK = standarLingkarKepala[anak.jenis_kelamin];

    // Buat data standar untuk semua umur 24-60
    const minus3SD_LK = createStandardData(standarLK.minus3SD_LK_raw, 48);
    const minus2SD_LK = createStandardData(standarLK.minus2SD_LK_raw, 48);
    const minus1SD_LK = createStandardData(standarLK.minus1SD_LK_raw, 48);
    const median_LK = createStandardData(standarLK.median_LK_raw, 48);
    const plus1SD_LK = createStandardData(standarLK.plus1SD_LK_raw, 48);
    const plus2SD_LK = createStandardData(standarLK.plus2SD_LK_raw, 48);
    const plus3SD_LK = createStandardData(standarLK.plus3SD_LK_raw, 48);

    // Grafik Lingkar Kepala Anak
    new Chart(document.getElementById('lingkarKepalaPerempuanChart'), {
        type: 'line',
        data: {
            labels: umurAnak, // Gunakan semua umur 24-60 sebagai label
            datasets: [{
                    label: 'Linkar Kepala Anak',
                    data: lingkarKepalaAnak,
                    borderColor: 'purple',
                    backgroundColor: 'purple',
                    type: 'scatter',
                    showLine: false,
                    pointRadius: 6,
                    pointHoverRadius: 8
                },
                {
                    label: '-3 SD',
                    data: minus3SD_LK,
                    borderColor: 'red',
                    borderWidth: 1.5,
                    fill: '+1',
                    backgroundColor: '#FFD1D1',
                    pointRadius: 0
                },
                {
                    label: '-2 SD',
                    data: minus2SD_LK,
                    borderColor: 'orange',
                    borderWidth: 1.5,
                    fill: '+1',
                    backgroundColor: '#B6FFB6',
                    pointRadius: 0
                },
                {
                    label: '-1 SD',
                    data: minus1SD_LK,
                    borderColor: 'yellowgreen',
                    borderWidth: 1.5,
                    fill: '+1',
                    backgroundColor: '#E2FFB2',
                    pointRadius: 0
                },
                {
                    label: 'Normal',
                    data: median_LK,
                    borderColor: 'green',
                    borderWidth: 2,
                    fill: '+1',
                    backgroundColor: '#FFF7B2',
                    pointRadius: 0
                },
                {
                    label: '+1 SD',
                    data: plus1SD_LK,
                    borderColor: 'yellowgreen',
                    borderWidth: 1.5,
                    fill: '+1',
                    backgroundColor: '#E2FFB2',
                    pointRadius: 0
                },
                {
                    label: '+2 SD',
                    data: plus2SD_LK,
                    borderColor: 'orange',
                    borderWidth: 1.5,
                    fill: '+1',
                    pointRadius: 0
                },
                {
                    label: '+3 SD',
                    data: plus3SD_LK,
                    borderColor: 'red',
                    borderWidth: 1.5,
                    fill: false,
                    pointRadius: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Grafik Lingkar Kepala Pemeriksaan Balita',
                    font: {
                        size: window.innerWidth < 576 ? 14 : 18
                    }
                },
                legend: {
                    labels: {
                        font: {
                            size: window.innerWidth < 576 ? 10 : 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            // Hanya tampilkan tooltip jika ada data
                            if (context.raw !== null) {
                                return context.dataset.label + ': ' + context.raw;
                            }
                            return null;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Umur (bulan)'
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 30,
                        font: {
                            size: window.innerWidth < 576 ? 9 : 12
                        }
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Lingkar Kepala (cm)'
                    },
                    ticks: {
                        font: {
                            size: window.innerWidth < 576 ? 9 : 12
                        }
                    }
                }
            },
            layout: {
                padding: {
                    right: 20
                }
            }
        },
        plugins: [{
            id: 'endLabelPlugin',
            afterDatasetsDraw(chart) {
                const ctx = chart.ctx;
                ctx.save();
                ctx.font = 'bold 12px Arial';
                ctx.fillStyle = 'black';
                ctx.textAlign = 'left';
                const datasets = chart.data.datasets;
                const labelsText = ['-3', '-2', '-1', '0', '1', '2', '3'];

                datasets.forEach((ds, i) => {
                    if (i === 0) return; // Skip dataset pertama (data anak)
                    const meta = chart.getDatasetMeta(i);

                    // Pastikan meta.data ada dan memiliki elemen
                    if (meta.data && meta.data.length > 0) {
                        const lastPoint = meta.data[meta.data.length - 1];
                        if (lastPoint) {
                            const y = lastPoint.y;
                            const x = lastPoint.x + 5;
                            ctx.fillText(labelsText[i - 1], x, y + 4);
                        }
                    }
                });
                ctx.restore();
            }
        }]
    });

    // ===== GRAFIK LINGKAR LENGAN =====
    // Data standar WHO (37 poin untuk 24-60 bulan) - Lingkar Lengan (cm)
    const standarLingkarLengan = {
        'Perempuan': {
            minus3SD_LILA_raw: [12.2, 12.3, 12.4, 12.5, 12.6, 12.7, 12.8, 12.9, 13.0, 13.1, 13.2, 13.3, 13.4, 13.5, 13.6, 13.7, 13.8, 13.9, 14.0, 14.1, 14.2, 14.3, 14.4, 14.5, 14.6, 14.7, 14.8, 14.9, 15.0, 15.1, 15.2, 15.3, 15.4, 15.5, 15.6, 15.7, 15.8],
            minus2SD_LILA_raw: [12.8, 12.9, 13.0, 13.1, 13.2, 13.3, 13.4, 13.5, 13.6, 13.7, 13.8, 13.9, 14.0, 14.1, 14.2, 14.3, 14.4, 14.5, 14.6, 14.7, 14.8, 14.9, 15.0, 15.1, 15.2, 15.3, 15.4, 15.5, 15.6, 15.7, 15.8, 15.9, 16.0, 16.1, 16.2, 16.3, 16.4],
            minus1SD_LILA_raw: [13.5, 13.6, 13.7, 13.8, 13.9, 14.0, 14.1, 14.2, 14.3, 14.4, 14.5, 14.6, 14.7, 14.8, 14.9, 15.0, 15.1, 15.2, 15.3, 15.4, 15.5, 15.6, 15.7, 15.8, 15.9, 16.0, 16.1, 16.2, 16.3, 16.4, 16.5, 16.6, 16.7, 16.8, 16.9, 17.0, 17.1],
            median_LILA_raw: [14.2, 14.3, 14.4, 14.5, 14.6, 14.7, 14.8, 14.9, 15.0, 15.1, 15.2, 15.3, 15.4, 15.5, 15.6, 15.7, 15.8, 15.9, 16.0, 16.1, 16.2, 16.3, 16.4, 16.5, 16.6, 16.7, 16.8, 16.9, 17.0, 17.1, 17.2, 17.3, 17.4, 17.5, 17.6, 17.7, 17.8],
            plus1SD_LILA_raw: [14.9, 15.0, 15.1, 15.2, 15.3, 15.4, 15.5, 15.6, 15.7, 15.8, 15.9, 16.0, 16.1, 16.2, 16.3, 16.4, 16.5, 16.6, 16.7, 16.8, 16.9, 17.0, 17.1, 17.2, 17.3, 17.4, 17.5, 17.6, 17.7, 17.8, 17.9, 18.0, 18.1, 18.2, 18.3, 18.4, 18.5],
            plus2SD_LILA_raw: [15.6, 15.7, 15.8, 15.9, 16.0, 16.1, 16.2, 16.3, 16.4, 16.5, 16.6, 16.7, 16.8, 16.9, 17.0, 17.1, 17.2, 17.3, 17.4, 17.5, 17.6, 17.7, 17.8, 17.9, 18.0, 18.1, 18.2, 18.3, 18.4, 18.5, 18.6, 18.7, 18.8, 18.9, 19.0, 19.1, 19.2],
            plus3SD_LILA_raw: [16.3, 16.4, 16.5, 16.6, 16.7, 16.8, 16.9, 17.0, 17.1, 17.2, 17.3, 17.4, 17.5, 17.6, 17.7, 17.8, 17.9, 18.0, 18.1, 18.2, 18.3, 18.4, 18.5, 18.6, 18.7, 18.8, 18.9, 19.0, 19.1, 19.2, 19.3, 19.4, 19.5, 19.6, 19.7, 19.8, 19.9],
        },
        'Laki-laki': {
            minus3SD_LILA_raw: [12.0, 12.1, 12.1, 12.2, 12.2, 12.3, 12.3, 12.3, 12.4, 12.4, 12.4, 12.4, 12.5, 12.5, 12.5, 12.5, 12.6, 12.6, 12.6, 12.6, 12.6, 12.7, 12.7, 12.7, 12.7, 12.7, 12.8, 12.8, 12.8, 12.8, 12.8, 12.8, 12.8, 12.9, 12.9, 12.9, 12.9],
            minus2SD_LILA_raw: [13.0, 13.0, 13.1, 13.1, 13.2, 13.2, 13.3, 13.3, 13.3, 13.4, 13.4, 13.4, 13.5, 13.5, 13.5, 13.5, 13.6, 13.6, 13.6, 13.6, 13.6, 13.7, 13.7, 13.7, 13.7, 13.8, 13.8, 13.8, 13.9, 13.9, 13.9, 13.9, 13.9, 13.9, 13.9, 14.0, 14.0],
            minus1SD_LILA_raw: [14.0, 14.1, 14.1, 14.2, 14.2, 14.3, 14.3, 14.4, 14.4, 14.4, 14.5, 14.5, 14.5, 14.6, 14.6, 14.6, 14.7, 14.7, 14.7, 14.8, 14.8, 14.8, 14.8, 14.9, 14.9, 14.9, 15.0, 15.0, 15.0, 15.0, 15.1, 15.1, 15.1, 15.1, 15.2, 15.2, 15.2],
            median_LILA_raw: [15.2, 15.2, 15.3, 15.3, 15.4, 15.4, 15.5, 15.5, 15.6, 15.6, 15.7, 15.7, 15.7, 15.8, 15.8, 15.8, 15.9, 15.9, 15.9, 16.0, 16.0, 16.0, 16.1, 16.1, 16.1, 16.2, 16.3, 16.3, 16.3, 16.3, 16.4, 16.4, 16.4, 16.4, 16.5, 16.5, 16.5],
            plus1SD_LILA_raw: [16.4, 16.4, 16.5, 16.6, 16.6, 16.7, 16.8, 16.8, 16.9, 16.9, 17.0, 17.0, 17.1, 17.1, 17.1, 17.2, 17.2, 17.3, 17.3, 17.3, 17.4, 17.4, 17.5, 17.5, 17.5, 17.6, 17.7, 17.7, 17.7, 17.8, 17.9, 17.9, 17.9, 17.9, 18.0, 18.0, 18.0],
            plus2SD_LILA_raw: [17.7, 17.8, 17.9, 17.9, 18.0, 18.1, 18.1, 18.1, 18.3, 18.4, 18.4, 18.4, 18.5, 18.6, 18.6, 18.7, 18.7, 18.8, 18.8, 18.9, 18.9, 19.0, 19.0, 19.1, 19.1, 19.2, 19.2, 19.3, 19.3, 19.4, 19.4, 19.5, 19.5, 19.6, 19.6, 19.7, 19.8],
            plus3SD_LILA_raw: [19.2, 19.2, 19.3, 19.4, 19.5, 19.6, 19.7, 19.7, 19.8, 19.9, 20.0, 20.0, 20.1, 20.2, 20.2, 20.2, 20.3, 20.3, 20.3, 20.4, 20.5, 20.5, 20.6, 20.6, 20.6, 20.7, 20.7, 20.8, 20.8, 20.9, 21.0, 21.0, 21.0, 21.1, 21.1, 21.2, 21.3],
        }
    };

    const standarLILA = standarLingkarLengan[anak.jenis_kelamin];

    // Buat data standar untuk semua umur 24-60
    const minus3SD_LILA = createStandardData(standarLILA.minus3SD_LILA_raw, 48);
    const minus2SD_LILA = createStandardData(standarLILA.minus2SD_LILA_raw, 48);
    const minus1SD_LILA = createStandardData(standarLILA.minus1SD_LILA_raw, 48);
    const median_LILA = createStandardData(standarLILA.median_LILA_raw, 48);
    const plus1SD_LILA = createStandardData(standarLILA.plus1SD_LILA_raw, 48);
    const plus2SD_LILA = createStandardData(standarLILA.plus2SD_LILA_raw, 48);
    const plus3SD_LILA = createStandardData(standarLILA.plus3SD_LILA_raw, 48);

    // Grafik Lingkar Lengan Anak
    new Chart(document.getElementById('lingkarLenganPerempuanChart'), {
        type: 'line',
        data: {
            labels: umurAnak, // Gunakan semua umur 24-60 sebagai label
            datasets: [{
                    label: 'Lingkar Lengan Anak',
                    data: lingkarLenganAnak,
                    borderColor: 'purple',
                    backgroundColor: 'purple',
                    type: 'scatter',
                    showLine: false,
                    pointRadius: 6,
                    pointHoverRadius: 8
                },
                {
                    label: '-3 SD',
                    data: minus3SD_LILA,
                    borderColor: 'red',
                    borderWidth: 1.5,
                    fill: '+1',
                    backgroundColor: '#FFD1D1',
                    pointRadius: 0
                },
                {
                    label: '-2 SD',
                    data: minus2SD_LILA,
                    borderColor: 'orange',
                    borderWidth: 1.5,
                    fill: '+1',
                    backgroundColor: '#B6FFB6',
                    pointRadius: 0
                },
                {
                    label: '-1 SD',
                    data: minus1SD_LILA,
                    borderColor: 'yellowgreen',
                    borderWidth: 1.5,
                    fill: '+1',
                    backgroundColor: '#E2FFB2',
                    pointRadius: 0
                },
                {
                    label: 'Normal',
                    data: median_LILA,
                    borderColor: 'green',
                    borderWidth: 2,
                    fill: '+1',
                    backgroundColor: '#FFF7B2',
                    pointRadius: 0
                },
                {
                    label: '+1 SD',
                    data: plus1SD_LILA,
                    borderColor: 'yellowgreen',
                    borderWidth: 1.5,
                    fill: '+1',
                    backgroundColor: '#E2FFB2',
                    pointRadius: 0
                },
                {
                    label: '+2 SD',
                    data: plus2SD_LILA,
                    borderColor: 'orange',
                    borderWidth: 1.5,
                    fill: '+1',
                    pointRadius: 0
                },
                {
                    label: '+3 SD',
                    data: plus3SD_LILA,
                    borderColor: 'red',
                    borderWidth: 1.5,
                    fill: false,
                    pointRadius: 0
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                title: {
                    display: true,
                    text: 'Grafik Lingkar Kepala Pemeriksaan Balita',
                    font: {
                        size: window.innerWidth < 576 ? 14 : 18
                    }
                },
                legend: {
                    labels: {
                        font: {
                            size: window.innerWidth < 576 ? 10 : 12
                        }
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            // Hanya tampilkan tooltip jika ada data
                            if (context.raw !== null) {
                                return context.dataset.label + ': ' + context.raw;
                            }
                            return null;
                        }
                    }
                }
            },
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Umur (bulan)'
                    },
                    ticks: {
                        maxRotation: 45,
                        minRotation: 30,
                        font: {
                            size: window.innerWidth < 576 ? 9 : 12
                        }
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Lingkar Kepala (cm)'
                    },
                    ticks: {
                        font: {
                            size: window.innerWidth < 576 ? 9 : 12
                        }
                    }
                }
            },
            layout: {
                padding: {
                    right: 20
                }
            }
        },
        plugins: [{
            id: 'endLabelPlugin',
            afterDatasetsDraw(chart) {
                const ctx = chart.ctx;
                ctx.save();
                ctx.font = 'bold 12px Arial';
                ctx.fillStyle = 'black';
                ctx.textAlign = 'left';
                const datasets = chart.data.datasets;
                const labelsText = ['-3', '-2', '-1', '0', '1', '2', '3'];

                datasets.forEach((ds, i) => {
                    if (i === 0) return; // Skip dataset pertama (data anak)
                    const meta = chart.getDatasetMeta(i);

                    // Pastikan meta.data ada dan memiliki elemen
                    if (meta.data && meta.data.length > 0) {
                        const lastPoint = meta.data[meta.data.length - 1];
                        if (lastPoint) {
                            const y = lastPoint.y;
                            const x = lastPoint.x + 5;
                            ctx.fillText(labelsText[i - 1], x, y + 4);
                        }
                    }
                });
                ctx.restore();
            }
        }]
    });
</script>
@endsection