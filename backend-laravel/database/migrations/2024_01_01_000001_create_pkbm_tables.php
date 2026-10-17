<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration: Create Programs Table
 * Tabel program pendidikan PKBM (Paket A, B, C)
 */
return new class extends Migration
{
    public function up(): void
    {
        // ===========================
        // PROGRAMS
        // ===========================
        Schema::create('programs', function (Blueprint $table) {
            $table->id();
            $table->enum('jenjang', ['paket_a', 'paket_b', 'paket_c']);
            $table->string('nama_program');
            $table->string('kode_program', 20)->unique();
            $table->integer('total_semester')->default(6);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        // ===========================
        // ANGKATANS
        // ===========================
        Schema::create('angkatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->string('nama_angkatan');
            $table->string('tahun_ajaran', 10); // "2024/2025"
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['aktif', 'selesai', 'tutup'])->default('aktif');
            $table->timestamps();
            $table->softDeletes();
        });

        // ===========================
        // TUTORS
        // ===========================
        Schema::create('tutors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('nip', 30)->nullable()->unique();
            $table->string('nama_lengkap');
            $table->string('spesialisasi')->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->string('foto')->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // ===========================
        // SISWA
        // ===========================
        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('angkatan_id')->constrained()->cascadeOnDelete();
            $table->string('nisn', 20)->unique();
            $table->string('nik', 20)->nullable()->unique();
            $table->string('nama_lengkap');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->text('alamat');
            $table->string('rt_rw', 10)->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('no_telepon', 20)->nullable();
            $table->string('nama_orang_tua');
            $table->string('pekerjaan_orang_tua')->nullable();
            $table->string('telepon_orang_tua', 20)->nullable();
            $table->enum('status', ['aktif', 'lulus', 'dropout', 'cuti', 'pindah'])->default('aktif');
            $table->string('foto')->nullable();
            $table->string('ijazah_terakhir')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['angkatan_id', 'status']);
            $table->index('nisn');
        });

        // ===========================
        // MATA PELAJARANS
        // ===========================
        Schema::create('mata_pelajarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->string('kode_mapel', 10)->unique();
            $table->string('nama_mapel');
            $table->integer('semester');
            $table->integer('kkm')->default(65);
            $table->integer('jam_per_minggu')->default(2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ===========================
        // KELAS
        // ===========================
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('angkatan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tutor_id')->constrained()->nullOnDelete();
            $table->string('nama_kelas', 50);
            $table->integer('semester');
            $table->integer('kapasitas')->default(30);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ===========================
        // KELAS SISWA (Pivot)
        // ===========================
        Schema::create('kelas_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained()->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->enum('status', ['aktif', 'pindah', 'keluar'])->default('aktif');
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->timestamps();

            $table->unique(['kelas_id', 'siswa_id']);
        });

        // ===========================
        // JADWALS
        // ===========================
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tutor_id')->constrained();
            $table->enum('hari', ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('ruangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // ===========================
        // ABSENSI
        // ===========================
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jadwal_id')->constrained()->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->date('tanggal');
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpha'])->default('alpha');
            $table->text('keterangan')->nullable();
            $table->string('qr_token')->nullable();
            $table->timestamp('waktu_absen')->nullable();
            $table->timestamps();

            $table->unique(['jadwal_id', 'siswa_id', 'tanggal']);
            $table->index(['siswa_id', 'tanggal']);
        });

        // ===========================
        // NILAIS
        // ===========================
        Schema::create('nilais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained()->cascadeOnDelete();
            $table->integer('semester');
            $table->decimal('nilai_tugas', 5, 2)->default(0);
            $table->decimal('nilai_uts', 5, 2)->default(0);
            $table->decimal('nilai_uas', 5, 2)->default(0);
            $table->decimal('nilai_praktek', 5, 2)->nullable();
            $table->decimal('nilai_akhir', 5, 2)->nullable();
            $table->enum('predikat', ['A', 'B', 'C', 'D'])->nullable();
            $table->boolean('lulus')->default(false);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'mata_pelajaran_id', 'kelas_id', 'semester']);
        });

        // ===========================
        // RAPORTS
        // ===========================
        Schema::create('raports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('kelas_id')->constrained()->cascadeOnDelete();
            $table->integer('semester');
            $table->string('tahun_ajaran', 10);
            $table->decimal('rata_rata', 5, 2)->default(0);
            $table->integer('peringkat')->default(0);
            $table->integer('total_hadir')->default(0);
            $table->integer('total_sakit')->default(0);
            $table->integer('total_izin')->default(0);
            $table->integer('total_alpha')->default(0);
            $table->enum('status_naik', ['naik', 'tinggal', 'lulus'])->nullable();
            $table->string('file_pdf')->nullable();
            $table->text('catatan_wali_kelas')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->unique(['siswa_id', 'kelas_id', 'semester']);
        });

        // ===========================
        // SERTIFIKATS
        // ===========================
        Schema::create('sertifikats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->foreignId('program_id')->constrained()->cascadeOnDelete();
            $table->string('nomor_sertifikat')->unique();
            $table->date('tanggal_terbit');
            $table->string('file_pdf')->nullable();
            $table->enum('status', ['draft', 'terbit', 'revisi'])->default('draft');
            $table->timestamps();
        });

        // ===========================
        // PEMBAYARANS
        // ===========================
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->string('bulan', 20);
            $table->integer('tahun');
            $table->decimal('nominal', 10, 2);
            $table->enum('jenis', ['spp', 'seragam', 'buku', 'lainnya'])->default('spp');
            $table->enum('status', ['lunas', 'belum', 'cicil'])->default('belum');
            $table->date('tanggal_bayar')->nullable();
            $table->string('bukti_bayar')->nullable();
            $table->string('keterangan')->nullable();
            $table->foreignId('diterima_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['siswa_id', 'tahun', 'bulan']);
        });

        // ===========================
        // PENGUMUMANS
        // ===========================
        Schema::create('pengumumans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('judul');
            $table->text('isi');
            $table->enum('target', ['semua', 'siswa', 'tutor', 'admin'])->default('semua');
            $table->boolean('is_active')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengumumans');
        Schema::dropIfExists('pembayarans');
        Schema::dropIfExists('sertifikats');
        Schema::dropIfExists('raports');
        Schema::dropIfExists('nilais');
        Schema::dropIfExists('absensi');
        Schema::dropIfExists('jadwals');
        Schema::dropIfExists('kelas_siswa');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('mata_pelajarans');
        Schema::dropIfExists('siswa');
        Schema::dropIfExists('tutors');
        Schema::dropIfExists('angkatans');
        Schema::dropIfExists('programs');
    }
};
