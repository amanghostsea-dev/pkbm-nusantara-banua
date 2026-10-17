<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ===========================
        // MATERI / MODUL BELAJAR DIGITAL
        // ===========================
        Schema::create('materis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->foreignId('tutor_id')->nullable()->constrained('tutors')->nullOnDelete();
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->enum('tipe', ['pdf', 'video', 'artikel', 'dokumen'])->default('pdf');
            $table->string('file_path')->nullable();
            $table->string('link_url')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->index(['kelas_id', 'mata_pelajaran_id']);
        });

        // ===========================
        // TUGAS SISWA
        // ===========================
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->foreignId('tutor_id')->nullable()->constrained('tutors')->nullOnDelete();
            $table->string('judul');
            $table->text('deskripsi');
            $table->string('file_lampiran')->nullable();
            $table->dateTime('deadline');
            $table->integer('poin_maksimal')->default(100);
            $table->enum('status', ['aktif', 'selesai', 'draft'])->default('aktif');
            $table->timestamps();

            $table->index(['kelas_id', 'mata_pelajaran_id', 'deadline']);
        });

        // ===========================
        // PENGUMPULAN TUGAS SISWA (SUBMISSION)
        // ===========================
        Schema::create('tugas_pengumpulans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tugas_id')->constrained('tugas')->cascadeOnDelete();
            $table->foreignId('siswa_id')->constrained('siswa')->cascadeOnDelete();
            $table->string('file_tugas')->nullable();
            $table->text('catatan_siswa')->nullable();
            $table->dateTime('tanggal_kumpul');
            $table->integer('nilai')->nullable();
            $table->text('feedback')->nullable();
            $table->enum('status', ['dikirim', 'dinilai', 'terlambat', 'revisi'])->default('dikirim');
            $table->dateTime('dinilai_at')->nullable();
            $table->timestamps();

            $table->unique(['tugas_id', 'siswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tugas_pengumpulans');
        Schema::dropIfExists('tugas');
        Schema::dropIfExists('materis');
    }
};
