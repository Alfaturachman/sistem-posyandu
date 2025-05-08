<?php

namespace App\Http\Controllers;

use App\Models\Anak;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;
use App\Jobs\ProcessImageJob;
use App\Models\CitraTelapakKaki;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

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
        $anak = Anak::with([
            'pemeriksaans.citraTelapakKaki',
            'pemeriksaans.petugas'
        ])->findOrFail($id);

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
        $pemeriksaan = Pemeriksaan::create($data);

        // Handle image upload (either from file or camera)
        if ($request->hasFile('citra_telapak_kaki')) {
            $file = $request->file('citra_telapak_kaki');

            // Process the image
            $hasilProses = $this->processFootImage($file);

            if (isset($hasilProses['error'])) {
                return redirect()->back()->with('error', $hasilProses['error']);
            }

            // Konversi dari mm ke cm jika perlu (pastikan tidak salah satuan)
            $panjang_cm = $hasilProses['panjang_telapak_kaki'] / 100; // mm → cm
            $lebar_cm = $hasilProses['lebar_telapak_kaki'] / 100; // mm → cm

            // Simpan hasil pemrosesan citra
            CitraTelapakKaki::create([
                'id_pemeriksaan' => $pemeriksaan->id,
                'path_citra' => $hasilProses['processed_image'],
                'panjang_telapak_kaki' => number_format($panjang_cm, 2),
                'lebar_telapak_kaki' => number_format($lebar_cm, 2),
                'clarke_angle' => number_format($hasilProses['clarke_angle'], 2),
            ]);
        }

        return redirect()->route('periksa')->with('success', 'Data pemeriksaan berhasil ditambahkan.');
    }

    private function processFootImage($file)
    {
        // Store original image
        $originalPath = $file->store('uploads/originals', 'public');
        $fullPath = Storage::disk('public')->path($originalPath);

        if (!file_exists($fullPath)) {
            return ['error' => 'File tidak ditemukan!'];
        }

        // Determine image type and create resource
        $imageInfo = getimagesize($fullPath);
        $mime = $imageInfo['mime'];

        // Create image resource based on mime type
        switch ($mime) {
            case 'image/jpeg':
                $imageResource = imagecreatefromjpeg($fullPath);
                break;
            case 'image/png':
                $imageResource = imagecreatefrompng($fullPath);
                break;
            default:
                return ['error' => 'Format gambar tidak didukung!'];
        }

        // Convert to grayscale
        imagefilter($imageResource, IMG_FILTER_GRAYSCALE);

        // Get image dimensions
        $width = imagesx($imageResource);
        $height = imagesy($imageResource);

        // Create binary image
        $binaryImage = imagecreatetruecolor($width, $height);

        // Convert to binary (black and white)
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $rgb = imagecolorat($imageResource, $x, $y);
                $colors = imagecolorsforindex($imageResource, $rgb);

                // Threshold for binary conversion
                $binaryColor = ($colors['red'] > 128) ? 255 : 0;
                $binaryColorIndex = imagecolorallocate($binaryImage, $binaryColor, $binaryColor, $binaryColor);
                imagesetpixel($binaryImage, $x, $y, $binaryColorIndex);
            }
        }

        // Rotate image 90 degrees clockwise
        $rotatedBinary = imagerotate($binaryImage, -90, 0);

        // Get rotated image dimensions
        $width = imagesx($rotatedBinary);
        $height = imagesy($rotatedBinary);

        // Image segmentation variables
        $top = $height;
        $bottom = 0;
        $left = $width;
        $right = 0;

        // Segment image into three parts
        $segmentHeight = floor($height / 3);

        $regionPixels = [
            'toe' => ['x_total' => 0, 'y_total' => 0, 'count' => 0],
            'arch' => ['x_total' => 0, 'y_total' => 0, 'count' => 0],
            'heel' => ['x_total' => 0, 'y_total' => 0, 'count' => 0]
        ];

        // Deteksi pixel hitam dengan sampling
        $sampling_interval = max(1, floor($width / 500)); // Sampling untuk mengurangi beban memori

        // Deteksi semua pixel hitam dengan pembobotan
        for ($y = 0; $y < $height; $y += $sampling_interval) {
            for ($x = 0; $x < $width; $x += $sampling_interval) {
                $rgb = imagecolorat($rotatedBinary, $x, $y);
                $colors = imagecolorsforindex($rotatedBinary, $rgb);

                if ($colors['red'] == 0) { // Black pixel
                    // Update boundary variables
                    $top = min($top, $y);
                    $bottom = max($bottom, $y);
                    $left = min($left, $x);
                    $right = max($right, $x);

                    // Tentukan region
                    if ($y < $segmentHeight) { // Toe region
                        $regionPixels['toe']['x_total'] += $x;
                        $regionPixels['toe']['y_total'] += $y;
                        $regionPixels['toe']['count']++;
                    } elseif ($y >= $segmentHeight && $y < 2 * $segmentHeight) { // Arch region
                        $regionPixels['arch']['x_total'] += $x;
                        $regionPixels['arch']['y_total'] += $y;
                        $regionPixels['arch']['count']++;
                    } else { // Heel region
                        $regionPixels['heel']['x_total'] += $x;
                        $regionPixels['heel']['y_total'] += $y;
                        $regionPixels['heel']['count']++;
                    }
                }
            }
        }

        // Calculate foot dimensions
        $footLength = max(0, $right - $left);
        $footWidth = max(0, $bottom - $top);

        // Hitung titik rata-rata
        $points = [];
        foreach ($regionPixels as $key => $region) {
            if ($region['count'] > 0) {
                $points[$key] = [
                    'x' => round($region['x_total'] / $region['count']),
                    'y' => round($region['y_total'] / $region['count'])
                ];
            }
        }

        // Fallback jika deteksi gagal
        if (empty($points['toe']) || empty($points['heel']) || empty($points['arch'])) {
            $points = [
                'toe' => ['x' => $left, 'y' => $top],
                'heel' => ['x' => $right, 'y' => $bottom],
                'arch' => [
                    'x' => ($left + $right) / 2,
                    'y' => ($top + $bottom) / 2
                ]
            ];
        }

        Log::info("Memory-Efficient Points Detection:", $points);

        // Perhitungan vektor dengan log tambahan
        $V31x = $points['heel']['x'] - $points['toe']['x'];
        $V31y = $points['heel']['y'] - $points['toe']['y'];
        $V32x = $points['heel']['x'] - $points['arch']['x'];
        $V32y = $points['heel']['y'] - $points['arch']['y'];

        Log::info("Detailed Vector Calculation:", [
            'V31x' => $V31x,
            'V31y' => $V31y,
            'V32x' => $V32x,
            'V32y' => $V32y
        ]);

        // Perhitungan panjang vektor
        $V_31 = sqrt(pow($V31x, 2) + pow($V31y, 2));
        $V_32 = sqrt(pow($V32x, 2) + pow($V32y, 2));

        Log::info("Vector Lengths:", [
            'V_31' => $V_31,
            'V_32' => $V_32
        ]);

        // Pastikan tidak ada pembagian dengan nol
        if ($V_31 > 0 && $V_32 > 0) {
            $V31dotV32 = $V31x * $V32x + $V31y * $V32y;
            $cosTheta = $V31dotV32 / ($V_31 * $V_32);
            $cosTheta = max(min($cosTheta, 1), -1);

            $clarkeAngle = rad2deg(acos($cosTheta));

            Log::info("Detailed Angle Calculation:", [
                'dot_product' => $V31dotV32,
                'cos_theta' => $cosTheta,
                'clarke_angle' => $clarkeAngle
            ]);
        } else {
            Log::error("Vector length zero detected");
            $clarkeAngle = 0;
        }

        // Hitung dot product
        $V31dotV32 = $V31x * $V32x + $V31y * $V32y;

        // Gunakan metode cross product untuk sudut
        $crossProduct = $V31x * $V32y - $V31y * $V32x;
        $dotProduct = $V31x * $V32x + $V31y * $V32y;

        // Hitung sudut menggunakan atan2
        $clarkeAngle = abs(rad2deg(atan2($crossProduct, $dotProduct)));

        Log::info("Angle Calculation Method 2:", [
            'cross_product' => $crossProduct,
            'dot_product' => $dotProduct,
            'clarke_angle' => $clarkeAngle
        ]);

        // Tambahkan validasi tambahan
        if (!is_numeric($clarkeAngle) || $clarkeAngle < 0 || $clarkeAngle > 180) {
            Log::error('Sudut Clarke tidak valid: ' . $clarkeAngle);
            return ['error' => 'Gagal menghitung Clarke Angle karena hasil tidak valid!'];
        }

        // Arch type classification
        $archType = "Normal";
        if ($clarkeAngle < 30) {
            $archType = "Flat Foot (Pes Planus)";
        } elseif ($clarkeAngle >= 30 && $clarkeAngle < 45) {
            $archType = "Moderate Arch";
        } elseif ($clarkeAngle >= 45) {
            $archType = "High Arch (Pes Cavus)";
        }

        // Save processed image
        $processedFilename = 'processed_' . time() . '.jpg';
        $processedPath = 'uploads/processed/' . $processedFilename;

        // Save the rotated binary image
        imagejpeg($rotatedBinary, Storage::disk('public')->path($processedPath), 80);

        // Free up memory
        imagedestroy($imageResource);
        imagedestroy($binaryImage);
        imagedestroy($rotatedBinary);

        return [
            'message' => 'Citra berhasil diproses!',
            'processed_image' => $processedPath,
            'panjang_telapak_kaki' => $footLength,
            'lebar_telapak_kaki' => $footWidth,
            'clarke_angle' => round($clarkeAngle, 2),
            'arch_type' => $archType,
            'debug_points' => $points
        ];
    }
}
