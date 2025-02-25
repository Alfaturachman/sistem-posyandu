<?php

namespace App\Http\Controllers;

use App\Models\Anak;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;
use App\Jobs\ProcessImageJob;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class PeriksaAnakController extends Controller
{
    // Menampilkan daftar anak dalam tabel
    public function hasil()
    {
        $anakList = Anak::all();
        return view('backend.pages.periksa-anak.index', compact('anakList'));
    }

    public function hasil_detail($id)
    {
        $anak = Anak::with('pemeriksaans')->findOrFail($id);
        return view('backend.pages.periksa-anak.detail', compact('anak'));
    }

    public function edit_identitas($id)
    {
        $anak = Anak::findOrFail($id);
        return view('backend.pages.periksa-anak.edit-identitas', compact('anak'));
    }

    public function update_identitas(Request $request, $id)
    {
        $request->validate([
            'nik' => 'required|max:16',
            'nama_anak' => 'required|string|max:255',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'nama_ibu' => 'required|string',
        ]);

        $anak = Anak::findOrFail($id);
        $anak->update($request->all());

        return redirect()->route('edit-identitas', $id)->with('success', 'Identitas anak berhasil diperbarui.');
    }

    public function periksa()
    {
        $anakList = Anak::all();
        return view('backend.pages.periksa-anak.periksa', compact('anakList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_anak' => 'required|exists:anak,id',
            'berat_badan' => 'required|numeric|min:0',
            'tinggi_badan' => 'required|numeric|min:0',
            'lingkar_lengan' => 'required|numeric|min:0',
            'lingkar_kepala' => 'required|numeric|min:0',
            'citra_telapak_kaki' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Simpan data pemeriksaan
        $data = $request->only(['id_anak', 'berat_badan', 'tinggi_badan', 'lingkar_lengan', 'lingkar_kepala']);

        if ($request->hasFile('citra_telapak_kaki')) {
            $file = $request->file('citra_telapak_kaki');
        
            // Simpan file asli
            $originalPath = $file->store('uploads/originals', 'public');
            $fullPath = Storage::disk('public')->path($originalPath);
        
            // Cek apakah file benar-benar ada
            if (!file_exists($fullPath)) {
                return response()->json(['error' => 'File tidak ditemukan!'], 404);
            }
        
            // Buat image resource berdasarkan jenis file
            $imageInfo = getimagesize($fullPath);
            $mime = $imageInfo['mime'];
        
            if ($mime == 'image/jpeg') {
                $imageResource = imagecreatefromjpeg($fullPath);
            } elseif ($mime == 'image/png') {
                $imageResource = imagecreatefrompng($fullPath);
            } else {
                return response()->json(['error' => 'Format gambar tidak didukung!'], 400);
            }
        
            // Ubah ke grayscale
            imagefilter($imageResource, IMG_FILTER_GRAYSCALE);
        
            // Thresholding manual (binarization)
            $width = imagesx($imageResource);
            $height = imagesy($imageResource);
            $threshold = 128;
        
            // Variabel untuk menyimpan batas atas, bawah, kiri, dan kanan
            $top = $height;
            $bottom = 0;
            $left = $width;
            $right = 0;
        
            $archY = $height; // Posisi vertikal arch paling rendah
            $archX = 0; // Posisi horizontal arch
        
            for ($y = 0; $y < $height; $y++) {
                for ($x = 0; $x < $width; $x++) {
                    // Ambil nilai warna piksel
                    $rgb = imagecolorat($imageResource, $x, $y);
                    $colors = imagecolorsforindex($imageResource, $rgb);
        
                    // Ambil grayscale value (karena sudah grayscale, maka R=G=B)
                    $gray = $colors['red'];
        
                    // Binarisasi: jika di bawah threshold, set hitam; jika di atas, set putih
                    $newColor = ($gray < $threshold) ? 0 : 255;
                    $colorAllocated = imagecolorallocate($imageResource, $newColor, $newColor, $newColor);
                    imagesetpixel($imageResource, $x, $y, $colorAllocated);
        
                    // Jika piksel putih (telapak kaki), perbarui batas atas, bawah, kiri, kanan
                    if ($newColor == 255) { 
                        if ($y < $top) $top = $y;
                        if ($y > $bottom) $bottom = $y;
                        if ($x < $left) $left = $x;
                        if ($x > $right) $right = $x;
                    }
                }
            }
        
            // Menentukan titik-titik utama
            $heelY = $bottom; // Tumit (A) ada di bagian paling bawah
            $toeY = $top; // Jari kaki (C) ada di bagian paling atas
        
            // Cari titik terdalam arch (B)
            for ($y = $top; $y <= $bottom; $y++) {
                for ($x = $left; $x <= $right; $x++) {
                    if (imagecolorat($imageResource, $x, $y) == 255) {
                        if ($y > $archY) { // Cari titik terdalam arch
                            $archY = $y;
                            $archX = $x;
                        }
                    }
                }
            }
        
            // Hitung jarak vertikal dan horizontal
            $AB = abs($heelY - $archY); // Jarak vertikal dari B ke A
            $BC = abs($archX - $right); // Jarak horizontal dari B ke C
        
            // Hitung Clarke's Angle dalam derajat
            $clarkeAngle = rad2deg(atan2($AB, $BC));
        
            // Klasifikasi arch berdasarkan Clarke’s Angle
            $archType = "Normal";
            if ($clarkeAngle < 30) {
                $archType = "Flat Foot (Pes Planus)";
            } elseif ($clarkeAngle < 45) {
                $archType = "Moderate Flat Foot";
            }
        
            // Simpan hasil olahan dengan nama unik
            $processedFilename = 'processed_' . time() . '.jpg';
            $processedPath = 'uploads/processed/' . $processedFilename;
        
            // Simpan gambar hasil binarisasi
            ob_start();
            imagejpeg($imageResource, null, 80);
            $imageData = ob_get_clean();
            Storage::disk('public')->put($processedPath, $imageData);
        
            // Hapus resource GD
            imagedestroy($imageResource);
        
            // Kembalikan respons dengan hasil analisis
            return response()->json([
                'message' => 'Citra berhasil diproses!',
                'processed_image' => $processedPath,
                'panjang_telapak_kaki' => $bottom - $top,
                'lebar_telapak_kaki' => $right - $left,
                'clarke_angle' => round($clarkeAngle, 2),
                'arch_type' => $archType
            ]);
        }        

        // Simpan data pemeriksaan ke database
        $pemeriksaan = Pemeriksaan::create($data);

        return redirect()->route('periksa')->with('success', 'Data pemeriksaan berhasil ditambahkan.');
    }
}
