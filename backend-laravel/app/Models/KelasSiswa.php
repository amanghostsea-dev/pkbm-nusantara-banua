<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class KelasSiswa extends Pivot
{
    protected $table = 'kelas_siswa';

    protected $fillable = [
        'kelas_id',
        'siswa_id',
        'status', // 'aktif', 'pindah', 'keluar'
        'tanggal_masuk',
        'tanggal_keluar',
    ];

    protected $casts = [
        'tanggal_masuk' => 'date',
        'tanggal_keluar' => 'date',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }
}
