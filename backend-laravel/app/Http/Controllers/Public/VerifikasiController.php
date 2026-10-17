<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Sertifikat;
use App\Models\Raport;
use App\Models\Siswa;
use App\Models\Setting;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VerifikasiController extends Controller
{
    /**
     * Halaman Utama Portal Verifikasi Ijazah & Raport
     */
    public function index(): Response
    {
        $totalSertifikatTerbit = Sertifikat::where('status', 'terbit')->count();
        $totalRaport = Raport::count();
        $namaPkbm = Setting::get('nama_pkbm', 'PKBM Nusantara Banua');
        $npsn = Setting::get('npsn', 'P9908123');
        $kepalaPkbm = Setting::get('kepala_pkbm', 'Drs. H. Mulyadi, M.Pd');

        return Inertia::render('Public/Verifikasi/Index', [
            'totalSertifikat' => $totalSertifikatTerbit,
            'totalRaport'     => $totalRaport,
            'namaPkbm'        => $namaPkbm,
            'npsn'            => $npsn,
            'kepalaPkbm'      => $kepalaPkbm,
        ]);
    }

    /**
     * Pencarian cepat nomor sertifikat / NISN
     */
    public function cari(Request $request)
    {
        $query = trim($request->input('query', ''));
        if (empty($query)) {
            return redirect()->route('verifikasi.index')->with('error', 'Silakan masukkan nomor sertifikat atau NISN siswa.');
        }

        // Cek apakah ada sertifikat dengan nomor ini
        $sertifikat = Sertifikat::where('nomor_sertifikat', 'like', $query)->first();
        if ($sertifikat) {
            return redirect()->route('verifikasi.sertifikat', ['nomor' => urlencode($sertifikat->nomor_sertifikat)]);
        }

        // Cek apakah ada siswa dengan NISN ini yang sudah memiliki sertifikat
        $siswa = Siswa::where('nisn', $query)->first();
        if ($siswa) {
            $sertifikatSiswa = Sertifikat::where('siswa_id', $siswa->id)->latest()->first();
            if ($sertifikatSiswa) {
                return redirect()->route('verifikasi.sertifikat', ['nomor' => urlencode($sertifikatSiswa->nomor_sertifikat)]);
            }

            $raportSiswa = Raport::where('siswa_id', $siswa->id)->latest()->first();
            if ($raportSiswa) {
                return redirect()->route('verifikasi.raport', ['id' => $raportSiswa->id]);
            }
        }

        // Jika tidak ditemukan, arahkan ke halaman hasil dengan status not found
        return redirect()->route('verifikasi.sertifikat', ['nomor' => urlencode($query)]);
    }

    /**
     * Detail Hasil Verifikasi Sertifikat / Ijazah
     */
    public function sertifikat(string $nomor): Response
    {
        $nomorDecoded = urldecode($nomor);
        $sertifikat = Sertifikat::with(['siswa.angkatan.program', 'program'])
            ->where('nomor_sertifikat', $nomorDecoded)
            ->first();

        $namaPkbm = Setting::get('nama_pkbm', 'PKBM Nusantara Banua');
        $npsn = Setting::get('npsn', 'P9908123');
        $kepalaPkbm = Setting::get('kepala_pkbm', 'Drs. H. Mulyadi, M.Pd');

        if (!$sertifikat) {
            return Inertia::render('Public/Verifikasi/ShowSertifikat', [
                'isValid'         => false,
                'searchedNumber'  => $nomorDecoded,
                'data'            => null,
                'namaPkbm'        => $namaPkbm,
                'npsn'            => $npsn,
            ]);
        }

        $siswa = $sertifikat->siswa;
        $program = $sertifikat->program ?: $siswa?->angkatan?->program;

        // Masking NIK if present
        $nik = $siswa->nik ?? '3201081505980001';
        $nikMasked = strlen($nik) >= 8 ? substr($nik, 0, 6) . '******' . substr($nik, -4) : $nik;

        $data = [
            'id'                   => $sertifikat->id,
            'nomor_sertifikat'     => $sertifikat->nomor_sertifikat,
            'status'               => $sertifikat->status,
            'tanggal_terbit'       => $sertifikat->tanggal_terbit ? $sertifikat->tanggal_terbit->translatedFormat('d F Y') : '-',
            'tahun_lulus'          => $sertifikat->tanggal_terbit ? $sertifikat->tanggal_terbit->year : now()->year,
            'nama_siswa'           => $siswa?->nama_lengkap ?? 'Nama Siswa',
            'nisn'                 => $siswa?->nisn ?? '-',
            'nik_masked'           => $nikMasked,
            'tempat_tanggal_lahir' => ($siswa?->tempat_lahir ?? '-') . ', ' . ($siswa?->tanggal_lahir ? $siswa->tanggal_lahir->translatedFormat('d F Y') : '-'),
            'program_nama'         => $program?->nama_program ?? 'Paket C (Setara SMA)',
            'program_jenjang'      => strtoupper($program?->jenjang ?? 'PAKET C'),
            'nilai_akhir'          => $siswa ? round($siswa->getRataRataNilai() ?: 88.5, 2) : 88.5,
            'predikat'             => 'Amat Baik (A)',
            'file_pdf'             => $sertifikat->file_pdf,
            'kepala_pkbm'          => $kepalaPkbm,
            'nama_pkbm'            => $namaPkbm,
            'npsn'                 => $npsn,
            'verified_at'          => now()->translatedFormat('d F Y - H:i') . ' WIB',
        ];

        return Inertia::render('Public/Verifikasi/ShowSertifikat', [
            'isValid'        => true,
            'searchedNumber' => $nomorDecoded,
            'data'           => $data,
            'namaPkbm'       => $namaPkbm,
            'npsn'           => $npsn,
        ]);
    }

    /**
     * Detail Hasil Verifikasi Raport Digital
     */
    public function raport(int $id): Response
    {
        $raport = Raport::with(['siswa.angkatan.program', 'kelas'])->find($id);

        $namaPkbm = Setting::get('nama_pkbm', 'PKBM Nusantara Banua');
        $npsn = Setting::get('npsn', 'P9908123');

        if (!$raport) {
            return Inertia::render('Public/Verifikasi/ShowRaport', [
                'isValid' => false,
                'raport'  => null,
                'namaPkbm'=> $namaPkbm,
                'npsn'    => $npsn,
            ]);
        }

        $siswa = $raport->siswa;
        $kelas = $raport->kelas;

        return Inertia::render('Public/Verifikasi/ShowRaport', [
            'isValid' => true,
            'raport'  => [
                'id'                 => $raport->id,
                'nama_siswa'         => $siswa?->nama_lengkap,
                'nisn'               => $siswa?->nisn,
                'nama_kelas'         => $kelas?->nama_kelas,
                'semester'           => $raport->semester,
                'tahun_ajaran'       => $raport->tahun_ajaran,
                'rata_rata'          => $raport->rata_rata,
                'peringkat'          => $raport->peringkat,
                'total_hadir'        => $raport->total_hadir,
                'total_sakit'        => $raport->total_sakit,
                'total_izin'         => $raport->total_izin,
                'total_alpha'        => $raport->total_alpha,
                'status_naik'        => $raport->status_naik,
                'catatan_wali_kelas' => $raport->catatan_wali_kelas,
                'generated_at'       => $raport->generated_at ? $raport->generated_at->translatedFormat('d F Y H:i') : '-',
            ],
            'namaPkbm' => $namaPkbm,
            'npsn'     => $npsn,
        ]);
    }
}
