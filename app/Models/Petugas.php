<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    use HasFactory;

    protected $table = 'petugas';
    protected $fillable = [
        'username',
        'password',
        'nama_petugas',
        'no_hp',
        'alamat'
    ];

    // Relasi ke Pemeriksaan
    public function pemeriksaans()
    {
        return $this->hasMany(Pemeriksaan::class, 'id_petugas');
    }
}
