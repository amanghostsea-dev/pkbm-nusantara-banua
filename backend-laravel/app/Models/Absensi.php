<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;

    protected $table = 'absensi';

    protected $fillable = [
        'jadwal_id',
        'siswa_id',
        'tanggal',
        'status', // 'hadir', 'sakit', 'izin', 'alpha'
        'keterangan',
        'qr_token',
        'waktu_absen',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'waktu_absen' => 'datetime',
    ];

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
