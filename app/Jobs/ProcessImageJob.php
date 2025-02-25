<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class ProcessImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $imagePath;

    /**
     * Create a new job instance.
     */
    public function __construct($imagePath)
    {
        $this->imagePath = $imagePath;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $image = Image::make(storage_path('app/' . $this->imagePath));

        // 1. Ubah ke grayscale
        $image->greyscale();

        // 2. Binarization (Thresholding)
        $image->contrast(100);
        $threshold = 128;
        $image->brightness(- ($threshold - 128));

        // 3. Rotasi gambar
        $image->rotate(90);

        // 4. Hitung ukuran gambar
        $width = $image->width();
        $height = $image->height();

        // 5. Split gambar menjadi 4 bagian
        $parts = [];
        $partWidth = $width / 2;
        $partHeight = $height / 2;
        for ($i = 0; $i < 2; $i++) {
            for ($j = 0; $j < 2; $j++) {
                $part = clone $image;
                $part->crop($partWidth, $partHeight, $i * $partWidth, $j * $partHeight);
                $partPath = 'processed_images/part_' . $i . '_' . $j . '.jpg';
                $part->save(storage_path('app/public/' . $partPath));
                $parts[] = $partPath;
            }
        }

        // 6. Transpose gambar
        $image->flip('horizontal');

        // 7. Hitung jumlah piksel hitam
        $blackPixels = 0;
        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $color = $image->pickColor($x, $y);
                if ($color[0] < 128) {
                    $blackPixels++;
                }
            }
        }

        // 8. Simpan hasil gambar
        $outputPath = 'processed_images/' . time() . '_processed.jpg';
        $image->save(storage_path('app/public/' . $outputPath));

        return [
            'processedImage' => $outputPath,
            'width' => $width,
            'height' => $height,
            'blackPixels' => $blackPixels,
            'parts' => $parts
        ];
    }
}
