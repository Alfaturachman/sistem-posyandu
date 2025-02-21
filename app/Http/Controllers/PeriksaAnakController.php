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

        $data = $request->only([
            'id_anak',
            'berat_badan',
            'tinggi_badan',
            'lingkar_lengan',
            'lingkar_kepala'
        ]);

        if ($request->hasFile('citra_telapak_kaki')) {
            $file = $request->file('citra_telapak_kaki');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('uploads/citra-telapak-kaki', $filename, 'public');
            $data['citra_telapak_kaki'] = $path;
        }

        Pemeriksaan::create($data);

        return redirect()->route('periksa')->with('success', 'Data pemeriksaan berhasil ditambahkan.');
    }
}
