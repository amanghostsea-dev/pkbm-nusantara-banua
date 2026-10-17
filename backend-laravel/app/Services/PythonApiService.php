<?php

namespace App\Services;

use App\Models\Siswa;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Service untuk komunikasi dengan Python FastAPI Microservice
 * Handles: Raport PDF, Sertifikat, Prediksi ML, Analytics
 */
class PythonApiService
{
    private string $baseUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.python_api.url', 'http://localhost:8000');
        $this->apiKey  = config('services.python_api.key', '');
    }

    /**
     * HTTP client dengan default headers
     */
    private function client()
    {
        return Http::baseUrl($this->baseUrl)
            ->withHeaders([
                'X-API-Key'    => $this->apiKey,
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ])
            ->timeout(120); // 2 menit untuk operasi PDF
    }

    /**
     * Generate Raport PDF untuk siswa
     */
    public function generateRaport(Siswa $siswa, int $semester, int $kelasId): array
    {
        try {
            // Kumpulkan data raport dari database
            $siswa->load(['angkatan.program', 'nilais.mataPelajaran', 'absensi']);
            
            $nilaiList = $siswa->nilais()
                ->where('kelas_id', $kelasId)
                ->where('semester', $semester)
                ->with('mataPelajaran')
                ->get()
                ->map(fn($n) => [
                    'nama_mapel'     => $n->mataPelajaran->nama_mapel,
                    'kode_mapel'     => $n->mataPelajaran->kode_mapel,
                    'nilai_tugas'    => $n->nilai_tugas,
                    'nilai_uts'      => $n->nilai_uts,
                    'nilai_uas'      => $n->nilai_uas,
                    'nilai_praktek'  => $n->nilai_praktek,
                    'nilai_akhir'    => $n->nilai_akhir,
                    'kkm'            => $n->mataPelajaran->kkm,
                    'predikat'       => $n->predikat,
                    'lulus'          => $n->lulus,
                ]);

            // Hitung absensi
            $absensi = $siswa->absensi()
                ->whereHas('jadwal', fn($q) => $q->where('kelas_id', $kelasId))
                ->selectRaw('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "hadir" THEN 1 ELSE 0 END) as hadir,
                    SUM(CASE WHEN status = "sakit" THEN 1 ELSE 0 END) as sakit,
                    SUM(CASE WHEN status = "izin" THEN 1 ELSE 0 END) as izin,
                    SUM(CASE WHEN status = "alpha" THEN 1 ELSE 0 END) as alpha
                ')
                ->first();

            $totalAbsensi = $absensi->total ?: 1;
            
            $payload = [
                'siswa_id'          => $siswa->id,
                'nama_siswa'        => $siswa->nama_lengkap,
                'nisn'              => $siswa->nisn,
                'tempat_lahir'      => $siswa->tempat_lahir,
                'tanggal_lahir'     => $siswa->tanggal_lahir->format('Y-m-d'),
                'jenis_kelamin'     => $siswa->jenis_kelamin,
                'nama_orang_tua'    => $siswa->nama_orang_tua,
                'program'           => $siswa->angkatan->program->jenjang,
                'angkatan'          => $siswa->angkatan->nama_angkatan,
                'kelas'             => 'Kelas ' . $semester,
                'semester'          => $semester,
                'tahun_ajaran'      => $siswa->angkatan->tahun_ajaran,
                'wali_kelas'        => 'Nama Wali Kelas', // TODO: dari kelas
                'kepala_pkbm'       => config('pkbm.kepala_pkbm', 'Kepala PKBM'),
                'nama_pkbm'         => config('pkbm.nama', 'PKBM'),
                'alamat_pkbm'       => config('pkbm.alamat', ''),
                'npsn'              => config('pkbm.npsn', ''),
                'nilai_list'        => $nilaiList->toArray(),
                'absensi' => [
                    'total_hadir'       => $absensi->hadir ?? 0,
                    'total_sakit'       => $absensi->sakit ?? 0,
                    'total_izin'        => $absensi->izin ?? 0,
                    'total_alpha'       => $absensi->alpha ?? 0,
                    'persentase_hadir'  => round(($absensi->hadir ?? 0) / $totalAbsensi * 100, 2),
                ],
                'rata_rata'         => $siswa->getRataRataNilai(),
                'peringkat'         => 1, // TODO: hitung peringkat
                'status_naik'       => 'naik', // TODO: tentukan berdasarkan nilai
            ];

            $response = $this->client()
                ->post('/api/v1/raport/generate', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Python API raport error', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return [
                'success' => false,
                'message' => 'Python API error: ' . $response->status(),
            ];

        } catch (\Exception $e) {
            Log::error('PythonApiService::generateRaport error', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Prediksi kelulusan siswa menggunakan ML
     */
    public function prediksiKelulusan(Siswa $siswa): array
    {
        try {
            $payload = [
                'siswa_id'                      => $siswa->id,
                'rata_rata_nilai'               => $siswa->getRataRataNilai(),
                'persentase_kehadiran'          => $siswa->getPersentaseKehadiran(),
                'jumlah_semester_sudah_ditempuh' => 1, // TODO: dari kelas
                'status_pembayaran_lancar'      => $siswa->isPembayaranLancar(),
                'usia'                          => $siswa->usia,
                'jenis_kelamin'                 => $siswa->jenis_kelamin,
            ];

            $response = $this->client()
                ->post('/api/v1/prediksi/kelulusan', $payload);

            if ($response->successful()) {
                return $response->json();
            }

            return ['error' => 'Prediksi gagal'];

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Ambil data analitik dashboard
     */
    public function getDashboardAnalitik(?string $tahunAjaran = null): array
    {
        try {
            $response = $this->client()
                ->get('/api/v1/analitik/dashboard/ringkasan', [
                    'tahun_ajaran' => $tahunAjaran,
                ]);

            return $response->successful() ? $response->json() : [];

        } catch (\Exception $e) {
            Log::warning('Analytics API unavailable: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate QR Code untuk absensi
     */
    public function generateQrAbsensi(int $jadwalId, string $tanggal, string $kelas = 'Kelas Mandiri', string $mapel = '', int $expiredMinutes = 30): array
    {
        try {
            $response = $this->client()
                ->post('/api/v1/qrcode/absensi/generate', [
                    'jadwal_id'       => $jadwalId,
                    'tanggal'         => $tanggal,
                    'kelas'           => $kelas,
                    'mata_pelajaran'  => $mapel,
                    'expired_minutes' => $expiredMinutes,
                ]);

            return $response->successful() ? $response->json() : ['error' => 'QR gagal'];

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Verifikasi Biometrik Wajah untuk Presensi
     */
    public function verifyFaceAttendance(int $jadwalId, string $imageBase64, ?int $siswaId = null, ?string $nisn = null): array
    {
        try {
            $response = $this->client()
                ->post('/api/v1/face/verify', [
                    'jadwal_id'    => $jadwalId,
                    'siswa_id'     => $siswaId,
                    'nisn'         => $nisn,
                    'image_base64' => $imageBase64,
                ]);

            return $response->successful() ? $response->json() : ['success' => false, 'message' => 'Verifikasi wajah gagal'];

        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Generate laporan dinas resmi (Excel / PDF)
     */
    public function generateLaporanDinas(string $jenis, int $tahun, ?string $program = null, string $format = 'excel'): array
    {
        try {
            $payload = [
                'jenis'   => $jenis,
                'tahun'   => $tahun,
                'program' => $program ?: 'semua',
                'format'  => $format,
            ];

            $response = $this->client()->post('/api/v1/laporan/generate', $payload);
            return $response->successful() ? $response->json() : ['success' => false, 'message' => 'Gagal generate laporan'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Generate Sertifikat Kelulusan PDF
     */
    public function generateSertifikat(\App\Models\Sertifikat $sertifikat): array
    {
        try {
            $sertifikat->load(['siswa.angkatan.program', 'program']);
            $siswa = $sertifikat->siswa;
            $program = $sertifikat->program ?: $siswa->angkatan?->program;

            $payload = [
                'siswa_id'         => $siswa->id,
                'program_id'       => $program?->id ?? 1,
                'nama_siswa'       => $siswa->nama_lengkap,
                'nisn'             => $siswa->nisn,
                'tempat_lahir'     => $siswa->tempat_lahir,
                'tanggal_lahir'    => $siswa->tanggal_lahir?->format('Y-m-d') ?? '2000-01-01',
                'program'          => $program?->jenjang ?? 'paket_c',
                'nomor_sertifikat' => $sertifikat->nomor_sertifikat,
                'tanggal_terbit'   => $sertifikat->tanggal_terbit?->format('Y-m-d') ?? now()->toDateString(),
                'tahun_lulus'      => (string) ($sertifikat->tanggal_terbit?->year ?? now()->year),
                'kepala_pkbm'      => \App\Models\Setting::get('kepala_pkbm', 'Drs. H. Mulyadi, M.Pd'),
                'nama_pkbm'        => \App\Models\Setting::get('nama_pkbm', 'PKBM Harapan Bangsa'),
                'npsn'             => \App\Models\Setting::get('npsn', 'P9901234'),
                'nilai_akhir'      => (float) ($siswa->getRataRataNilai() ?: 85.0),
                'predikat'         => 'Amat Baik (A)',
            ];

            $response = $this->client()->post('/api/v1/sertifikat/generate', $payload);

            if ($response->successful()) {
                $data = $response->json();
                $sertifikat->update([
                    'file_pdf' => $data['download_url'] ?? null,
                    'status'   => 'terbit',
                ]);
                return $data;
            }

            return ['success' => false, 'message' => 'Gagal membuat sertifikat'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Download Raport PDF
     */
    public function downloadRaport(string $filename)
    {
        try {
            $response = $this->client()->get("/api/v1/raport/download/{$filename}");
            if ($response->successful()) {
                return response($response->body(), 200, [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => "inline; filename=\"{$filename}\"",
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Download Raport error: ' . $e->getMessage());
        }

        abort(404, 'File raport tidak ditemukan di server.');
    }

    /**
     * Download Sertifikat PDF
     */
    public function downloadSertifikat(string $filename)
    {
        try {
            $response = $this->client()->get("/api/v1/sertifikat/download/{$filename}");
            if ($response->successful()) {
                return response($response->body(), 200, [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => "inline; filename=\"{$filename}\"",
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Download Sertifikat error: ' . $e->getMessage());
        }

        abort(404, 'File sertifikat tidak ditemukan di server.');
    }

    /**
     * Health check Python API
     */
    public function isAvailable(): bool
    {
        try {
            $response = $this->client()
                ->timeout(5)
                ->get('/health');
            return $response->successful();
        } catch (\Exception) {
            return false;
        }
    }
}
