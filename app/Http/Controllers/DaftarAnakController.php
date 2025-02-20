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
    public function create()
    {
        return view('backend.pages.daftar-anak.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|unique:anak,nik',
            'nama_anak' => 'required',
            'tempat_lahir' => 'required',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required',
            'nama_ibu' => 'required'
        ]);

        Anak::create($request->all());

        return redirect()->route('anak.create')->with('success', 'Data anak berhasil ditambahkan.');
    }
}
