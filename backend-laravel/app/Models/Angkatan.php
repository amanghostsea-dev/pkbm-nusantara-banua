<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Angkatan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'program_id',
        'nama_angkatan',
        'tahun_ajaran',
        'tanggal_mulai',
        'tanggal_selesai',
        'status', // 'aktif', 'selesai', 'tutup'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class);
    }

    public function kelas()
    {
        return $this->hasMany(Kelas::class);
    }
}
