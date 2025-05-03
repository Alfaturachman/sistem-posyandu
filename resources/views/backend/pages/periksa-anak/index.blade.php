@extends('backend.layouts.app')

@section('title', 'Hasil Periksa')

@section('content')

<div class="container-fluid">
    <div class="row pt-2">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h4 class="fw-bolder mb-3">Hasil Periksa Anak</h4>
                        <a class="btn btn-primary mb-3" href="{{ route('periksa') }}">
                            Tambah Periksa Anak</i>
                        </a>
                        <div id="alert" class="alert alert-primary" role="alert">
                            Di bawah ini hasil periksa anak yang telah dilakukan.
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table id="dataTables" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-start">NIK</th>
                                    <th>Nama Anak</th>
                                    <th>Tanggal Lahir</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($anakList as $anak)
                                <tr>
                                    <td class="text-start">{{ $anak->nik }}</td>
                                    <td>{{ $anak->nama_anak }}</td>
                                    <td>{{ \Carbon\Carbon::parse($anak->tanggal_lahir)->translatedFormat('d F Y') }}</td>
                                    <td>{{ $anak->jenis_kelamin }}</td>
                                    <td>
                                        <a href="{{ route('anak.detail', $anak->id) }}" class="btn btn-primary btn-sm">
                                            <i class="ti ti-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection