<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    use HasFactory;

    protected $table = 'materis';

    protected $fillable = [
        'kelas_id',
        'mata_pelajaran_id',
        'tutor_id',
        'judul',
        'deskripsi',
        'tipe', // 'pdf', 'video', 'artikel', 'dokumen'
        'file_path',
        'link_url',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
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
}
