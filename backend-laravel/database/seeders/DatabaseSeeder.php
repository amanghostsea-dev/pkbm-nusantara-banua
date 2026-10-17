<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Program;
use App\Models\Angkatan;
use App\Models\Tutor;
use App\Models\Siswa;
use App\Models\MataPelajaran;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ===========================
        // USERS
        // ===========================
        $admin = User::create([
            'name'     => 'Administrator PKBM',
            'email'    => 'admin@pkbm.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);
        
        $tutorUser = User::create([
            'name'     => 'Budi Santoso, S.Pd',
            'email'    => 'tutor@pkbm.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'tutor',
        ]);

        $siswaUser = User::create([
            'name'     => 'Ahmad Rizki',
            'email'    => 'siswa@pkbm.sch.id',
            'password' => Hash::make('password'),
            'role'     => 'siswa',
        ]);

        // ===========================
        // PROGRAMS
        // ===========================
        $paketA = Program::create([
            'jenjang'        => 'paket_a',
            'nama_program'   => 'Pendidikan Kesetaraan Paket A',
            'kode_program'   => 'PKA',
            'total_semester' => 6,
            'deskripsi'      => 'Program pendidikan setara Sekolah Dasar (SD)',
        ]);

        $paketB = Program::create([
            'jenjang'        => 'paket_b',
            'nama_program'   => 'Pendidikan Kesetaraan Paket B',
            'kode_program'   => 'PKB',
            'total_semester' => 6,
            'deskripsi'      => 'Program pendidikan setara Sekolah Menengah Pertama (SMP)',
        ]);

        $paketC = Program::create([
            'jenjang'        => 'paket_c',
            'nama_program'   => 'Pendidikan Kesetaraan Paket C',
            'kode_program'   => 'PKC',
            'total_semester' => 6,
            'deskripsi'      => 'Program pendidikan setara Sekolah Menengah Atas (SMA)',
        ]);

        // ===========================
        // ANGKATAN
        // ===========================
        $angkatanC = Angkatan::create([
            'program_id'   => $paketC->id,
            'nama_angkatan' => 'Angkatan 2024/2025',
            'tahun_ajaran' => '2024/2025',
            'tanggal_mulai' => '2024-07-15',
            'status'       => 'aktif',
        ]);

        $angkatanB = Angkatan::create([
            'program_id'   => $paketB->id,
            'nama_angkatan' => 'Angkatan 2024/2025',
            'tahun_ajaran' => '2024/2025',
            'tanggal_mulai' => '2024-07-15',
            'status'       => 'aktif',
        ]);

        $angkatanA = Angkatan::create([
            'program_id'   => $paketA->id,
            'nama_angkatan' => 'Angkatan 2024/2025',
            'tahun_ajaran' => '2024/2025',
            'tanggal_mulai' => '2024-07-15',
            'status'       => 'aktif',
        ]);

        // ===========================
        // TUTOR
        // ===========================
        $tutor = Tutor::create([
            'user_id'      => $tutorUser->id,
            'nama_lengkap' => 'Budi Santoso, S.Pd',
            'spesialisasi' => 'Matematika, IPA',
            'no_telepon'   => '08123456789',
        ]);

        // ===========================
        // MATA PELAJARAN PAKET C
        // ===========================
        $mapelC = [
            ['kode_mapel' => 'PKnC', 'nama_mapel' => 'Pendidikan Kewarganegaraan', 'semester' => 1, 'kkm' => 65],
            ['kode_mapel' => 'BIC',  'nama_mapel' => 'Bahasa Indonesia',            'semester' => 1, 'kkm' => 65],
            ['kode_mapel' => 'ENC',  'nama_mapel' => 'Bahasa Inggris',              'semester' => 1, 'kkm' => 60],
            ['kode_mapel' => 'MATC', 'nama_mapel' => 'Matematika',                  'semester' => 1, 'kkm' => 65],
            ['kode_mapel' => 'IPAC', 'nama_mapel' => 'IPA',                          'semester' => 1, 'kkm' => 65],
            ['kode_mapel' => 'IPSC', 'nama_mapel' => 'IPS',                          'semester' => 1, 'kkm' => 65],
            ['kode_mapel' => 'KEWC', 'nama_mapel' => 'Keterampilan Vokasional',     'semester' => 1, 'kkm' => 70],
        ];

        foreach ($mapelC as $m) {
            MataPelajaran::create(array_merge($m, ['program_id' => $paketC->id]));
        }

        // ===========================
        // SISWA DEMO
        // ===========================
        Siswa::create([
            'user_id'          => $siswaUser->id,
            'angkatan_id'      => $angkatanC->id,
            'nisn'             => '1234567890',
            'nama_lengkap'     => 'Ahmad Rizki Pratama',
            'tempat_lahir'     => 'Jakarta',
            'tanggal_lahir'    => '1998-05-15',
            'jenis_kelamin'    => 'L',
            'alamat'           => 'Jl. Merdeka No. 10, Jakarta Pusat',
            'nama_orang_tua'   => 'Bapak Rizki',
            'status'           => 'aktif',
        ]);

        // Tambah 10 siswa dummy lainnya
        $namaDummy = [
            'Siti Rahayu', 'Dewi Lestari', 'Eko Prasetyo', 'Fitri Handayani',
            'Hendra Wijaya', 'Indah Permata', 'Joko Susanto', 'Kartika Sari',
            'Lukman Hakim', 'Mutiara Indah',
        ];

        foreach ($namaDummy as $i => $nama) {
            Siswa::create([
                'angkatan_id'   => $i < 5 ? $angkatanC->id : ($i < 8 ? $angkatanB->id : $angkatanA->id),
                'nisn'          => '99999' . str_pad($i + 1, 5, '0', STR_PAD_LEFT),
                'nama_lengkap'  => $nama,
                'tempat_lahir'  => 'Jakarta',
                'tanggal_lahir' => '1995-0' . (($i % 9) + 1) . '-15',
                'jenis_kelamin' => $i % 2 === 0 ? 'P' : 'L',
                'alamat'        => 'Jl. Demo No. ' . ($i + 1) . ', Jakarta',
                'nama_orang_tua' => 'Orang Tua ' . ($i + 1),
                'status'        => 'aktif',
            ]);
        }

        // ===========================
        // KELAS & JADWAL SEEDING
        // ===========================
        $kelasC = \App\Models\Kelas::create([
            'angkatan_id' => $angkatanC->id,
            'tutor_id'    => $tutor->id,
            'nama_kelas'  => 'Paket C - Kelas 10 Mandiri',
            'semester'    => 1,
            'kapasitas'   => 30,
            'is_active'   => true,
        ]);

        $kelasB = \App\Models\Kelas::create([
            'angkatan_id' => $angkatanB->id,
            'tutor_id'    => $tutor->id,
            'nama_kelas'  => 'Paket B - Kelas 8 Terpadu',
            'semester'    => 1,
            'kapasitas'   => 30,
            'is_active'   => true,
        ]);

        $kelasA = \App\Models\Kelas::create([
            'angkatan_id' => $angkatanA->id,
            'tutor_id'    => $tutor->id,
            'nama_kelas'  => 'Paket A - Kelas 4 Bintang',
            'semester'    => 1,
            'kapasitas'   => 25,
            'is_active'   => true,
        ]);

        $mapels = MataPelajaran::all();

        if ($mapels->count() > 0) {
            \App\Models\Jadwal::create([
                'kelas_id'          => $kelasC->id,
                'mata_pelajaran_id' => $mapels[0]->id,
                'tutor_id'          => $tutor->id,
                'hari'              => 'senin',
                'jam_mulai'         => '08:00',
                'jam_selesai'       => '10:00',
                'ruangan'           => 'Ruang C-01',
                'is_active'         => true,
            ]);

            if ($mapels->count() > 1) {
                \App\Models\Jadwal::create([
                    'kelas_id'          => $kelasC->id,
                    'mata_pelajaran_id' => $mapels[1]->id,
                    'tutor_id'          => $tutor->id,
                    'hari'              => 'jumat',
                    'jam_mulai'         => '14:00',
                    'jam_selesai'       => '16:30',
                    'ruangan'           => 'Lab Vokasi & Digital',
                    'is_active'         => true,
                ]);
            }
        }
        // ===========================
        // ENROLL SISWA KE KELAS (kelas_siswa)
        // ===========================
        $allSiswas = Siswa::all();
        foreach ($allSiswas as $s) {
            $targetKelas = $s->angkatan_id == $angkatanC->id ? $kelasC : ($s->angkatan_id == $angkatanB->id ? $kelasB : $kelasA);
            if (!$targetKelas->siswas()->where('siswa_id', $s->id)->exists()) {
                $targetKelas->siswas()->attach($s->id, [
                    'status' => 'aktif',
                    'tanggal_masuk' => now()->toDateString(),
                ]);
            }
        }

        // ===========================
        // INITIAL SETTINGS
        // ===========================
        \App\Models\Setting::set('nama_pkbm', 'PKBM Harapan Bangsa Mandiri');
        \App\Models\Setting::set('npsn', 'P9908123');
        \App\Models\Setting::set('kepala_pkbm', 'Drs. H. Mulyadi, M.Pd');
        \App\Models\Setting::set('alamat', 'Jl. Pendidikan Kesetaraan No. 45, Jakarta Pusat');
        \App\Models\Setting::set('no_telepon', '(021) 7890-1234');
        \App\Models\Setting::set('email', 'info@pkbm.sch.id');
        \App\Models\Setting::set('tahun_ajaran_aktif', '2024/2025');
        \App\Models\Setting::set('semester_aktif', '1');

        $this->command->info('✅ Seeder PKBM berhasil dijalankan!');
        $this->command->info('📧 Admin login: admin@pkbm.sch.id / password');
        $this->command->info('📧 Tutor login: tutor@pkbm.sch.id / password');
        $this->command->info('📧 Siswa login: siswa@pkbm.sch.id / password');
    }
}
