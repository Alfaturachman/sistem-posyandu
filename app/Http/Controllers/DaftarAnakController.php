<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Anak;

class DaftarAnakController extends Controller
{
    public function index()
    {
        return view('backend.pages.daftar-anak.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|unique:anak,nik',
            'nama_anak' => 'required',
            'tempat_lahir' => 'nullable',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable',
            'nama_ibu' => 'nullable'
        ]);

        Anak::create($request->all());

        return redirect()->route('daftar-anak')->with('success', 'Data anak berhasil ditambahkan.');
    }
}
