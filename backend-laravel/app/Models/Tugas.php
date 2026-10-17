<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tugas extends Model
{
    use HasFactory;

    protected $table = 'tugas';

    protected $fillable = [
        'kelas_id',
        'mata_pelajaran_id',
        'tutor_id',
        'judul',
        'deskripsi',
        'file_lampiran',
        'deadline',
        'poin_maksimal',
        'status', // 'aktif', 'selesai', 'draft'
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'poin_maksimal' => 'integer',
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

    public function pengumpulans()
    {
        return $this->hasMany(TugasPengumpulan::class, 'tugas_id');
    }
}
