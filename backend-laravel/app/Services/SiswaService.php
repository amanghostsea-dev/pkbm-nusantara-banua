<?php

namespace App\Services;

use App\Models\Siswa;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

/**
 * Service Layer untuk Manajemen Siswa
 */
class SiswaService
{
    /**
     * Ambil semua siswa dengan filter
     */
    public function getAll(array $filters): LengthAwarePaginator
    {
        $query = Siswa::with(['angkatan.program'])
            ->orderBy('nama_lengkap');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('nama_lengkap', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (!empty($filters['angkatan_id'])) {
            $query->where('angkatan_id', $filters['angkatan_id']);
        }

        if (!empty($filters['program'])) {
            $query->byProgram($filters['program']);
        }

        $perPage = min($filters['per_page'] ?? 20, 100);
        
        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Buat siswa baru
     */
    public function create(array $data, ?UploadedFile $foto = null): Siswa
    {
        if ($foto) {
            $data['foto'] = $foto->store('siswa/foto', 'public');
        }

        $siswa = Siswa::create($data);

        // Buat user account jika diminta
        if ($data['create_user_account'] ?? false) {
            $user = User::create([
                'name'     => $siswa->nama_lengkap,
                'email'    => strtolower(str_replace(' ', '.', $siswa->nama_lengkap)) . '@siswa.pkbm.sch.id',
                'password' => Hash::make($siswa->nisn),
                'role'     => 'siswa',
            ]);
            
            $siswa->update(['user_id' => $user->id]);
        }

        return $siswa;
    }

    /**
     * Update data siswa
     */
    public function update(Siswa $siswa, array $data, ?UploadedFile $foto = null): Siswa
    {
        if ($foto) {
            // Hapus foto lama
            if ($siswa->foto) {
                Storage::disk('public')->delete($siswa->foto);
            }
            $data['foto'] = $foto->store('siswa/foto', 'public');
        }

        $siswa->update($data);
        
        return $siswa->fresh();
    }

    /**
     * Hapus siswa (soft delete)
     */
    public function delete(Siswa $siswa): void
    {
        $siswa->delete();
    }

    /**
     * Statistik ringkasan siswa
     */
    public function getStats(): array
    {
        return [
            'total_aktif'   => Siswa::aktif()->count(),
            'total_lulus'   => Siswa::where('status', 'lulus')->count(),
            'total_dropout' => Siswa::where('status', 'dropout')->count(),
            'paket_a'       => Siswa::aktif()->byProgram('paket_a')->count(),
            'paket_b'       => Siswa::aktif()->byProgram('paket_b')->count(),
            'paket_c'       => Siswa::aktif()->byProgram('paket_c')->count(),
        ];
    }

    /**
     * Export data siswa ke Excel
     */
    public function export(array $filters)
    {
        // Gunakan Laravel Excel atau generate CSV
        $siswa = Siswa::with(['angkatan.program'])
            ->when($filters['status'] ?? null, fn($q, $s) => $q->where('status', $s))
            ->when($filters['program'] ?? null, fn($q, $p) => $q->byProgram($p))
            ->get();

        // Return response untuk download
        return response()->streamDownload(function () use ($siswa) {
            $csv = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($csv, ['No', 'NISN', 'Nama Lengkap', 'Program', 'Status', 'Tanggal Lahir', 'No. Telepon']);
            
            foreach ($siswa as $i => $s) {
                fputcsv($csv, [
                    $i + 1,
                    $s->nisn,
                    $s->nama_lengkap,
                    $s->angkatan?->program?->nama_program,
                    ucfirst($s->status),
                    $s->tanggal_lahir?->format('d/m/Y'),
                    $s->no_telepon,
                ]);
            }
            
            fclose($csv);
        }, 'data-siswa-' . date('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Download template CSV import siswa
     */
    public function downloadTemplate()
    {
        return response()->streamDownload(function () {
            $csv = fopen('php://output', 'w');
            fputcsv($csv, ['nisn', 'nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'no_telepon', 'nama_orang_tua', 'status']);
            fputcsv($csv, ['1234567890', 'Budi Pratama', '3171012345678901', 'Jakarta', '2005-08-17', 'L', 'Jl. Merdeka No. 12', '08123456789', 'Suryanto', 'aktif']);
            fputcsv($csv, ['1234567891', 'Siti Rahmawati', '3171012345678902', 'Bandung', '2006-03-21', 'P', 'Jl. Melati No. 5', '08129876543', 'Ahmad Sobari', 'aktif']);
            fclose($csv);
        }, 'template-import-siswa-pkbm.csv', [
            'Content-Type' => 'text/csv; charset=utf-8',
        ]);
    }

    /**
     * Import siswa dari Excel/CSV
     */
    public function import(UploadedFile $file, ?int $defaultAngkatanId = null): array
    {
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');
        if (!$handle) {
            return ['imported' => 0, 'errors' => ['File tidak dapat dibaca.']];
        }

        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = ',';
        if (strpos($firstLine, ';') !== false) {
            $delimiter = ';';
        } elseif (strpos($firstLine, "\t") !== false) {
            $delimiter = "\t";
        }

        $header = fgetcsv($handle, 1000, $delimiter);
        if (!$header) {
            fclose($handle);
            return ['imported' => 0, 'errors' => ['Format CSV tidak valid / kosong.']];
        }

        $cleanHeaders = array_map(function($h) {
            $h = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', (string)$h);
            return strtolower(trim($h));
        }, $header);

        $defaultAngkatan = $defaultAngkatanId 
            ? \App\Models\Angkatan::find($defaultAngkatanId) 
            : (\App\Models\Angkatan::where('status', 'aktif')->latest()->first() ?: \App\Models\Angkatan::first());

        $imported = 0;
        $errors = [];
        $rowNum = 1;

        while (($row = fgetcsv($handle, 1000, $delimiter)) !== false) {
            $rowNum++;
            if (empty(array_filter($row))) {
                continue;
            }

            $data = [];
            foreach ($cleanHeaders as $index => $colName) {
                $data[$colName] = isset($row[$index]) ? trim($row[$index]) : null;
            }

            $nisn = $data['nisn'] ?? null;
            $nama = $data['nama_lengkap'] ?? ($data['nama'] ?? null);

            if (!$nisn || !$nama) {
                $errors[] = "Baris {$rowNum}: NISN dan Nama Lengkap wajib diisi.";
                continue;
            }

            if (Siswa::where('nisn', $nisn)->exists()) {
                $errors[] = "Baris {$rowNum}: Siswa dengan NISN {$nisn} sudah terdaftar.";
                continue;
            }

            $user = User::firstOrCreate(
                ['email' => $nisn . '@siswa.pkbm.sch.id'],
                [
                    'name' => $nama,
                    'password' => Hash::make('password'),
                    'role' => 'siswa',
                ]
            );

            Siswa::create([
                'user_id' => $user->id,
                'angkatan_id' => !empty($data['angkatan_id']) ? (int)$data['angkatan_id'] : ($defaultAngkatan?->id ?? 1),
                'nisn' => $nisn,
                'nik' => $data['nik'] ?? null,
                'nama_lengkap' => $nama,
                'tempat_lahir' => $data['tempat_lahir'] ?? 'Jakarta',
                'tanggal_lahir' => !empty($data['tanggal_lahir']) ? date('Y-m-d', strtotime($data['tanggal_lahir'])) : '2005-01-01',
                'jenis_kelamin' => in_array(strtoupper($data['jenis_kelamin'] ?? ''), ['L', 'P']) ? strtoupper($data['jenis_kelamin']) : 'L',
                'alamat' => $data['alamat'] ?? 'Alamat warga belajar',
                'no_telepon' => $data['no_telepon'] ?? null,
                'nama_orang_tua' => !empty($data['nama_orang_tua']) ? $data['nama_orang_tua'] : 'Orang Tua / Wali',
                'status' => in_array(strtolower($data['status'] ?? ''), ['aktif', 'lulus', 'cuti', 'dropout']) ? strtolower($data['status']) : 'aktif',
            ]);

            $imported++;
        }

        fclose($handle);

        return [
            'imported' => $imported,
            'errors' => $errors,
        ];
    }

    /**
     * Bulk create siswa dari multi-row form
     */
    public function bulkStore(array $siswasList, ?int $angkatanId = null): int
    {
        $defaultAngkatan = $angkatanId 
            ? \App\Models\Angkatan::find($angkatanId) 
            : (\App\Models\Angkatan::where('status', 'aktif')->latest()->first() ?: \App\Models\Angkatan::first());

        $count = 0;

        foreach ($siswasList as $item) {
            if (empty($item['nisn']) || empty($item['nama_lengkap'])) {
                continue;
            }

            if (Siswa::where('nisn', $item['nisn'])->exists()) {
                continue;
            }

            $user = User::firstOrCreate(
                ['email' => $item['nisn'] . '@siswa.pkbm.sch.id'],
                [
                    'name' => $item['nama_lengkap'],
                    'password' => Hash::make('password'),
                    'role' => 'siswa',
                ]
            );

            Siswa::create([
                'user_id' => $user->id,
                'angkatan_id' => !empty($item['angkatan_id']) ? (int)$item['angkatan_id'] : ($defaultAngkatan?->id ?? 1),
                'nisn' => $item['nisn'],
                'nik' => $item['nik'] ?? null,
                'nama_lengkap' => $item['nama_lengkap'],
                'tempat_lahir' => $item['tempat_lahir'] ?? 'Jakarta',
                'tanggal_lahir' => $item['tanggal_lahir'] ?? '2005-01-01',
                'jenis_kelamin' => in_array($item['jenis_kelamin'] ?? '', ['L', 'P']) ? $item['jenis_kelamin'] : 'L',
                'alamat' => $item['alamat'] ?? 'Alamat warga belajar',
                'no_telepon' => $item['no_telepon'] ?? null,
                'nama_orang_tua' => !empty($item['nama_orang_tua']) ? $item['nama_orang_tua'] : 'Orang Tua / Wali',
                'status' => $item['status'] ?? 'aktif',
            ]);

            $count++;
        }

        return $count;
    }
}
