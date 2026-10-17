<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    use HasFactory;

    protected $table = 'kelas';

    protected $fillable = [
        'angkatan_id',
        'tutor_id',
        'nama_kelas',
        'semester',
        'kapasitas',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'semester' => 'integer',
        'kapasitas' => 'integer',
    ];

    public function angkatan()
    {
        return $this->belongsTo(Angkatan::class);
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    public function kelasSiswa()
    {
        return $this->hasMany(KelasSiswa::class);
    }

    public function siswas()
    {
        return $this->belongsToMany(Siswa::class, 'kelas_siswa')
            ->withPivot('status', 'tanggal_masuk', 'tanggal_keluar')
            ->withTimestamps();
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function raports()
    {
        return $this->hasMany(Raport::class);
    }
}
