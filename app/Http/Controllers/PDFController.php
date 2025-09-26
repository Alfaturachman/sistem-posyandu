<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PDFController extends Controller
{
    public function downloadPdf(Request $request)
    {
        try {
            $images = $request->input('images');

            if (!$images || count($images) === 0) {
                Log::warning("Tidak ada images diterima");
                return response()->json(['error' => 'Tidak ada gambar dikirim'], 400);
            }

            $pdf = Pdf::loadView('frontend.pdf.pdf', [
                'images' => $images,
                'namaAnak' => $request->input('nama_anak'),
                'jenisKelamin' => $request->input('jenis_kelamin')
            ])->setPaper('a4', 'portrait');


            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="hasil_pemeriksaan.pdf"');
        } catch (\Exception $e) {
            Log::error('PDF gagal dibuat: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function testPdf()
    {
        $images = [
            public_path('frontend/assets/img/logo.png'), // contoh dummy image
        ];
        $namaAnak = "Contoh Anak";
        $jenisKelamin = "Laki-laki";

        return view('frontend.pdf.test', [
            'images' => $images,
            'namaAnak' => $namaAnak,
            'jenisKelamin' => $jenisKelamin
        ]);
    }
}
