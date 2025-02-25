<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CitraTelapakKaki extends Model
{
    use HasFactory;

    protected $table = 'citra_telapak_kaki';

    protected $fillable = [
        'id_pemeriksaan',
        'path_citra',
        'panjang_telapak_kaki',
        'lebar_telapak_kaki',
        'clarke_angle',
    ];

    public function pemeriksaan()
    {
        return $this->belongsTo(Pemeriksaan::class, 'id_pemeriksaan');
    }
}
