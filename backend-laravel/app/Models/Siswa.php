<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Siswa
 * 
 * @property int $id
 * @property int $angkatan_id
 * @property string $nisn
 * @property string $nama_lengkap
 * @property string $jenis_kelamin
 * @property string $status
 */
class Siswa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'siswa';

    protected $fillable = [
        'user_id',
        'angkatan_id',
        'nisn',
        'nik',
        'nama_lengkap',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'rt_rw',
        'kelurahan',
        'kecamatan',
        'kota',
        'provinsi',
        'kode_pos',
        'no_telepon',
        'nama_orang_tua',
        'pekerjaan_orang_tua',
        'telepon_orang_tua',
        'status',
        'foto',
        'ijazah_terakhir',
        'catatan',
    ];

    protected $casts = [
        'tanggal_lahir' => 'date',
    ];

    protected $appends = ['foto_url', 'usia', 'nama_program'];

    // ==========================
    // Relationships
    // ==========================

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function angkatan(): BelongsTo
    {
        return $this->belongsTo(Angkatan::class);
    }

    public function kelas(): HasMany
    {
        return $this->hasMany(KelasSiswa::class);
    }

    public function kelasAktif()
    {
        return $this->hasOneThrough(Kelas::class, KelasSiswa::class, 'siswa_id', 'id', 'id', 'kelas_id')
            ->where('kelas_siswa.status', 'aktif');
    }

    public function nilais(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }

    public function absensi(): HasMany
    {
        return $this->hasMany(Absensi::class);
    }

    public function raports(): HasMany
    {
        return $this->hasMany(Raport::class);
    }

    public function sertifikats(): HasMany
    {
        return $this->hasMany(Sertifikat::class);
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    // ==========================
    // Accessors
    // ==========================

    public function getFotoUrlAttribute(): ?string
    {
        if ($this->foto) {
            return asset('storage/' . $this->foto);
        }
        return asset('images/default-avatar.png');
    }

    public function getUsiaAttribute(): int
    {
        return $this->tanggal_lahir?->age ?? 0;
    }

    public function getNamaProgramAttribute(): string
    {
        return $this->angkatan?->program?->nama_program ?? '';
    }

    // ==========================
    // Scopes
    // ==========================

    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }

    public function scopeByProgram($query, string $jenjang)
    {
        return $query->whereHas('angkatan.program', fn($q) => $q->where('jenjang', $jenjang));
    }

    public function scopeByAngkatan($query, int $angkatanId)
    {
        return $query->where('angkatan_id', $angkatanId);
    }

    // ==========================
    // Helper Methods
    // ==========================

    public function getRataRataNilai(): float
    {
        $rata = $this->nilais()
            ->latest('id')
            ->avg('nilai_akhir');
        return round($rata ?? 0, 2);
    }

    public function getPersentaseKehadiran(): float
    {
        $total = $this->absensi()->count();
        if ($total === 0) return 0;
        
        $hadir = $this->absensi()->where('status', 'hadir')->count();
        return round(($hadir / $total) * 100, 2);
    }

    public function isPembayaranLancar(): bool
    {
        $tunggakan = $this->pembayarans()
            ->where('status', '!=', 'lunas')
            ->count();
        return $tunggakan === 0;
    }
}
