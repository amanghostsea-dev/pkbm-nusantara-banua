<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Raport extends Model
{
    use HasFactory;

    protected $table = 'raports';

    protected $fillable = [
        'siswa_id',
        'kelas_id',
        'semester',
        'tahun_ajaran',
        'rata_rata',
        'peringkat',
        'total_hadir',
        'total_sakit',
        'total_izin',
        'total_alpha',
        'status_naik', // 'naik', 'tinggal', 'lulus'
        'file_pdf',
        'catatan_wali_kelas',
        'generated_at',
    ];

    protected $casts = [
        'rata_rata' => 'float',
        'generated_at' => 'datetime',
        'semester' => 'integer',
        'peringkat' => 'integer',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }
}
