-- ==============================================================
-- DOCKER INITIALIZATION SCRIPT FOR MYSQL 8.0
-- PKBM (PAKET A, PAKET B, PAKET C)
-- ==============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+07:00";

CREATE DATABASE IF NOT EXISTS `pkbm_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `pkbm_db`;

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','tutor','siswa') NOT NULL DEFAULT 'siswa',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: programs
-- --------------------------------------------------------
DROP TABLE IF EXISTS `programs`;
CREATE TABLE `programs` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `jenjang` enum('paket_a','paket_b','paket_c') NOT NULL,
  `nama_program` varchar(255) NOT NULL,
  `kode_program` varchar(20) NOT NULL,
  `total_semester` int(11) NOT NULL DEFAULT 6,
  `deskripsi` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `programs_kode_program_unique` (`kode_program`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: angkatans
-- --------------------------------------------------------
DROP TABLE IF EXISTS `angkatans`;
CREATE TABLE `angkatans` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `nama_angkatan` varchar(255) NOT NULL,
  `tahun_ajaran` varchar(10) NOT NULL,
  `tanggal_mulai` date NOT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `status` enum('aktif','selesai','tutup') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `angkatans_program_id_foreign` (`program_id`),
  CONSTRAINT `angkatans_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: tutors
-- --------------------------------------------------------
DROP TABLE IF EXISTS `tutors`;
CREATE TABLE `tutors` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `nip` varchar(30) DEFAULT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `spesialisasi` varchar(255) DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tutors_nip_unique` (`nip`),
  KEY `tutors_user_id_foreign` (`user_id`),
  CONSTRAINT `tutors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: siswa
-- --------------------------------------------------------
DROP TABLE IF EXISTS `siswa`;
CREATE TABLE `siswa` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `angkatan_id` bigint(20) UNSIGNED NOT NULL,
  `nisn` varchar(20) NOT NULL,
  `nik` varchar(20) DEFAULT NULL,
  `nama_lengkap` varchar(255) NOT NULL,
  `tempat_lahir` varchar(255) NOT NULL,
  `tanggal_lahir` date NOT NULL,
  `jenis_kelamin` enum('L','P') NOT NULL,
  `alamat` text NOT NULL,
  `rt_rw` varchar(10) DEFAULT NULL,
  `kelurahan` varchar(255) DEFAULT NULL,
  `kecamatan` varchar(255) DEFAULT NULL,
  `kota` varchar(255) DEFAULT NULL,
  `provinsi` varchar(255) DEFAULT NULL,
  `kode_pos` varchar(10) DEFAULT NULL,
  `no_telepon` varchar(20) DEFAULT NULL,
  `nama_orang_tua` varchar(255) NOT NULL,
  `pekerjaan_orang_tua` varchar(255) DEFAULT NULL,
  `telepon_orang_tua` varchar(20) DEFAULT NULL,
  `status` enum('aktif','lulus','dropout','cuti','pindah') NOT NULL DEFAULT 'aktif',
  `foto` varchar(255) DEFAULT NULL,
  `ijazah_terakhir` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `siswa_nisn_unique` (`nisn`),
  UNIQUE KEY `siswa_nik_unique` (`nik`),
  KEY `siswa_user_id_foreign` (`user_id`),
  KEY `siswa_angkatan_id_foreign` (`angkatan_id`),
  KEY `siswa_angkatan_id_status_index` (`angkatan_id`,`status`),
  CONSTRAINT `siswa_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `siswa_angkatan_id_foreign` FOREIGN KEY (`angkatan_id`) REFERENCES `angkatans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: mata_pelajarans
-- --------------------------------------------------------
DROP TABLE IF EXISTS `mata_pelajarans`;
CREATE TABLE `mata_pelajarans` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `kode_mapel` varchar(10) NOT NULL,
  `nama_mapel` varchar(255) NOT NULL,
  `semester` int(11) NOT NULL,
  `kkm` int(11) NOT NULL DEFAULT 65,
  `jam_per_minggu` int(11) NOT NULL DEFAULT 2,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mata_pelajarans_kode_mapel_unique` (`kode_mapel`),
  KEY `mata_pelajarans_program_id_foreign` (`program_id`),
  CONSTRAINT `mata_pelajarans_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: kelas
-- --------------------------------------------------------
DROP TABLE IF EXISTS `kelas`;
CREATE TABLE `kelas` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `angkatan_id` bigint(20) UNSIGNED NOT NULL,
  `tutor_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nama_kelas` varchar(50) NOT NULL,
  `semester` int(11) NOT NULL,
  `kapasitas` int(11) NOT NULL DEFAULT 30,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `kelas_angkatan_id_foreign` (`angkatan_id`),
  KEY `kelas_tutor_id_foreign` (`tutor_id`),
  CONSTRAINT `kelas_angkatan_id_foreign` FOREIGN KEY (`angkatan_id`) REFERENCES `angkatans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kelas_tutor_id_foreign` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: kelas_siswa
-- --------------------------------------------------------
DROP TABLE IF EXISTS `kelas_siswa`;
CREATE TABLE `kelas_siswa` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `kelas_id` bigint(20) UNSIGNED NOT NULL,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('aktif','pindah','keluar') NOT NULL DEFAULT 'aktif',
  `tanggal_masuk` date NOT NULL,
  `tanggal_keluar` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `kelas_siswa_kelas_id_siswa_id_unique` (`kelas_id`,`siswa_id`),
  KEY `kelas_siswa_siswa_id_foreign` (`siswa_id`),
  CONSTRAINT `kelas_siswa_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `kelas_siswa_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: jadwals
-- --------------------------------------------------------
DROP TABLE IF EXISTS `jadwals`;
CREATE TABLE `jadwals` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `kelas_id` bigint(20) UNSIGNED NOT NULL,
  `mata_pelajaran_id` bigint(20) UNSIGNED NOT NULL,
  `tutor_id` bigint(20) UNSIGNED NOT NULL,
  `hari` enum('senin','selasa','rabu','kamis','jumat','sabtu') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL,
  `ruangan` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jadwals_kelas_id_foreign` (`kelas_id`),
  KEY `jadwals_mata_pelajaran_id_foreign` (`mata_pelajaran_id`),
  KEY `jadwals_tutor_id_foreign` (`tutor_id`),
  CONSTRAINT `jadwals_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jadwals_mata_pelajaran_id_foreign` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajarans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `jadwals_tutor_id_foreign` FOREIGN KEY (`tutor_id`) REFERENCES `tutors` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: absensi
-- --------------------------------------------------------
DROP TABLE IF EXISTS `absensi`;
CREATE TABLE `absensi` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `jadwal_id` bigint(20) UNSIGNED NOT NULL,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `status` enum('hadir','sakit','izin','alpha') NOT NULL DEFAULT 'alpha',
  `keterangan` text DEFAULT NULL,
  `qr_token` varchar(255) DEFAULT NULL,
  `waktu_absen` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `absensi_jadwal_id_siswa_id_tanggal_unique` (`jadwal_id`,`siswa_id`,`tanggal`),
  KEY `absensi_siswa_id_foreign` (`siswa_id`),
  KEY `absensi_siswa_id_tanggal_index` (`siswa_id`,`tanggal`),
  CONSTRAINT `absensi_jadwal_id_foreign` FOREIGN KEY (`jadwal_id`) REFERENCES `jadwals` (`id`) ON DELETE CASCADE,
  CONSTRAINT `absensi_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: nilais
-- --------------------------------------------------------
DROP TABLE IF EXISTS `nilais`;
CREATE TABLE `nilais` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `mata_pelajaran_id` bigint(20) UNSIGNED NOT NULL,
  `kelas_id` bigint(20) UNSIGNED NOT NULL,
  `semester` int(11) NOT NULL,
  `nilai_tugas` decimal(5,2) NOT NULL DEFAULT 0.00,
  `nilai_uts` decimal(5,2) NOT NULL DEFAULT 0.00,
  `nilai_uas` decimal(5,2) NOT NULL DEFAULT 0.00,
  `nilai_praktek` decimal(5,2) DEFAULT NULL,
  `nilai_akhir` decimal(5,2) DEFAULT NULL,
  `predikat` enum('A','B','C','D') DEFAULT NULL,
  `lulus` tinyint(1) NOT NULL DEFAULT 0,
  `catatan` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nilais_siswa_mapel_kelas_semester_unique` (`siswa_id`,`mata_pelajaran_id`,`kelas_id`,`semester`),
  KEY `nilais_mata_pelajaran_id_foreign` (`mata_pelajaran_id`),
  KEY `nilais_kelas_id_foreign` (`kelas_id`),
  CONSTRAINT `nilais_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nilais_mata_pelajaran_id_foreign` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajarans` (`id`) ON DELETE CASCADE,
  CONSTRAINT `nilais_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: raports
-- --------------------------------------------------------
DROP TABLE IF EXISTS `raports`;
CREATE TABLE `raports` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `kelas_id` bigint(20) UNSIGNED NOT NULL,
  `semester` int(11) NOT NULL,
  `tahun_ajaran` varchar(10) NOT NULL,
  `rata_rata` decimal(5,2) NOT NULL DEFAULT 0.00,
  `peringkat` int(11) NOT NULL DEFAULT 0,
  `total_hadir` int(11) NOT NULL DEFAULT 0,
  `total_sakit` int(11) NOT NULL DEFAULT 0,
  `total_izin` int(11) NOT NULL DEFAULT 0,
  `total_alpha` int(11) NOT NULL DEFAULT 0,
  `status_naik` enum('naik','tinggal','lulus') DEFAULT NULL,
  `file_pdf` varchar(255) DEFAULT NULL,
  `catatan_wali_kelas` text DEFAULT NULL,
  `generated_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `raports_siswa_id_kelas_id_semester_unique` (`siswa_id`,`kelas_id`,`semester`),
  KEY `raports_kelas_id_foreign` (`kelas_id`),
  CONSTRAINT `raports_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `raports_kelas_id_foreign` FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: sertifikats
-- --------------------------------------------------------
DROP TABLE IF EXISTS `sertifikats`;
CREATE TABLE `sertifikats` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `program_id` bigint(20) UNSIGNED NOT NULL,
  `nomor_sertifikat` varchar(255) NOT NULL,
  `tanggal_terbit` date NOT NULL,
  `file_pdf` varchar(255) DEFAULT NULL,
  `status` enum('draft','terbit','revisi') NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sertifikats_nomor_sertifikat_unique` (`nomor_sertifikat`),
  KEY `sertifikats_siswa_id_foreign` (`siswa_id`),
  KEY `sertifikats_program_id_foreign` (`program_id`),
  CONSTRAINT `sertifikats_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sertifikats_program_id_foreign` FOREIGN KEY (`program_id`) REFERENCES `programs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: pembayarans
-- --------------------------------------------------------
DROP TABLE IF EXISTS `pembayarans`;
CREATE TABLE `pembayarans` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `siswa_id` bigint(20) UNSIGNED NOT NULL,
  `bulan` varchar(20) NOT NULL,
  `tahun` int(11) NOT NULL,
  `nominal` decimal(10,2) NOT NULL,
  `jenis` enum('spp','seragam','buku','lainnya') NOT NULL DEFAULT 'spp',
  `status` enum('lunas','belum','cicil') NOT NULL DEFAULT 'belum',
  `tanggal_bayar` date DEFAULT NULL,
  `bukti_bayar` varchar(255) DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `diterima_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pembayarans_siswa_id_foreign` (`siswa_id`),
  KEY `pembayarans_diterima_oleh_foreign` (`diterima_oleh`),
  KEY `pembayarans_siswa_tahun_bulan_index` (`siswa_id`,`tahun`,`bulan`),
  CONSTRAINT `pembayarans_siswa_id_foreign` FOREIGN KEY (`siswa_id`) REFERENCES `siswa` (`id`) ON DELETE CASCADE,
  CONSTRAINT `pembayarans_diterima_oleh_foreign` FOREIGN KEY (`diterima_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: pengumumans
-- --------------------------------------------------------
DROP TABLE IF EXISTS `pengumumans`;
CREATE TABLE `pengumumans` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `judul` varchar(255) NOT NULL,
  `isi` text NOT NULL,
  `target` enum('semua','siswa','tutor','admin') NOT NULL DEFAULT 'semua',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `published_at` timestamp NULL DEFAULT NULL,
  `expired_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pengumumans_user_id_foreign` (`user_id`),
  CONSTRAINT `pengumumans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: settings
-- --------------------------------------------------------
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nama_pkbm` varchar(255) NOT NULL DEFAULT 'PKBM Mandiri Cendekia',
  `npsn` varchar(20) DEFAULT 'P9967812',
  `alamat` text DEFAULT NULL,
  `telepon` varchar(20) DEFAULT '021-88997766',
  `email` varchar(255) DEFAULT 'info@pkbm.sch.id',
  `kepala_pkbm` varchar(255) DEFAULT 'Drs. H. Mulyadi, M.Pd',
  `nip_kepala` varchar(30) DEFAULT '197008151996031002',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================
-- DATA AWAL (SEED DATA / DEMO ACCOUNTS)
-- ==============================================================

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Administrator PKBM', 'admin@pkbm.sch.id', '$2y$12$DmbxM5R71gB443W1UoJ58uG89N2f7Gf9fK51j2w4i6d8O4h9k1m2.', 'admin', NOW(), NOW()),
(2, 'Budi Santoso, S.Pd', 'tutor@pkbm.sch.id', '$2y$12$DmbxM5R71gB443W1UoJ58uG89N2f7Gf9fK51j2w4i6d8O4h9k1m2.', 'tutor', NOW(), NOW()),
(3, 'Ahmad Rizki', 'siswa@pkbm.sch.id', '$2y$12$DmbxM5R71gB443W1UoJ58uG89N2f7Gf9fK51j2w4i6d8O4h9k1m2.', 'siswa', NOW(), NOW());

INSERT INTO `programs` (`id`, `jenjang`, `nama_program`, `kode_program`, `total_semester`, `deskripsi`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'paket_a', 'Pendidikan Kesetaraan Paket A', 'PKA', 6, 'Program pendidikan setara Sekolah Dasar (SD)', 1, NOW(), NOW()),
(2, 'paket_b', 'Pendidikan Kesetaraan Paket B', 'PKB', 6, 'Program pendidikan setara Sekolah Menengah Pertama (SMP)', 1, NOW(), NOW()),
(3, 'paket_c', 'Pendidikan Kesetaraan Paket C', 'PKC', 6, 'Program pendidikan setara Sekolah Menengah Atas (SMA)', 1, NOW(), NOW());

INSERT INTO `angkatans` (`id`, `program_id`, `nama_angkatan`, `tahun_ajaran`, `tanggal_mulai`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'Angkatan 2024/2025 Paket A', '2024/2025', '2024-07-15', 'aktif', NOW(), NOW()),
(2, 2, 'Angkatan 2024/2025 Paket B', '2024/2025', '2024-07-15', 'aktif', NOW(), NOW()),
(3, 3, 'Angkatan 2024/2025 Paket C', '2024/2025', '2024-07-15', 'aktif', NOW(), NOW());

INSERT INTO `tutors` (`id`, `user_id`, `nip`, `nama_lengkap`, `spesialisasi`, `no_telepon`, `alamat`, `created_at`, `updated_at`) VALUES
(1, 2, '198501102010011005', 'Budi Santoso, S.Pd', 'Bahasa Indonesia & Matematika Terapan', '081234567890', 'Jl. Merdeka No. 45, Jakarta', NOW(), NOW());

INSERT INTO `siswa` (`id`, `user_id`, `angkatan_id`, `nisn`, `nik`, `nama_lengkap`, `tempat_lahir`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `no_telepon`, `nama_orang_tua`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 3, '0054321987', '3171012304050001', 'Ahmad Rizki', 'Jakarta', '2005-04-23', 'L', 'Jl. Kenanga No. 12, Tebet, Jakarta Selatan', '085711223344', 'Bambang Supriyadi', 'aktif', NOW(), NOW());

INSERT INTO `mata_pelajarans` (`id`, `program_id`, `kode_mapel`, `nama_mapel`, `semester`, `kkm`, `jam_per_minggu`, `created_at`, `updated_at`) VALUES
(1, 3, 'PKC-IND', 'Bahasa Indonesia', 1, 70, 3, NOW(), NOW()),
(2, 3, 'PKC-MAT', 'Matematika Terapan', 1, 65, 3, NOW(), NOW()),
(3, 3, 'PKC-PKN', 'Pendidikan Kewarganegaraan', 1, 75, 2, NOW(), NOW()),
(4, 3, 'PKC-KWU', 'Kewirausahaan Digital', 1, 75, 4, NOW(), NOW());

INSERT INTO `kelas` (`id`, `angkatan_id`, `tutor_id`, `nama_kelas`, `semester`, `kapasitas`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 3, 1, 'Paket C - Kelas 10 Mandiri', 1, 30, 1, NOW(), NOW()),
(2, 2, 1, 'Paket B - Kelas 8 Terpadu', 1, 30, 1, NOW(), NOW()),
(3, 1, 1, 'Paket A - Kelas 4 Bintang', 1, 25, 1, NOW(), NOW());

INSERT INTO `kelas_siswa` (`id`, `kelas_id`, `siswa_id`, `status`, `tanggal_masuk`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'aktif', '2024-07-15', NOW(), NOW());

INSERT INTO `jadwals` (`id`, `kelas_id`, `mata_pelajaran_id`, `tutor_id`, `hari`, `jam_mulai`, `jam_selesai`, `ruangan`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 4, 1, 'jumat', '14:00:00', '17:00:00', 'Lab Komputer / Daring', 1, NOW(), NOW()),
(2, 1, 2, 1, 'senin', '08:00:00', '10:00:00', 'Ruang C-01', 1, NOW(), NOW());

INSERT INTO `nilais` (`id`, `siswa_id`, `mata_pelajaran_id`, `kelas_id`, `semester`, `nilai_tugas`, `nilai_uts`, `nilai_uas`, `nilai_praktek`, `nilai_akhir`, `predikat`, `lulus`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 90.00, 88.00, 92.00, 90.00, 90.20, 'A', 1, NOW(), NOW()),
(2, 1, 2, 1, 1, 85.00, 80.00, 84.00, 85.00, 83.50, 'B', 1, NOW(), NOW()),
(3, 1, 4, 1, 1, 95.00, 92.00, 96.00, 98.00, 95.60, 'A', 1, NOW(), NOW());

INSERT INTO `settings` (`id`, `nama_pkbm`, `npsn`, `alamat`, `telepon`, `email`, `kepala_pkbm`, `nip_kepala`, `created_at`, `updated_at`) VALUES
(1, 'PKBM Mandiri Cendekia', 'P9967812', 'Jl. Pendidikan No. 100, Jakarta Pusat', '021-88997766', 'admin@pkbm.sch.id', 'Drs. H. Mulyadi, M.Pd', '197008151996031002', NOW(), NOW());

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;
