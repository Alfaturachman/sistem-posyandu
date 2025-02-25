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

            // Ambil path lengkap dengan Storage
            $fullPath = Storage::disk('public')->path($originalPath);

            // Cek apakah file benar-benar ada
            if (!file_exists($fullPath)) {
                return response()->json(['error' => 'File tidak ditemukan!'], 404);
            }

            // Proses gambar dengan Intervention Image
            $manager = new ImageManager(new Driver());
            $image = $manager->read($fullPath);
            $image->greyscale();

            // Simpan hasil olahan dengan nama unik
            $processedFilename = 'processed_' . time() . '.jpg';
            $processedPath = 'uploads/processed/' . $processedFilename;

            // Simpan gambar ke storage
            Storage::disk('public')->put($processedPath, $image->toJpeg(80));

            // Simpan path gambar yang sudah diproses ke dalam database
            $data['citra_telapak_kaki'] = $processedPath;
        }

        // Simpan data pemeriksaan ke database
        $pemeriksaan = Pemeriksaan::create($data);

        return redirect()->route('periksa')->with('success', 'Data pemeriksaan berhasil ditambahkan.');
    }
}
