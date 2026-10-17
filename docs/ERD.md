# 📊 ERD — Sistem Informasi PKBM
## Entity Relationship Diagram

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email
        string password
        enum role "admin|tutor|siswa|orang_tua"
        boolean is_active
        timestamp created_at
        timestamp updated_at
    }

    PROGRAMS {
        bigint id PK
        enum jenjang "paket_a|paket_b|paket_c"
        string nama_program
        string kode_program
        integer total_semester
        text deskripsi
        boolean is_active
        timestamp created_at
    }

    ANGKATANS {
        bigint id PK
        bigint program_id FK
        string nama_angkatan
        string tahun_ajaran
        date tanggal_mulai
        date tanggal_selesai
        enum status "aktif|selesai|tutup"
        timestamp created_at
    }

    SISWA {
        bigint id PK
        bigint user_id FK
        bigint angkatan_id FK
        string nisn
        string nik
        string nama_lengkap
        string tempat_lahir
        date tanggal_lahir
        enum jenis_kelamin "L|P"
        text alamat
        string no_telepon
        string nama_orang_tua
        string telepon_orang_tua
        enum status "aktif|lulus|dropout|cuti"
        string foto
        timestamp created_at
    }

    TUTORS {
        bigint id PK
        bigint user_id FK
        string nip
        string nama_lengkap
        string spesialisasi
        string no_telepon
        string foto
        timestamp created_at
    }

    MATA_PELAJARANS {
        bigint id PK
        bigint program_id FK
        string kode_mapel
        string nama_mapel
        integer semester
        integer kkm
        integer jam_per_minggu
        timestamp created_at
    }

    KELAS {
        bigint id PK
        bigint angkatan_id FK
        bigint tutor_id FK
        string nama_kelas
        integer semester
        integer kapasitas
        timestamp created_at
    }

    KELAS_SISWA {
        bigint id PK
        bigint kelas_id FK
        bigint siswa_id FK
        enum status "aktif|pindah|keluar"
        timestamp created_at
    }

    JADWALS {
        bigint id PK
        bigint kelas_id FK
        bigint mata_pelajaran_id FK
        bigint tutor_id FK
        enum hari "senin|selasa|rabu|kamis|jumat|sabtu"
        time jam_mulai
        time jam_selesai
        string ruangan
        timestamp created_at
    }

    ABSENSI {
        bigint id PK
        bigint jadwal_id FK
        bigint siswa_id FK
        date tanggal
        enum status "hadir|sakit|izin|alpha"
        text keterangan
        string qr_token
        timestamp created_at
    }

    NILAIS {
        bigint id PK
        bigint siswa_id FK
        bigint mata_pelajaran_id FK
        bigint kelas_id FK
        integer semester
        decimal nilai_tugas
        decimal nilai_uts
        decimal nilai_uas
        decimal nilai_praktek
        decimal nilai_akhir
        enum predikat "A|B|C|D"
        boolean lulus
        timestamp created_at
    }

    RAPORTS {
        bigint id PK
        bigint siswa_id FK
        bigint kelas_id FK
        integer semester
        string tahun_ajaran
        decimal rata_rata
        integer peringkat
        integer total_hadir
        integer total_sakit
        integer total_izin
        integer total_alpha
        enum status_naik "naik|tinggal|lulus"
        string file_pdf
        timestamp generated_at
        timestamp created_at
    }

    SERTIFIKATS {
        bigint id PK
        bigint siswa_id FK
        bigint program_id FK
        string nomor_sertifikat
        date tanggal_terbit
        string file_pdf
        enum status "draft|terbit|revisi"
        timestamp created_at
    }

    PEMBAYARANS {
        bigint id PK
        bigint siswa_id FK
        string bulan
        integer tahun
        decimal nominal
        enum jenis "spp|seragam|buku|lainnya"
        enum status "lunas|belum|cicil"
        date tanggal_bayar
        string bukti_bayar
        timestamp created_at
    }

    PENGUMUMANS {
        bigint id PK
        bigint user_id FK
        string judul
        text isi
        enum target "semua|siswa|tutor|admin"
        boolean is_active
        timestamp created_at
    }

    USERS ||--o| SISWA : "memiliki"
    USERS ||--o| TUTORS : "memiliki"
    PROGRAMS ||--|{ ANGKATANS : "memiliki"
    ANGKATANS ||--|{ SISWA : "memiliki"
    PROGRAMS ||--|{ MATA_PELAJARANS : "memiliki"
    ANGKATANS ||--|{ KELAS : "memiliki"
    TUTORS ||--|{ KELAS : "mengajar"
    KELAS ||--|{ KELAS_SISWA : "memiliki"
    SISWA ||--|{ KELAS_SISWA : "terdaftar"
    KELAS ||--|{ JADWALS : "memiliki"
    MATA_PELAJARANS ||--|{ JADWALS : "termasuk"
    JADWALS ||--|{ ABSENSI : "memiliki"
    SISWA ||--|{ ABSENSI : "memiliki"
    SISWA ||--|{ NILAIS : "memiliki"
    MATA_PELAJARANS ||--|{ NILAIS : "dinilai"
    SISWA ||--|{ RAPORTS : "memiliki"
    SISWA ||--|{ SERTIFIKATS : "memiliki"
    SISWA ||--|{ PEMBAYARANS : "memiliki"
```

## 📋 Tabel Summary

| Tabel | Deskripsi | Relasi Utama |
|-------|-----------|--------------|
| users | Semua pengguna sistem | - |
| programs | Program Paket A, B, C | - |
| angkatans | Tahun angkatan tiap program | program |
| siswa | Data lengkap siswa | user, angkatan |
| tutors | Data tutor/pengajar | user |
| mata_pelajarans | Daftar mata pelajaran per program | program |
| kelas | Kelas per angkatan | angkatan, tutor |
| kelas_siswa | Siswa yang terdaftar di kelas | kelas, siswa |
| jadwals | Jadwal pelajaran | kelas, mapel, tutor |
| absensi | Rekap kehadiran | jadwal, siswa |
| nilais | Nilai per mata pelajaran | siswa, mapel |
| raports | Raport per semester | siswa, kelas |
| sertifikats | Sertifikat kelulusan | siswa, program |
| pembayarans | Administrasi pembayaran | siswa |
| pengumumans | Pengumuman sistem | user |
