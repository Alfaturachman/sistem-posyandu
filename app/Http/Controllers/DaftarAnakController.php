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
            'nik'           => 'nullable|unique:anak,nik',
            'nama_anak'     => 'required',
            'tempat_lahir'  => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'jenis_kelamin' => 'nullable|string',
            'nama_ibu'      => 'nullable|string'
        ]);

        Anak::create($request->only([
            'nik',
            'nama_anak',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'nama_ibu'
        ]));

        return redirect()->route('daftar-anak')->with('success', 'Data anak berhasil ditambahkan.');
    }
}
