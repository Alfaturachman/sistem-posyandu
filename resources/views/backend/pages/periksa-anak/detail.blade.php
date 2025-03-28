@extends('backend.layouts.app')

@section('title', 'Detail Periksa')

@section('content')

<div class="container-fluid">
    <div class="row pt-2">
        <div class="col d-flex align-items-stretch">
            <div class="card w-100">
                <div class="card-body">
                    <div class="d-block align-items-center justify-content-center mb-4">
                        <h4 class="fw-bolder mb-3">Detail Hasil Periksa Anak</h4>
                        <div id="alert" class="alert alert-success" role="alert">
                            Silahkan untuk periksa data anak dengan mengisi form di bawah ini.
                        </div>
                    </div>
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5>Detail Identitas Anak</h5>
                        <a class="btn btn-warning" href="{{ route('edit-identitas', $anak->id) }}">
                            <i class="ti ti-edit"></i> Edit Identitas
                        </a>                        
                    </div>
                    <div class="table-responsive mb-3">
                        <table class="table">
                            <tr>
                                <th>NIK</th>
                                <td>{{ $anak->nik }}</td>
                            </tr>
                            <tr>
                                <th>Nama</th>
                                <td>{{ $anak->nama_anak }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Lahir</th>
                                <td>{{ \Carbon\Carbon::parse($anak->tanggal_lahir)->translatedFormat('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>Jenis Kelamin</th>
                                <td>{{ $anak->jenis_kelamin }}</td>
                            </tr>
                            <tr>
                                <th>Nama Orang Tua/Wali</th>
                                <td>{{ $anak->nama_ibu }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="d-block align-items-center justify-content-center mb-3">
                        <h5>Riwayat Pemeriksaan Anak</h5>
                    </div>
                    <div class="table-responsive">
                        @if($anak->pemeriksaans->isEmpty())
                            <div class="alert alert-warning text-center" role="alert">
                                Belum ada data pemeriksaan untuk ananda {{ $anak->nama_anak }}.
                            </div>
                        @else
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Tanggal Periksa</th>
                                        <th>Berat Badan (kg)</th>
                                        <th>Tinggi Badan (cm)</th>
                                        <th>Lingkar Lengan (cm)</th>
                                        <th>Lingkar Kepala (cm)</th>
                                        <th>Citra Kaki</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($anak->pemeriksaans as $periksa)
                                    <tr>
                                        <td>{{ date('d-m-Y', strtotime($periksa->tanggal_periksa)) }}</td>
                                        <td>{{ $periksa->berat_badan }}</td>
                                        <td>{{ $periksa->tinggi_badan }}</td>
                                        <td>{{ $periksa->lingkar_lengan }}</td>
                                        <td>{{ $periksa->lingkar_kepala }}</td>
                                        <td>
                                            @if(optional($periksa->citraTelapakKaki)->path_citra)
                                                <img src="{{ Storage::url($periksa->citraTelapakKaki->path_citra) }}"
                                                     alt="Citra Telapak Kaki"
                                                     class="img-fluid"
                                                     width="300">
                                            @else
                                                <p>Tidak ada gambar</p>
                                            @endif
                                        </td>                                        
                                    </tr>
                                    @endforeach
                                </tbody>                                
                            </table>
                        @endif
                    </div>                    
                    <a href="{{ route('hasil') }}" class="btn btn-secondary mt-3">Kembali</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection