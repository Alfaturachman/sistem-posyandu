<?php

namespace App\Http\Controllers;

use App\Models\Anak;
use App\Models\Pemeriksaan;
use Illuminate\Http\Request;

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


    public function periksa()
    {
        return view('backend.pages.periksa-anak.periksa');
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_anak' => 'required|exists:anak,id',
            'tanggal_periksa' => 'required|date',
            'berat_badan' => 'required|numeric|min:0',
            'tinggi_badan' => 'required|numeric|min:0',
            'lingkar_lengan' => 'required|numeric|min:0',
            'lingkar_kepala' => 'required|numeric|min:0',
            'citra_telapak_kaki' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'id_petugas' => 'required|exists:petugas,id',
        ]);

        $data = $request->all();

        if ($request->hasFile('citra_telapak_kaki')) {
            $file = $request->file('citra_telapak_kaki');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/telapak_kaki', $filename, 'public');
            $data['citra_telapak_kaki'] = $path;
        }

        Pemeriksaan::create($data);

        return redirect()->route('periksa-anak')->with('success', 'Data pemeriksaan berhasil ditambahkan.');
    }
}
