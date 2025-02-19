<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Anak extends Model
{
    use HasFactory;

    protected $table = 'anak';
    protected $fillable = [
        'nik', 'nama_anak', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'golongan_darah', 'nama_ibu'
    ];

    // Relasi ke Pemeriksaan
    public function pemeriksaans()
    {
        return $this->hasMany(Pemeriksaan::class, 'id_anak');
    }
}
// Compare this snippet from config/database.php: