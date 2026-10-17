<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TugasPengumpulan extends Model
{
    use HasFactory;

    protected $table = 'tugas_pengumpulans';

    protected $fillable = [
        'tugas_id',
        'siswa_id',
        'file_tugas',
        'catatan_siswa',
        'tanggal_kumpul',
        'nilai',
        'feedback',
        'status', // 'dikirim', 'dinilai', 'terlambat', 'revisi'
        'dinilai_at',
    ];

    protected $casts = [
        'tanggal_kumpul' => 'datetime',
        'dinilai_at' => 'datetime',
        'nilai' => 'integer',
    ];

    public function tugas()
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }
}
