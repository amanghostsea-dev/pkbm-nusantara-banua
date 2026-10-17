<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayarans';

    protected $fillable = [
        'siswa_id',
        'bulan',
        'tahun',
        'nominal',
        'jenis', // 'spp', 'seragam', 'buku', 'lainnya'
        'status', // 'lunas', 'belum', 'cicil'
        'tanggal_bayar',
        'bukti_bayar',
        'keterangan',
        'diterima_oleh',
    ];

    protected $casts = [
        'nominal' => 'float',
        'tanggal_bayar' => 'date',
    ];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'diterima_oleh');
    }
}
