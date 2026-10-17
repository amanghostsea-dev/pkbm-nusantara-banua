<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'jenjang', // 'paket_a', 'paket_b', 'paket_c'
        'nama_program',
        'kode_program',
        'total_semester',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'total_semester' => 'integer',
    ];

    public function angkatans()
    {
        return $this->hasMany(Angkatan::class);
    }

    public function mataPelajarans()
    {
        return $this->hasMany(MataPelajaran::class);
    }

    public function sertifikats()
    {
        return $this->hasMany(Sertifikat::class);
    }
}
