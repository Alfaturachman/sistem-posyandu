<?php

namespace App\Http\Controllers;

use App\Models\Anak;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;
use App\Jobs\ProcessImageJob;
use App\Models\CitraTelapakKaki;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

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

        // Ambil data weight dari device_id tertentu
        $latestWeight = DB::table('api_iot')
            ->where('device_id', 'isp32_scale_017')
            ->orderByDesc('timestamp')
            ->value('weight');

        // Ambil data height dari device_id tertentu
        $latestHeight = DB::table('api_iot')
            ->where('device_id', 'isp32_scale_017')
            ->orderByDesc('timestamp')
            ->value('height');

        // Default tanggal periksa
        $tanggalPeriksa = now();

        return view('backend.pages.periksa-anak.periksa', compact('anakList', 'latestWeight', 'latestHeight', 'tanggalPeriksa'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'id_anak' => 'required|exists:anak,id',
            'berat_badan' => 'required|numeric|min:0',
            'tinggi_badan' => 'required|numeric|min:0',
            'lingkar_lengan' => 'required|numeric|min:0',
            'lingkar_kepala' => 'required|numeric|min:0',
            'tanggal_periksa' => 'required|date',
            'citra_telapak_kaki' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Simpan data pemeriksaan
        $data = $request->only([
            'id_anak',
            'berat_badan',
            'tinggi_badan',
            'lingkar_lengan',
            'lingkar_kepala',
            'tanggal_periksa'
        ]);
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
                'path_original' => $hasilProses['original_path'],
                'path_segmented' => $hasilProses['segmented_image'],
                'panjang_telapak_kaki' => number_format($panjang_cm, 2),
                'lebar_telapak_kaki' => number_format($lebar_cm, 2),
                'clarke_angle' => number_format($hasilProses['clarke_angle'], 2),
            ]);
        }

        return redirect()->route('periksa')->with('success', 'Data pemeriksaan berhasil ditambahkan.');
    }


    private function processFootImage($file)
    {
        $timestamp = time();
        $processedImages = [];

        // ===== 0) Baca & validasi =====
        $originalPath = $file->store('uploads/originals', 'public');
        $fullPath = Storage::disk('public')->path($originalPath);

        if (!file_exists($fullPath)) return ['error' => 'File tidak ditemukan!'];

        $info = getimagesize($fullPath);
        if (!$info) return ['error' => 'Gagal membaca metadata gambar!'];

        $mime = $info['mime'];
        if ($mime === 'image/jpeg') $im = imagecreatefromjpeg($fullPath);
        elseif ($mime === 'image/png') $im = imagecreatefrompng($fullPath);
        else return ['error' => 'Format gambar tidak didukung!'];

        $w = imagesx($im);
        $h = imagesy($im);

        // Save Step 0: Original
        $step0Path = "uploads/processed/step_0_original_{$timestamp}.jpg";
        imagejpeg($im, Storage::disk('public')->path($step0Path), 90);
        $processedImages['step_0_original'] = $step0Path;

        // ===== 1) Grayscale =====
        imagefilter($im, IMG_FILTER_GRAYSCALE);

        // Save Step 1: Grayscale
        $step1Path = "uploads/processed/step_1_grayscale_{$timestamp}.jpg";
        imagejpeg($im, Storage::disk('public')->path($step1Path), 90);
        $processedImages['step_1_grayscale'] = $step1Path;

        // ===== 2) FAST Feature Detection =====
        $threshold = 20;
        $keypoints = $this->detectFAST($im, $threshold);

        // Create image with keypoints
        $imFast = imagecreatefromjpeg(Storage::disk('public')->path($step1Path));
        $redColor = imagecolorallocate($imFast, 255, 0, 0);
        foreach ($keypoints as $point) {
            imagefilledellipse($imFast, $point['x'], $point['y'], 5, 5, $redColor);
        }

        // Save Step 2: FAST Keypoints
        $step2Path = "uploads/processed/step_2_fast_keypoints_{$timestamp}.jpg";
        imagejpeg($imFast, Storage::disk('public')->path($step2Path), 90);
        $processedImages['step_2_fast_keypoints'] = $step2Path;
        imagedestroy($imFast);

        // ===== 3) Enhanced tone analysis =====
        $toneMap = array();
        $intensitySum = 0;
        $pixelCount = 0;

        for ($y = 0; $y < $h; $y++) {
            $toneMap[$y] = array();
            for ($x = 0; $x < $w; $x++) {
                $idx = imagecolorat($im, $x, $y);
                $c = imagecolorsforindex($im, $idx);
                $g = $c['red'];
                $toneMap[$y][$x] = $g;
                $intensitySum += $g;
                $pixelCount++;
            }
        }

        $avgIntensity = $intensitySum / $pixelCount;

        // Save Step 3: Tone Map (visualisasi sama dengan grayscale)
        $step3Path = "uploads/processed/step_3_tone_analysis_{$timestamp}.jpg";
        imagejpeg($im, Storage::disk('public')->path($step3Path), 90);
        $processedImages['step_3_tone_analysis'] = $step3Path;

        // ===== 4) Multi-level thresholding - Otsu =====
        $hist = array_fill(0, 256, 0);
        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $g = $toneMap[$y][$x];
                $hist[$g]++;
            }
        }

        $total = $w * $h;
        $sum = 0;
        for ($i = 0; $i < 256; $i++) $sum += $i * $hist[$i];

        $sumB = 0;
        $wB = 0;
        $maxVar = -1;
        $mainThreshold = 128;

        for ($i = 0; $i < 256; $i++) {
            $wB += $hist[$i];
            if ($wB == 0) continue;

            $wF = $total - $wB;
            if ($wF == 0) break;

            $sumB += $i * $hist[$i];
            $mB = $sumB / $wB;
            $mF = ($sum - $sumB) / $wF;
            $between = $wB * $wF * ($mB - $mF) * ($mB - $mF);

            if ($between > $maxVar) {
                $maxVar = $between;
                $mainThreshold = $i;
            }
        }

        $archThresholdLow = $mainThreshold * 0.7;
        $archThresholdHigh = $mainThreshold * 1.3;

        // ===== 5) Buat tiga level segmentasi =====
        $bin = imagecreatetruecolor($w, $h);
        $archMap = imagecreatetruecolor($w, $h);

        $white = imagecolorallocate($bin, 255, 255, 255);
        $black = imagecolorallocate($bin, 0, 0, 0);
        $gray = imagecolorallocate($bin, 128, 128, 128);

        $archWhite = imagecolorallocate($archMap, 255, 255, 255);
        $archBlack = imagecolorallocate($archMap, 0, 0, 0);
        $archGray = imagecolorallocate($archMap, 128, 128, 128);

        imagefilledrectangle($bin, 0, 0, $w, $h, $white);
        imagefilledrectangle($archMap, 0, 0, $w, $h, $archWhite);

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $g = $toneMap[$y][$x];

                if ($g < $mainThreshold) {
                    imagesetpixel($bin, $x, $y, $black);
                    imagesetpixel($archMap, $x, $y, $archBlack);
                } elseif ($g >= $archThresholdLow && $g <= $archThresholdHigh) {
                    imagesetpixel($bin, $x, $y, $gray);
                    imagesetpixel($archMap, $x, $y, $archGray);
                } else {
                    imagesetpixel($bin, $x, $y, $white);
                    imagesetpixel($archMap, $x, $y, $archWhite);
                }
            }
        }

        // Save Step 4-5: Thresholding & Segmentation
        $step4Path = "uploads/processed/step_4_otsu_threshold_{$timestamp}.jpg";
        imagejpeg($bin, Storage::disk('public')->path($step4Path), 90);
        $processedImages['step_4_otsu_threshold'] = $step4Path;

        $step5Path = "uploads/processed/step_5_segmentation_{$timestamp}.jpg";
        imagejpeg($archMap, Storage::disk('public')->path($step5Path), 90);
        $processedImages['step_5_segmentation'] = $step5Path;

        // ===== 6) Morphological operations =====
        $get = function ($img, $x, $y, $includeArch = false) use ($w, $h) {
            if ($x < 0 || $y < 0 || $x >= $w || $y >= $h) return 0;
            $c = imagecolorsforindex($img, imagecolorat($img, $x, $y));

            if ($includeArch) {
                return ($c['red'] <= 128) ? 1 : 0;
            } else {
                return ($c['red'] == 0) ? 1 : 0;
            }
        };

        $tmp = imagecreatetruecolor($w, $h);
        $white2 = imagecolorallocate($tmp, 255, 255, 255);
        $black2 = imagecolorallocate($tmp, 0, 0, 0);
        $gray2 = imagecolorallocate($tmp, 128, 128, 128);
        imagefilledrectangle($tmp, 0, 0, $w, $h, $white2);

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $currentPixel = imagecolorsforindex($bin, imagecolorat($bin, $x, $y));
                $isArch = ($currentPixel['red'] == 128);
                $isFoot = ($currentPixel['red'] == 0);

                if ($isArch) {
                    $archCount = 0;
                    $footCount = 0;
                    for ($dy = -1; $dy <= 1; $dy++) {
                        for ($dx = -1; $dx <= 1; $dx++) {
                            $neighbor = $get($bin, $x + $dx, $y + $dy, true);
                            if ($neighbor == 1) {
                                $neighborPixel = imagecolorsforindex($bin, imagecolorat($bin, $x + $dx, $y + $dy));
                                if ($neighborPixel['red'] == 128) $archCount++;
                                else $footCount++;
                            }
                        }
                    }

                    if ($archCount + $footCount >= 3) {
                        imagesetpixel($tmp, $x, $y, $gray2);
                    } else {
                        imagesetpixel($tmp, $x, $y, $white2);
                    }
                } elseif ($isFoot) {
                    $ok = 1;
                    for ($dy = -1; $dy <= 1; $dy++) {
                        for ($dx = -1; $dx <= 1; $dx++) {
                            $ok &= $get($bin, $x + $dx, $y + $dy, false);
                        }
                    }
                    imagesetpixel($tmp, $x, $y, $ok ? $black2 : $white2);
                } else {
                    imagesetpixel($tmp, $x, $y, $white2);
                }
            }
        }

        // Save Step 6: Morphology
        $step6Path = "uploads/processed/step_6_morphology_{$timestamp}.jpg";
        imagejpeg($tmp, Storage::disk('public')->path($step6Path), 90);
        $processedImages['step_6_morphology'] = $step6Path;

        // ===== 7) Rotasi =====
        $rot = imagerotate($tmp, -90, $white2);
        $w = imagesx($rot);
        $h = imagesy($rot);

        // Save Step 7: Rotation
        $step7Path = "uploads/processed/step_7_rotation_{$timestamp}.jpg";
        imagejpeg($rot, Storage::disk('public')->path($step7Path), 90);
        $processedImages['step_7_rotation'] = $step7Path;

        // ===== 8) Enhanced bounding box =====
        $top = $h;
        $bot = 0;
        $left = $w;
        $right = 0;

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $c = imagecolorsforindex($rot, imagecolorat($rot, $x, $y));
                if ($c['red'] <= 128) {
                    if ($y < $top) $top = $y;
                    if ($y > $bot) $bot = $y;
                    if ($x < $left) $left = $x;
                    if ($x > $right) $right = $x;
                }
            }
        }

        if ($left >= $right || $top >= $bot) {
            imagedestroy($im);
            imagedestroy($bin);
            imagedestroy($archMap);
            imagedestroy($tmp);
            imagedestroy($rot);
            return ['error' => 'Telapak kaki tidak terdeteksi.'];
        }

        $footLength = max(0, $right - $left);
        $footWidth = max(0, $bot - $top);

        // Save Step 8: Bounding Box
        $imBBox = imagecreatefromjpeg(Storage::disk('public')->path($step7Path));
        $bboxColor = imagecolorallocate($imBBox, 255, 0, 0);
        imagerectangle($imBBox, $left, $top, $right, $bot, $bboxColor);
        $step8Path = "uploads/processed/step_8_bounding_box_{$timestamp}.jpg";
        imagejpeg($imBBox, Storage::disk('public')->path($step8Path), 90);
        $processedImages['step_8_bounding_box'] = $step8Path;
        imagedestroy($imBBox);

        // ===== 9) Enhanced arch detection =====
        $yStart = $top + (int)(0.25 * ($bot - $top));
        $yEnd = $top + (int)(0.75 * ($bot - $top));

        $archRegions = array();
        $minArchWidth = PHP_INT_MAX;
        $bestArchY = $top;
        $xS_medial = $left;
        $xS_lateral = $right;

        for ($y = $yStart; $y <= $yEnd; $y++) {
            $minX = null;
            $maxX = null;
            $archMinX = null;
            $archMaxX = null;
            $solidCount = 0;
            $archCount = 0;

            for ($x = $left; $x <= $right; $x++) {
                $c = imagecolorsforindex($rot, imagecolorat($rot, $x, $y));

                if ($c['red'] == 0) {
                    if ($minX === null) $minX = $x;
                    $maxX = $x;
                    $solidCount++;
                } elseif ($c['red'] == 128) {
                    if ($archMinX === null) $archMinX = $x;
                    $archMaxX = $x;
                    $archCount++;

                    if ($minX === null) $minX = $x;
                    $maxX = $x;
                }
            }

            if ($minX !== null && $maxX !== null) {
                $totalWidth = $maxX - $minX + 1;
                $archRatio = $archCount / max(1, $solidCount + $archCount);
                $archScore = $totalWidth * (1 - $archRatio * 0.5);

                if ($archScore < $minArchWidth && $archRatio > 0.1) {
                    $minArchWidth = $archScore;
                    $bestArchY = $y;

                    $xCenter = ($left + $right) / 2.0;
                    $xS_medial = (abs($minX - $xCenter) < abs($maxX - $xCenter)) ? $minX : $maxX;
                    $xS_lateral = ($xS_medial == $minX) ? $maxX : $minX;
                }

                $archRegions[] = array(
                    'y' => $y,
                    'width' => $totalWidth,
                    'arch_ratio' => $archRatio,
                    'solid_count' => $solidCount,
                    'arch_count' => $archCount
                );
            }
        }

        $S = ['x' => $xS_medial, 'y' => $bestArchY];

        // ===== 10) Enhanced heel detection =====
        $yF = $bot;
        $minXF = null;
        $maxXF = null;

        for ($searchY = $bot; $searchY >= $bot - 10 && $searchY >= $top; $searchY--) {
            $tempMinX = null;
            $tempMaxX = null;
            for ($x = $left; $x <= $right; $x++) {
                $c = imagecolorsforindex($rot, imagecolorat($rot, $x, $searchY));
                if ($c['red'] <= 128) {
                    if ($tempMinX === null) $tempMinX = $x;
                    $tempMaxX = $x;
                }
            }

            if ($tempMinX !== null) {
                $minXF = $tempMinX;
                $maxXF = $tempMaxX;
                $yF = $searchY;
                break;
            }
        }

        if ($minXF === null) {
            $minXF = $left;
            $maxXF = $left;
        }

        $xF = (int)round(($minXF + $maxXF) / 2);
        $F = ['x' => $xF, 'y' => $yF];

        // ===== 11) Enhanced toe detection =====
        $yT = $top;
        $xT = $left;

        for ($searchY = $top; $searchY <= $top + 10 && $searchY <= $bot; $searchY++) {
            if ($xS_medial <= ($left + $right) / 2) {
                for ($x = $left; $x <= $right; $x++) {
                    $c = imagecolorsforindex($rot, imagecolorat($rot, $x, $searchY));
                    if ($c['red'] <= 128) {
                        $xT = $x;
                        $yT = $searchY;
                        goto found_toe;
                    }
                }
            } else {
                for ($x = $right; $x >= $left; $x--) {
                    $c = imagecolorsforindex($rot, imagecolorat($rot, $x, $searchY));
                    if ($c['red'] <= 128) {
                        $xT = $x;
                        $yT = $searchY;
                        goto found_toe;
                    }
                }
            }
        }

        found_toe:
        $T = ['x' => $xT, 'y' => $yT];
        $points = ['toe' => $T, 'arch' => $S, 'heel' => $F];

        // Save Step 9-11: Feature Points Detection
        $imPoints = imagecreatefromjpeg(Storage::disk('public')->path($step7Path));
        $redP = imagecolorallocate($imPoints, 255, 0, 0);
        $greenP = imagecolorallocate($imPoints, 0, 255, 0);
        $blueP = imagecolorallocate($imPoints, 0, 0, 255);

        imagefilledellipse($imPoints, $T['x'], $T['y'], 10, 10, $redP);
        imagefilledellipse($imPoints, $S['x'], $S['y'], 10, 10, $greenP);
        imagefilledellipse($imPoints, $F['x'], $F['y'], 10, 10, $blueP);

        $step9Path = "uploads/processed/step_9_11_feature_points_{$timestamp}.jpg";
        imagejpeg($imPoints, Storage::disk('public')->path($step9Path), 90);
        $processedImages['step_9_11_feature_points'] = $step9Path;
        imagedestroy($imPoints);

        // ===== 12) Clarke angle calculation =====
        $STx = $T['x'] - $S['x'];
        $STy = $T['y'] - $S['y'];
        $SFx = $F['x'] - $S['x'];
        $SFy = $F['y'] - $S['y'];

        $magST = sqrt($STx * $STx + $STy * $STy);
        $magSF = sqrt($SFx * $SFx + $SFy * $SFy);

        if ($magST < 1e-6 || $magSF < 1e-6) {
            imagedestroy($im);
            imagedestroy($bin);
            imagedestroy($archMap);
            imagedestroy($tmp);
            imagedestroy($rot);
            return ['error' => 'Vektor terlalu kecil, tidak bisa hitung sudut.'];
        }

        $dot = $STx * $SFx + $STy * $SFy;
        $cosTheta = $dot / ($magST * $magSF);
        $cosTheta = max(-1.0, min(1.0, $cosTheta));

        $angleBetweenVectors = rad2deg(acos($cosTheta));
        $clarkeAngle = 180.0 - $angleBetweenVectors;
        $clarkeAngle = max(0.0, min(180.0, $clarkeAngle));

        if (!is_numeric($clarkeAngle) || $clarkeAngle <= 0 || $clarkeAngle > 180) {
            imagedestroy($im);
            imagedestroy($bin);
            imagedestroy($archMap);
            imagedestroy($tmp);
            imagedestroy($rot);
            return ['error' => 'Gagal menghitung Clarke Angle (hasil tidak valid).'];
        }

        // ===== 13) Classification =====
        $archType = "Normal";
        if ($clarkeAngle < 30) $archType = "Flat Foot (Pes Planus)";
        elseif ($clarkeAngle < 45) $archType = "Moderate Arch";
        else $archType = "High Arch (Pes Cavus)";

        // ===== 14) Create visualization =====
        $overlay = imagecreatetruecolor($w, $h);
        $whiteOL = imagecolorallocate($overlay, 255, 255, 255);
        imagefilledrectangle($overlay, 0, 0, $w, $h, $whiteOL);

        for ($y = 0; $y < $h; $y++) {
            for ($x = 0; $x < $w; $x++) {
                $c = imagecolorsforindex($rot, imagecolorat($rot, $x, $y));
                if ($c['red'] == 0) {
                    imagesetpixel($overlay, $x, $y, imagecolorallocate($overlay, 0, 0, 0));
                } elseif ($c['red'] == 128) {
                    imagesetpixel($overlay, $x, $y, imagecolorallocate($overlay, 100, 100, 100));
                } else {
                    imagesetpixel($overlay, $x, $y, $whiteOL);
                }
            }
        }

        $red = imagecolorallocate($overlay, 255, 0, 0);
        $blue = imagecolorallocate($overlay, 0, 0, 255);
        $green = imagecolorallocate($overlay, 0, 200, 0);
        $orange = imagecolorallocate($overlay, 255, 140, 0);
        $blackC = imagecolorallocate($overlay, 0, 0, 0);

        $drawDashed = function ($img, $x1, $y1, $x2, $y2, $col) {
            $dx = $x2 - $x1;
            $dy = $y2 - $y1;
            $len = max(1, (int)hypot($dx, $dy));
            for ($i = 0; $i <= $len; $i += 6) {
                $t1 = $i / $len;
                $t2 = min(($i + 3) / $len, 1);
                $xa = (int)round($x1 + $t1 * $dx);
                $ya = (int)round($y1 + $t1 * $dy);
                $xb = (int)round($x1 + $t2 * $dx);
                $yb = (int)round($y1 + $t2 * $dy);
                imageline($img, $xa, $ya, $xb, $yb, $col);
            }
        };

        $drawDashed($overlay, $S['x'], $S['y'], $T['x'], $T['y'], $orange);
        $drawDashed($overlay, $S['x'], $S['y'], $F['x'], $F['y'], $orange);

        imagefilledellipse($overlay, $S['x'], $S['y'], 8, 8, $green);
        imagefilledellipse($overlay, $T['x'], $T['y'], 8, 8, $red);
        imagefilledellipse($overlay, $F['x'], $F['y'], 8, 8, $blue);

        imagestring($overlay, 3, $T['x'] + 5, $T['y'] - 12, 'T', $blackC);
        imagestring($overlay, 3, $S['x'] + 5, $S['y'] - 12, 'S', $blackC);
        imagestring($overlay, 3, $F['x'] + 5, $F['y'] - 12, 'F', $blackC);
        imagestring($overlay, 4, $S['x'] + 10, $S['y'] + 10, sprintf("Clarke: %.2f°", $clarkeAngle), $blackC);

        $processedFilename = 'processed_' . $timestamp . '.jpg';
        $processedPath = 'uploads/processed/' . $processedFilename;
        imagejpeg($overlay, Storage::disk('public')->path($processedPath), 90);
        $processedImages['step_14_visualization'] = $processedPath;

        // ===== 15) Create segmented image =====
        $segmented = imagecreatetruecolor($w, $h);
        $whiteS = imagecolorallocate($segmented, 255, 255, 255);
        imagefilledrectangle($segmented, 0, 0, $w, $h, $whiteS);

        $cHeel = imagecolorallocate($segmented, 255, 0, 0);
        $cArch = imagecolorallocate($segmented, 0, 255, 0);
        $cToe = imagecolorallocate($segmented, 0, 0, 255);
        $cArchTone = imagecolorallocate($segmented, 255, 255, 0);

        $regionH = (int)(($bot - $top) / 3);
        $heelTop = $bot - $regionH;
        $archTop = $top + $regionH;
        $archBottom = $bot - $regionH;

        for ($y = $top; $y <= $bot; $y++) {
            for ($x = $left; $x <= $right; $x++) {
                $c = imagecolorsforindex($rot, imagecolorat($rot, $x, $y));

                if ($c['red'] <= 128) {
                    if ($c['red'] == 128) {
                        imagesetpixel($segmented, $x, $y, $cArchTone);
                    } else {
                        if ($y < $archTop) {
                            imagesetpixel($segmented, $x, $y, $cToe);
                        } elseif ($y <= $archBottom) {
                            imagesetpixel($segmented, $x, $y, $cArch);
                        } else {
                            imagesetpixel($segmented, $x, $y, $cHeel);
                        }
                    }
                }
            }
        }

        $segFilename = 'segmented_' . $timestamp . '.jpg';
        $segPath = 'uploads/processed/' . $segFilename;
        imagejpeg($segmented, Storage::disk('public')->path($segPath), 90);
        $processedImages['step_15_segmented'] = $segPath;

        // ===== 16) Cleanup =====
        imagedestroy($im);
        imagedestroy($bin);
        imagedestroy($archMap);
        imagedestroy($tmp);
        imagedestroy($rot);
        imagedestroy($overlay);
        imagedestroy($segmented);

        return [
            'message' => 'Citra berhasil diproses dengan FAST dan analisis tone!',
            'original_path' => $originalPath,
            'processed_image' => $processedPath,
            'segmented_image' => $segPath,
            'all_process_images' => $processedImages,
            'panjang_telapak_kaki' => $footLength,
            'lebar_telapak_kaki' => $footWidth,
            'clarke_angle' => round($clarkeAngle, 2),
            'arch_type' => $archType,
            'keypoints' => $keypoints,
            'debug_points' => $points,
            'debug_boundaries' => compact('top', 'bot', 'left', 'right'),
            'thresholds' => [
                'main' => $mainThreshold,
                'arch_low' => $archThresholdLow,
                'arch_high' => $archThresholdHigh
            ],
            'arch_analysis' => $archRegions
        ];
    }

    // ===== FAST Feature Detection =====
    private function detectFAST($image, $threshold = 20)
    {
        $w = imagesx($image);
        $h = imagesy($image);
        $keypoints = [];

        for ($y = 1; $y < $h - 1; $y++) {
            for ($x = 1; $x < $w - 1; $x++) {
                $center = imagecolorsforindex($image, imagecolorat($image, $x, $y))['red'];
                $isKeypoint = true;

                for ($dy = -1; $dy <= 1; $dy++) {
                    for ($dx = -1; $dx <= 1; $dx++) {
                        if ($dx == 0 && $dy == 0) continue;

                        $neighbor = imagecolorsforindex($image, imagecolorat($image, $x + $dx, $y + $dy))['red'];
                        if (abs($center - $neighbor) < $threshold) {
                            $isKeypoint = false;
                            break;
                        }
                    }
                    if (!$isKeypoint) break;
                }

                if ($isKeypoint) {
                    $keypoints[] = ['x' => $x, 'y' => $y];
                }
            }
        }

        return $keypoints;
    }

    public function destroy($id)
    {
        try {
            // Proses penghapusan data
            $pemeriksaan = Pemeriksaan::findOrFail($id);
            $pemeriksaan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Data berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus data: ' . $e->getMessage()
            ], 500);
        }
    }
}
