<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Angkatan;
use App\Models\Kelas;
use App\Models\Program;
use App\Models\Sertifikat;
use App\Models\Setting;
use App\Models\Siswa;
use App\Models\Tutor;
use App\Services\PythonApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response as FacadeResponse;
use Inertia\Inertia;
use Inertia\Response;

class LaporanController extends Controller
{
    public function __construct(private PythonApiService $pythonApi) {}

    /**
     * Tampilkan halaman Pusat Laporan & Ekspor Data
     */
    public function index(Request $request): Response
    {
        $jenis   = $request->input('jenis', 'semester');
        $tahun   = $request->input('tahun', date('Y'));
        $program = $request->input('program', '');

        // Daftar Program Kesetaraan
        $programs = Program::select('id', 'nama_program', 'jenjang', 'kode_program')->get();

        // Daftar Tahun Ajaran
        $tahunList = Angkatan::select('tahun_ajaran')
            ->distinct()
            ->orderBy('tahun_ajaran', 'desc')
            ->pluck('tahun_ajaran')
            ->toArray();

        if (empty($tahunList)) {
            $tahunList = ['2024/2025', '2023/2024'];
        }

        // Statistik Ringkasan
        $stats = [
            'total_siswa'       => Siswa::count(),
            'siswa_aktif'       => Siswa::where('status', 'aktif')->count(),
            'total_tutor'       => Tutor::count(),
            'total_kelas'       => Kelas::count(),
            'total_sertifikat'  => Sertifikat::count(),
        ];

        // Query Preview Data Siswa
        $query = Siswa::with(['angkatan.program', 'nilais', 'absensi']);

        if (!empty($program)) {
            $query->whereHas('angkatan.program', function ($q) use ($program) {
                $q->where('jenjang', $program);
            });
        }

        if (!empty($tahun)) {
            $query->where(function ($q) use ($tahun) {
                $q->whereYear('created_at', $tahun)
                  ->orWhereHas('angkatan', function ($sub) use ($tahun) {
                      $sub->where('tahun_ajaran', 'like', "%{$tahun}%");
                  });
            });
        }

        $previewRows = $query->latest('id')->limit(12)->get()->map(function ($s) {
            return [
                'id'            => $s->id,
                'nisn'          => $s->nisn,
                'nama_lengkap'  => $s->nama_lengkap,
                'jenis_kelamin' => $s->jenis_kelamin,
                'program'       => $s->angkatan?->program?->nama_program ?? 'Paket Kesetaraan',
                'kehadiran'     => $s->getPersentaseKehadiran(),
                'nilai_rata'    => $s->getRataRataNilai(),
                'status'        => ucfirst($s->status),
            ];
        });

        return Inertia::render('Admin/Laporan/Index', [
            'programs'    => $programs,
            'tahunList'   => $tahunList,
            'stats'       => $stats,
            'previewRows' => $previewRows,
            'filters'     => [
                'jenis'   => $jenis,
                'tahun'   => (int) $tahun,
                'program' => $program,
            ],
            'pythonOnline'=> $this->pythonApi->isAvailable(),
        ]);
    }

    /**
     * API Handler untuk generate laporan resmi (mendukung Excel dan PDF)
     */
    public function generate(Request $request)
    {
        $validated = $request->validate([
            'jenis'   => 'required|in:bulanan,semester,tahunan,kelulusan,keuangan',
            'tahun'   => 'required',
            'program' => 'nullable|string',
            'format'  => 'required|in:excel,pdf',
        ]);

        $params = [
            'jenis'   => $validated['jenis'],
            'tahun'   => $validated['tahun'],
            'program' => $validated['program'] ?? '',
            'format'  => $validated['format'],
        ];

        // Format Excel langsung diarahkan ke export CSV/Excel
        if ($validated['format'] === 'excel') {
            return response()->json([
                'success'      => true,
                'message'      => 'Laporan format Excel resmi siap diunduh.',
                'download_url' => route('admin.laporan.export', $params),
                'format'       => 'excel',
            ]);
        }

        // Format PDF diarahkan ke cetak resmi dinas dengan Kop Surat
        return response()->json([
            'success'      => true,
            'message'      => 'Laporan PDF resmi dinas siap dicetak.',
            'download_url' => route('admin.laporan.cetak', $params),
            'print_url'    => route('admin.laporan.cetak', $params),
            'format'       => 'pdf',
        ]);
    }

    /**
     * Ekspor Laporan Excel (.csv dengan UTF-8 BOM untuk kompatibilitas penuh Microsoft Excel)
     */
    public function export(Request $request)
    {
        $jenis   = $request->input('jenis', 'semester');
        $tahun   = $request->input('tahun', date('Y'));
        $program = $request->input('program', '');

        $query = Siswa::with(['angkatan.program', 'nilais', 'absensi']);

        if (!empty($program)) {
            $query->whereHas('angkatan.program', function ($q) use ($program) {
                $q->where('jenjang', $program);
            });
        }

        if (!empty($tahun)) {
            $query->where(function ($q) use ($tahun) {
                $q->whereYear('created_at', $tahun)
                  ->orWhereHas('angkatan', function ($sub) use ($tahun) {
                      $sub->where('tahun_ajaran', 'like', "%{$tahun}%");
                  });
            });
        }

        $dataSiswa = $query->orderBy('nama_lengkap', 'asc')->get();

        $namaProgram = 'Semua Program';
        if (!empty($program)) {
            $progModel = Program::where('jenjang', $program)->first();
            if ($progModel) {
                $namaProgram = $progModel->nama_program;
            }
        }

        $timestamp = date('Ymd_His');
        $filename = "Laporan_{$jenis}_{$tahun}_{$timestamp}.csv";

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($dataSiswa, $jenis, $tahun, $namaProgram) {
            $file = fopen('php://output', 'w');
            // Tulis UTF-8 BOM agar Excel menampilkan huruf/karakter dengan benar
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Judul Dokumen
            fputcsv($file, ['PUSAT KEGIATAN BELAJAR MASYARAKAT (PKBM)']);
            fputcsv($file, [Setting::get('nama_pkbm', 'PKBM Nusantara Banua')]);
            fputcsv($file, ["LAPORAN RESMI: " . strtoupper($jenis)]);
            fputcsv($file, ["Tahun Ajaran / Periode", $tahun]);
            fputcsv($file, ["Program Kesetaraan", $namaProgram]);
            fputcsv($file, ["Waktu Unduh", date('d-m-Y H:i:s') . ' WIB']);
            fputcsv($file, []); // Baris Kosong

            // Header Tabel
            fputcsv($file, [
                'No',
                'NISN',
                'NIK',
                'Nama Lengkap',
                'Jenis Kelamin',
                'Tempat Lahir',
                'Tanggal Lahir',
                'Program',
                'Angkatan / Tahun',
                'Persentase Hadir (%)',
                'Rata-rata Nilai',
                'No. Telepon / WA',
                'Status Belajar',
            ]);

            // Baris Data
            foreach ($dataSiswa as $idx => $s) {
                fputcsv($file, [
                    $idx + 1,
                    $s->nisn ?? '-',
                    $s->nik ? "'".$s->nik : '-',
                    $s->nama_lengkap,
                    $s->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan',
                    $s->tempat_lahir ?? '-',
                    $s->tanggal_lahir ? $s->tanggal_lahir->format('d/m/Y') : '-',
                    $s->angkatan?->program?->nama_program ?? 'Kesetaraan',
                    $s->angkatan?->nama_angkatan ?? '-',
                    $s->getPersentaseKehadiran() . '%',
                    $s->getRataRataNilai(),
                    $s->no_telepon ?? '-',
                    ucfirst($s->status),
                ]);
            }

            fclose($file);
        };

        return FacadeResponse::stream($callback, 200, $headers);
    }

    /**
     * Tampilan Cetak Resmi Dinas (Dilengkapi Kop Surat & Tanda Tangan)
     */
    public function cetak(Request $request)
    {
        $jenis   = $request->input('jenis', 'semester');
        $tahun   = $request->input('tahun', date('Y'));
        $program = $request->input('program', '');

        $query = Siswa::with(['angkatan.program', 'nilais', 'absensi']);

        if (!empty($program)) {
            $query->whereHas('angkatan.program', function ($q) use ($program) {
                $q->where('jenjang', $program);
            });
        }

        if (!empty($tahun)) {
            $query->where(function ($q) use ($tahun) {
                $q->whereYear('created_at', $tahun)
                  ->orWhereHas('angkatan', function ($sub) use ($tahun) {
                      $sub->where('tahun_ajaran', 'like', "%{$tahun}%");
                  });
            });
        }

        $siswaModels = $query->orderBy('nama_lengkap', 'asc')->get();

        $namaProgram = 'Semua Program (Paket A, B, C)';
        if (!empty($program)) {
            $prog = Program::where('jenjang', $program)->first();
            if ($prog) $namaProgram = $prog->nama_program;
        }

        $titleMap = [
            'bulanan'   => 'Laporan Bulanan Kehadiran & Status Warga Belajar',
            'semester'  => 'Laporan Semester & Capaian Akademik Warga Belajar',
            'tahunan'   => 'Laporan Tahunan Evaluasi & Perkembangan Lembaga PKBM',
            'kelulusan' => 'Laporan Rekapitulasi Kelulusan & Penerbitan Ijazah',
            'keuangan'  => 'Laporan Administrasi & Rekapitulasi Pembayaran SPP',
        ];

        $title = $titleMap[$jenis] ?? 'Laporan Resmi Dinas PKBM';

        $siswaList = [];
        $totalKehadiran = 0;
        $totalNilai = 0;
        $totalL = 0;
        $totalP = 0;
        $totalAktif = 0;

        foreach ($siswaModels as $s) {
            $kehadiran = $s->getPersentaseKehadiran();
            $nilai     = $s->getRataRataNilai();

            if ($s->jenis_kelamin == 'L') $totalL++;
            if ($s->jenis_kelamin == 'P') $totalP++;
            if ($s->status == 'aktif') $totalAktif++;

            $totalKehadiran += $kehadiran;
            $totalNilai += $nilai;

            $siswaList[] = [
                'nisn'          => $s->nisn ?? '—',
                'nama_lengkap'  => $s->nama_lengkap,
                'jenis_kelamin' => $s->jenis_kelamin ?? '—',
                'program'       => $s->angkatan?->program?->nama_program ?? 'Kesetaraan',
                'kehadiran'     => $kehadiran,
                'nilai'         => $nilai,
                'status'        => ucfirst($s->status),
            ];
        }

        $count = count($siswaList) ?: 1;

        $summary = [
            'avg_kehadiran' => round($totalKehadiran / $count, 1),
            'avg_nilai'     => round($totalNilai / $count, 1),
            'total_l'       => $totalL,
            'total_p'       => $totalP,
            'total_aktif'   => $totalAktif,
        ];

        return view('laporan.cetak', [
            'title'        => $title,
            'nama_pkbm'    => Setting::get('nama_pkbm', 'PKBM Nusantara Banua'),
            'npsn'         => Setting::get('npsn', 'P9908123'),
            'kepala_pkbm'  => Setting::get('kepala_pkbm', 'Drs. H. Mulyadi, M.Pd'),
            'alamat'       => Setting::get('alamat', 'Jl. Pendidikan Kesetaraan No. 45, Banua'),
            'no_telepon'   => Setting::get('no_telepon', '(021) 7890-1234'),
            'email'        => Setting::get('email', 'info@pkbm-nusantarabanua.sch.id'),
            'tahun_ajaran' => $tahun ?: Setting::get('tahun_ajaran_aktif', '2024/2025'),
            'nama_program' => $namaProgram,
            'siswaList'    => $siswaList,
            'summary'      => $summary,
        ]);
    }

    /**
     * Download file laporan jika dihasilkan oleh engine Python
     */
    public function download($filename)
    {
        return $this->pythonApi->downloadLaporan($filename);
    }
}
