<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    use HasFactory;

    protected $table = 'jadwals';

    protected $fillable = [
        'kelas_id',
        'mata_pelajaran_id',
        'tutor_id',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'ruangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    public function absensis()
    {
        return $this->hasMany(Absensi::class);
    }
}
