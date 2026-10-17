<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sertifikat extends Model
{
    use HasFactory;

    protected $table = 'sertifikats';

    protected $fillable = [
        'siswa_id',
        'program_id',
        'nomor_sertifikat',
        'tanggal_terbit',
        'file_pdf',
        'status', // 'draft', 'terbit', 'revisi'
    ];

    protected $casts = [
        'tanggal_terbit' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }
}
