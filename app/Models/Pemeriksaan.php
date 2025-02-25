<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemeriksaan extends Model
{
    use HasFactory;

    protected $table = 'pemeriksaan';
    protected $fillable = [
        'id_anak',
        'tanggal_periksa',
        'berat_badan',
        'tinggi_badan',
        'lingkar_lengan',
        'lingkar_kepala',
        'citra_telapak_kaki',
        'id_petugas'
    ];

    // Relasi ke Anak
    public function anak()
    {
        return $this->belongsTo(Anak::class, 'id_anak');
    }

    // Relasi ke Petugas
    public function petugas()
    {
        return $this->belongsTo(Petugas::class, 'id_petugas');
    }
}
// Compare this snippet from app/Models/Pemeriksaan.php: