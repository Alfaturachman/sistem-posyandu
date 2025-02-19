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

        // Ambil data pemeriksaan terkait anak tersebut
        $pemeriksaans = Pemeriksaan::where('id_anak', $anak->id)->get();

        // Kirim data ke tampilan
        return view('frontend.show', compact('anak', 'pemeriksaans'));
    }
}
