<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $table = 'mata_pelajarans';

    protected $fillable = [
        'program_id',
        'kode_mapel',
        'nama_mapel',
        'semester',
        'kkm',
        'jam_per_minggu',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'semester' => 'integer',
        'kkm' => 'integer',
        'jam_per_minggu' => 'integer',
    ];

    public function program()
    {
        return $this->belongsTo(Program::class);
    }

    public function jadwals()
    {
        return $this->hasMany(Jadwal::class);
    }

    public function nilais()
    {
        return $this->hasMany(Nilai::class);
    }
}
