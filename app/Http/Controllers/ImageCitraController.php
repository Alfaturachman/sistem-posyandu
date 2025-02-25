<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessImageJob;

class ImageCitraController extends Controller
{
    public function processImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Simpan sementara gambar
        $path = $request->file('image')->store('uploads');

        // Dispatch job ke queue
        dispatch(new ProcessImageJob($path));

        return response()->json([
            'message' => 'Gambar sedang diproses di background.',
            'status' => 'Processing',
        ]);
    }
}
