<?php

namespace App\Http\Controllers;

use App\Models\Anak;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\CitraTelapakKaki;
use Illuminate\Support\Facades\Log;

class PDFController extends Controller
{
    public function downloadPdf(Request $request)
    {
        try {
            // Tingkatkan memory limit
            ini_set('memory_limit', '512M');
            ini_set('max_execution_time', '300');

            $images = $request->input('images');
            $nik = $request->input('nik');

            // Validasi images
            if (!$images || count($images) === 0) {
                Log::warning("Tidak ada images diterima");
                return response()->json(['error' => 'Tidak ada gambar dikirim'], 400);
            }

            Log::info("Received " . count($images) . " images");

            // Proses base64 images
            $processedImages = [];
            foreach ($images as $index => $imgData) {
                if (strpos($imgData, 'data:image') === 0) {
                    // Ini base64 dari canvas - gunakan langsung
                    $processedImages[] = $imgData;
                    Log::info("Image $index: base64 canvas data");
                } else {
                    // Ini path file - convert ke base64
                    if (file_exists($imgData)) {
                        $type = pathinfo($imgData, PATHINFO_EXTENSION);
                        $data = file_get_contents($imgData);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        $processedImages[] = $base64;
                        Log::info("Image $index: file path converted to base64");
                    }
                }
            }

            // Ambil data anak berdasarkan NIK
            $anak = Anak::where('nik', $nik)->first();
            if ($anak) {
                $latestCitra = CitraTelapakKaki::whereHas('pemeriksaan', function ($query) use ($anak) {
                    $query->where('id_anak', $anak->id);
                })->latest('created_at')->first();

                if ($latestCitra) {
                    $citraPath = public_path('storage/' . $latestCitra->path_citra);

                    // Cek file exists
                    if (file_exists($citraPath)) {
                        $type = pathinfo($citraPath, PATHINFO_EXTENSION);
                        $data = file_get_contents($citraPath);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                        $processedImages[] = $base64;
                        Log::info("Citra telapak kaki added");
                    } else {
                        Log::warning("Citra file not found: " . $citraPath);
                    }
                }
            }

            // Generate PDF
            $pdf = Pdf::loadView('frontend.pdf.pdf', [
                'images' => $processedImages,
                'namaAnak' => $request->input('nama_anak'),
                'jenisKelamin' => $request->input('jenis_kelamin'),
            ])
                ->setPaper('a4', 'portrait')
                ->setOption('isRemoteEnabled', true); // Untuk load gambar external

            return response($pdf->output(), 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="hasil_pemeriksaan.pdf"');
        } catch (\Exception $e) {
            Log::error('PDF gagal dibuat: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ], 500);
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
