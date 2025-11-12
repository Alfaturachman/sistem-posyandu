<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Pemeriksaan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Header yang diperbaiki */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            /* ⬅️ ini yang penting, sejajarkan tengah */
            border-bottom: 2px solid #00aaff;
            padding: 15px 20px;
            margin-bottom: 20px;
            background-color: #f8fdff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }


        .header-left {
            display: flex;
            flex-direction: row;
            /* pastikan horizontal */
            align-items: center;
            /* sejajarkan vertikal tengah */
            gap: 15px;
            /* jarak antar logo dan teks */
        }

        .header-left img {
            height: 70px;
            width: auto;
            flex-shrink: 0;
            border-radius: 5px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .header-center {
            font-size: 16px;
            font-weight: bold;
            color: #00aaff;
            line-height: 1.2;
            margin: 0;
            /* hapus margin bawaan */
            padding: 0;
            /* hapus padding */
            display: flex;
            align-items: center;
            /* pastikan teks rata tengah */
        }

        .header-right {
            font-size: 13px;
            font-weight: bold;
            text-align: left;
            line-height: 1.5;
            background-color: #e6f7ff;
            padding: 10px 15px;
            border-radius: 5px;
            border: 1px solid #b3e0ff;
            min-width: 200px;
        }

        .header-right-title {
            font-size: 14px;
            color: #00aaff;
            margin-bottom: 5px;
            border-bottom: 1px solid #b3e0ff;
            padding-bottom: 3px;
        }

        /* Identitas tambahan */
        .identitas {
            margin: 20px;
            font-size: 13px;
        }

        .footer {
            position: fixed;
            bottom: -10px;
            left: 0;
            right: 0;
            text-align: center;
            font-size: 10px;
            color: #777;
        }

        .footer .page:after {
            content: counter(page);
        }

        h2 {
            margin-top: 0;
            font-size: 16px;
            margin-bottom: 10px;
            color: #006699;
            border-bottom: 1px solid #e0e0e0;
            padding-bottom: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 12px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px 10px;
            text-align: left;
        }

        table th {
            background: #e6f7ff;
            color: #006699;
            font-weight: bold;
        }

        .chart {
            margin-bottom: 20px;
            text-align: center;
        }

        .chart img {
            max-width: 100%;
            border: 1px solid #e0e0e0;
            padding: 5px;
            border-radius: 5px;
        }
        .note-box {
            background-color: #ffe6f0; /* merah muda */
            border: 1px solid #ff99bb;
            padding: 12px 15px;
            margin: 20px;
            font-size: 12px;
            font-weight: bold;
            color: #b30047;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>

<body>
    <!-- Header yang diperbaiki -->
    <div class="header">
        <!-- Kiri (logo + teks tagline) -->
        <div class="header-left">
            <img src="{{ public_path('frontend/assets/img/logo.png') }}" alt="Logo">
            <div class="header-center">
                DARI KAMI UNTUK INDONESIA LEBIH MAJU
            </div>
        </div>

        <!-- Kanan (identitas pasien) -->
        <div class="header-right">
            <div class="header-right-title">IDENTITAS ANAK</div>
            NAMA: {{ $namaAnak }}<br>
            JENIS KELAMIN: {{ $jenisKelamin }}<br>
        </div>
    </div>

    <!-- Content -->
    <h2>Kategori Status Gizi</h2>
    <table>
        <thead>
            <tr>
                <th>Kategori Status Gizi</th>
                <th>Ambang Batas (Z-Score)</th>
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

    <h2>Grafik Pemeriksaan</h2>
@foreach ($images as $img)
    <div class="chart">
        <img src="{{ $img }}">
    </div>
@endforeach

    <div class="note-box">
        JIKA GRAFIK HASIL PEMERIKSAAN MENDATAR ATAU MENURUN MEMOTONG GARIS, 
        HAL ITU MENANDAKAN KURANG DARI KENAIKAN MINIMAL, YANG DI SESUAIKAN 
        OLEH PERMENKES NOMOR 2 TAHUN 2020 TENTANG STANDAR ANTROPOMETRI ANAK
    </div>

    <!-- Footer -->
    <div class="footer">
        <span class="page"></span>
    </div>
</body>

</html>