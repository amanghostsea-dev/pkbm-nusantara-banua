<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nilai extends Model
{
    use HasFactory;

    protected $table = 'nilais';

    protected $fillable = [
        'siswa_id',
        'mata_pelajaran_id',
        'kelas_id',
        'semester',
        'nilai_tugas',
        'nilai_uts',
        'nilai_uas',
        'nilai_praktek',
        'predikat',
        'lulus',
        'catatan',
    ];

    protected $casts = [
        'nilai_tugas' => 'float',
        'nilai_uts' => 'float',
        'nilai_uas' => 'float',
        'nilai_praktek' => 'float',
        'nilai_akhir' => 'float',
        'lulus' => 'boolean',
        'semester' => 'integer',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
