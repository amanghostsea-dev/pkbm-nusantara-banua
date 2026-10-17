<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Siswa;
use App\Models\Program;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Tutor;
use App\Models\Sertifikat;
use App\Models\Raport;
use App\Models\Materi;
use App\Models\Tugas;
use App\Models\TugasPengumpulan;

class LmsAndVerificationSeeder extends Seeder
{
    public function run(): void
    {
        $siswaUtama = Siswa::first();
        $siswaKedua = Siswa::skip(1)->first() ?? $siswaUtama;
        $programC = Program::where('jenjang', 'paket_c')->first() ?? Program::first();
        $kelasC = Kelas::first();
        $mapelList = MataPelajaran::all();
        $mapel1 = $mapelList->first();
        $mapel2 = $mapelList->skip(1)->first() ?? $mapel1;
        $tutor = Tutor::first();

        // 1. Seed Sertifikat Terbit
        if ($siswaUtama && $programC) {
            Sertifikat::firstOrCreate(
                ['nomor_sertifikat' => 'SRT/PKBM/2024/001'],
                [
                    'siswa_id'       => $siswaUtama->id,
                    'program_id'     => $programC->id,
                    'tanggal_terbit' => '2024-05-20',
                    'file_pdf'       => '/api/v1/sertifikat/download/sertifikat_1_paket_c_2024.pdf',
                    'status'         => 'terbit',
                ]
            );
        }

        if ($siswaKedua && $programC) {
            Sertifikat::firstOrCreate(
                ['nomor_sertifikat' => 'SRT/PKBM/2024/002'],
                [
                    'siswa_id'       => $siswaKedua->id,
                    'program_id'     => $programC->id,
                    'tanggal_terbit' => '2024-05-20',
                    'file_pdf'       => null,
                    'status'         => 'terbit',
                ]
            );
        }

        // 2. Seed Raport
        if ($siswaUtama && $kelasC) {
            Raport::firstOrCreate(
                ['siswa_id' => $siswaUtama->id, 'semester' => 1, 'tahun_ajaran' => '2024/2025'],
                [
                    'kelas_id'           => $kelasC->id,
                    'rata_rata'          => 89.75,
                    'peringkat'          => 1,
                    'total_hadir'        => 23,
                    'total_sakit'        => 1,
                    'total_izin'         => 0,
                    'total_alpha'        => 0,
                    'status_naik'        => 'naik',
                    'catatan_wali_kelas' => 'Siswa menunjukkan keaktifan dan kemandirian belajar yang luar biasa tinggi.',
                    'generated_at'       => now(),
                ]
            );
        }

        // 3. Seed Materi LMS
        if ($kelasC && $mapel1) {
            Materi::firstOrCreate(
                ['judul' => 'Modul 01: Pengantar Kewirausahaan & Analisis Peluang Usaha'],
                [
                    'kelas_id'          => $kelasC->id,
                    'mata_pelajaran_id' => $mapel1->id,
                    'tutor_id'          => $tutor?->id,
                    'deskripsi'         => 'Pelajari dasar-dasar merintis usaha mandiri, identifikasi target pasar, dan evaluasi kelayakan finansial.',
                    'tipe'              => 'pdf',
                    'file_path'         => null,
                    'link_url'          => 'https://repositori.kemdikbud.go.id/modul-pkbm-kewirausahaan.pdf',
                    'is_published'      => true,
                ]
            );

            Materi::firstOrCreate(
                ['judul' => 'Video Tutorial: Strategi Pemasaran Digital & Marketplace'],
                [
                    'kelas_id'          => $kelasC->id,
                    'mata_pelajaran_id' => $mapel1->id,
                    'tutor_id'          => $tutor?->id,
                    'deskripsi'         => 'Video panduan praktis membuat toko online, optimasi kata kunci produk, dan melayani pelanggan dengan etika bisnis.',
                    'tipe'              => 'video',
                    'file_path'         => null,
                    'link_url'          => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
                    'is_published'      => true,
                ]
            );
        }

        if ($kelasC && $mapel2) {
            Materi::firstOrCreate(
                ['judul' => 'Modul 02: Tata Bahasa Baku & Penulisan Dokumen Resmi'],
                [
                    'kelas_id'          => $kelasC->id,
                    'mata_pelajaran_id' => $mapel2->id,
                    'tutor_id'          => $tutor?->id,
                    'deskripsi'         => 'Panduan kaidah ejaan EYD, penyusunan surat dinas, penulisan esai naratif dan argumentatif yang efektif.',
                    'tipe'              => 'dokumen',
                    'file_path'         => null,
                    'link_url'          => null,
                    'is_published'      => true,
                ]
            );
        }

        // 4. Seed Tugas LMS & Submission
        if ($kelasC && $mapel1) {
            $tugas1 = Tugas::firstOrCreate(
                ['judul' => 'Tugas 1: Pembuatan Business Model Canvas (BMC)'],
                [
                    'kelas_id'          => $kelasC->id,
                    'mata_pelajaran_id' => $mapel1->id,
                    'tutor_id'          => $tutor?->id,
                    'deskripsi'         => 'Pilihlah salah satu ide usaha sederhana di lingkungan sekitar Anda. Buat 9 blok Business Model Canvas (BMC) mulai dari Customer Segments hingga Revenue Streams. Kumpulkan dalam bentuk PDF atau catatan terperinci.',
                    'deadline'          => now()->addDays(5),
                    'poin_maksimal'     => 100,
                    'status'            => 'aktif',
                ]
            );

            $tugas2 = Tugas::firstOrCreate(
                ['judul' => 'Tugas 2: Resume Esai Peran Kesetaraan di Era Modern'],
                [
                    'kelas_id'          => $kelasC->id,
                    'mata_pelajaran_id' => $mapel2?->id ?? $mapel1->id,
                    'tutor_id'          => $tutor?->id,
                    'deskripsi'         => 'Tulis esai singkat 300-500 kata mengenai manfaat pendidikan kesetaraan bagi pengembangan karier profesional Anda.',
                    'deadline'          => now()->subDays(2),
                    'poin_maksimal'     => 100,
                    'status'            => 'selesai',
                ]
            );

            // Seed submission for siswaUtama on Tugas 2 (Already graded)
            if ($siswaUtama) {
                TugasPengumpulan::firstOrCreate(
                    ['tugas_id' => $tugas2->id, 'siswa_id' => $siswaUtama->id],
                    [
                        'catatan_siswa'  => 'Selamat siang Tutor, berikut saya kumpulkan esai mengenai peran pendidikan kesetaraan. Terima kasih banyak atas bimbingannya.',
                        'tanggal_kumpul' => now()->subDays(3),
                        'nilai'          => 94,
                        'feedback'       => 'Sangat memuaskan! Gagasan disampaikan dengan runut, argumentasi kuat, dan pilihan kata sangat santun serta profesional.',
                        'status'         => 'dinilai',
                        'dinilai_at'     => now()->subDays(1),
                    ]
                );
            }
        }
    }
}
