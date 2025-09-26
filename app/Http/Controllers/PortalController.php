<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemeriksaan;
use App\Models\Anak;

class PortalController extends Controller
{
    public function index()
    {
        return view('frontend.portal');
    }

    public function cariPemeriksaan(Request $request)
    {
        $nik = trim($request->input('nik')); // Menghilangkan spasi yang tidak perlu

        // Cari data anak berdasarkan NIK
        $anak = Anak::where('nik', $nik)->first();

        // Jika data ditemukan
        if ($anak) {
            // Ambil data pemeriksaan terkait anak tersebut
            $pemeriksaans = $anak->pemeriksaans;

            // Mengembalikan response JSON dengan data anak dan pemeriksaannya
            return response()->json([
                'success' => true,
                'anak' => $anak,
                'pemeriksaans' => $pemeriksaans
            ], 200);
        } else {
            // Jika data anak tidak ditemukan
            return response()->json([
                'success' => false,
                'message' => 'Data anak tidak ditemukan'
            ], 404);
        }
    }

    public function show($nik)
    {
        // Cari data anak berdasarkan NIK
        $anak = Anak::where('nik', $nik)->first();

        if (!$anak) {
            return abort(404, 'Data anak tidak ditemukan');
        }

        // Ambil semua data pemeriksaan terkait anak tersebut
        $pemeriksaans = Pemeriksaan::with('citraTelapakKaki')
            ->where('id_anak', $anak->id)
            ->orderBy('tanggal_periksa', 'asc')
            ->get();

        // Hitung umur anak pada setiap pemeriksaan (dalam bulan) dan simpan data terbaru
        $beratData = array_fill(24, 37, null);
        $tinggiData = array_fill(24, 37, null);
        $lingkarKepalaData = array_fill(24, 37, null);
        $lingkarLenganData = array_fill(24, 37, null);
        $filteredPemeriksaans = [];

        foreach ($pemeriksaans as $pemeriksaan) {
            // Hitung umur dalam bulan pada saat pemeriksaan
            $tanggalLahir = \Carbon\Carbon::parse($anak->tanggal_lahir);
            $tanggalPeriksa = \Carbon\Carbon::parse($pemeriksaan->tanggal_periksa);
            $umurBulan = $tanggalLahir->diffInMonths($tanggalPeriksa);

            // Hanya menyertakan data dengan umur 24-60 bulan
            if ($umurBulan >= 24 && $umurBulan <= 60) {
                // Simpan hanya data terbaru untuk setiap umur
                $beratData[$umurBulan] = $pemeriksaan->berat_badan;
                $tinggiData[$umurBulan] = $pemeriksaan->tinggi_badan;
                $lingkarKepalaData[$umurBulan] = $pemeriksaan->lingkar_kepala;
                $lingkarLenganData[$umurBulan] = $pemeriksaan->lingkar_lengan;
                $filteredPemeriksaans[] = $pemeriksaan;
            }
        }

        // Buat array untuk sumbu X (24-60)
        $umurData = range(24, 60);

        // Jika tidak ada data dalam rentang 24-60 bulan
        if (empty($filteredPemeriksaans)) {
            return view('frontend.show', [
                'anak' => $anak,
                'pemeriksaans' => collect([]),
                'beratData' => array_values($beratData),
                'tinggiData' => array_values($tinggiData),
                'lingkarKepalaData' => array_values($lingkarKepalaData),
                'lingkarLenganData' => array_values($lingkarLenganData),
                'umurData' => $umurData,
                'message' => 'Tidak ada data pemeriksaan dalam rentang umur 24-60 bulan'
            ]);
        }

        // Kirim data ke view
        return view('frontend.show', compact(
            'anak',
            'pemeriksaans',
            'beratData',
            'tinggiData',
            'lingkarKepalaData',
            'lingkarLenganData',
            'umurData'
        ));
    }
}
