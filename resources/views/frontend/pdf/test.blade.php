<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Hasil Pemeriksaan</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; margin: 0; padding: 0; }
        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding: 10px 20px;
            margin-bottom: 20px;
        }
        .header-left {
            text-align: center;
        }
        .header-left img {
            height: 70px;
            display: block;
            margin: 0 auto 5px;
        }
        .header-left .title {
            font-size: 14px;
            font-weight: bold;
            color: #00aaff;
        }
        .header-center {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            line-height: 1.4;
        }
        .header-right {
            font-size: 13px;
            font-weight: bold;
            text-align: left;
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
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            font-size: 12px;
        }
        table th, table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }
        table th {
            background: #f2f2f2;
        }
        .chart {
            margin-bottom: 20px;
            text-align: center;
        }
        .chart img {
            max-width: 100%;
            border: 1px solid #ccc;
            padding: 5px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <!-- Kiri -->
        <div class="header-left">
            <img src="{{ public_path('frontend/assets/img/logo.png') }}" alt="Logo">
            <div class="title">E-GROWTH</div>
        </div>

        <!-- Tengah -->
        <div class="header-center">
            DARI KAMI <br>
            UNTUK <br>
            INDONESIA <br>
            LEBIH MAJU
        </div>

        <!-- Kanan -->
        <div class="header-right">
            NAMA: {{ $namaAnak }} <br>
            NIK: {{ $nik ?? '-' }}
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

    <!-- Footer -->
    <div class="footer">
        <span class="page"></span>
    </div>
</body>
</html>
