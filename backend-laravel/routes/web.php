<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// =======================================
// AUTH ROUTES (Laravel Breeze)
// =======================================
require __DIR__.'/auth.php';

// =======================================
// ROOT REDIRECT
// =======================================
Route::get('/', fn() => redirect()->route('dashboard'));

// =======================================
// PUBLIC VERIFIKASI IJAZAH & RAPORT ROUTES (No Login Required)
// =======================================
Route::get('/verifikasi', [\App\Http\Controllers\Public\VerifikasiController::class, 'index'])
    ->name('verifikasi.index');
Route::match(['get', 'post'], '/verifikasi/cari', [\App\Http\Controllers\Public\VerifikasiController::class, 'cari'])
    ->name('verifikasi.cari');
Route::get('/verifikasi/sertifikat/{nomor}', [\App\Http\Controllers\Public\VerifikasiController::class, 'sertifikat'])
    ->name('verifikasi.sertifikat')
    ->where('nomor', '.*');
Route::get('/verifikasi/raport/{id}', [\App\Http\Controllers\Public\VerifikasiController::class, 'raport'])
    ->name('verifikasi.raport');


// =======================================
// AUTHENTICATED ROUTES
// =======================================
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('/admin/dashboard', fn() => redirect()->route('dashboard'));

    // =======================================
    // PROFILE & PENGATURAN AKUN MANDIRI (SEMUA ROLE: ADMIN, TUTOR, SISWA)
    // =======================================
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])
        ->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])
        ->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])
        ->name('profile.password.update');

    // Route alias untuk navigasi setiap portal
    Route::get('/admin/profile', fn() => redirect()->route('profile.edit'));
    Route::get('/tutor/profile', fn() => redirect()->route('profile.edit'));
    Route::get('/siswa-portal/profile', fn() => redirect()->route('profile.edit'));

    // Python Analytics proxy for Dashboard
    Route::get('/api/analytics/dashboard', function(\App\Services\PythonApiService $pythonApi) {
        return response()->json($pythonApi->getDashboardAnalitik());
    });

    // Real-time AI Simulation for Dashboard
    Route::post('/api/analytics/simulate-prediction', function(\Illuminate\Http\Request $request) {
        $payload = [
            'siswa_id' => 999,
            'rata_rata_nilai' => (float) $request->input('rata_rata_nilai', 85.0),
            'persentase_kehadiran' => (float) $request->input('persentase_kehadiran', 90.0),
            'nilai_matematika' => (float) $request->input('nilai_matematika', 80.0),
            'nilai_bahasa_indonesia' => (float) $request->input('nilai_bahasa_indonesia', 88.0),
            'nilai_ipa' => (float) $request->input('nilai_ipa', 82.0),
            'jumlah_semester_sudah_ditempuh' => (int) $request->input('jumlah_semester_sudah_ditempuh', 3),
            'status_pembayaran_lancar' => (bool) $request->input('status_pembayaran_lancar', true),
            'usia' => (int) $request->input('usia', 18),
            'jenis_kelamin' => $request->input('jenis_kelamin', 'L'),
        ];

        try {
            $response = \Illuminate\Support\Facades\Http::baseUrl(config('services.python_api.url', 'http://127.0.0.1:8000'))
                ->timeout(10)
                ->post('/api/v1/prediksi/kelulusan', $payload);

            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Python ML Service offline',
                'message' => $e->getMessage(),
            ], 500);
        }
    });

    // ==================================
    // ADMIN ROUTES
    // ==================================
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

        // Manajemen Siswa
        Route::get('siswa/export', [\App\Http\Controllers\Admin\SiswaController::class, 'export'])
            ->name('siswa.export');
        Route::get('siswa/template-import', [\App\Http\Controllers\Admin\SiswaController::class, 'downloadTemplate'])
            ->name('siswa.template');
        Route::post('siswa/import', [\App\Http\Controllers\Admin\SiswaController::class, 'import'])
            ->name('siswa.import');
        Route::post('siswa/bulk', [\App\Http\Controllers\Admin\SiswaController::class, 'bulkStore'])
            ->name('siswa.bulk');
        Route::resource('siswa', \App\Http\Controllers\Admin\SiswaController::class);
        Route::post('siswa/{siswa}/raport', [\App\Http\Controllers\Admin\SiswaController::class, 'generateRaport'])
            ->name('siswa.raport.generate');
        Route::get('siswa/{siswa}/prediksi', [\App\Http\Controllers\Admin\SiswaController::class, 'prediksiKelulusan'])
            ->name('siswa.prediksi');

        // Manajemen Program
        Route::resource('programs', \App\Http\Controllers\Admin\ProgramController::class);
        
        // Manajemen Angkatan
        Route::resource('angkatans', \App\Http\Controllers\Admin\AngkatanController::class);

        // Manajemen Kelas
        Route::resource('kelas', \App\Http\Controllers\Admin\KelasController::class)->parameters(['kelas' => 'kelas']);
        Route::post('kelas/{kelas}/siswa', [\App\Http\Controllers\Admin\KelasController::class, 'addSiswa'])
            ->name('kelas.siswa.add');

        // Manajemen Tutor
        Route::resource('tutors', \App\Http\Controllers\Admin\TutorController::class);

        // Jadwal Pelajaran
        Route::resource('jadwals', \App\Http\Controllers\Admin\JadwalController::class);

        // Mata Pelajaran
        Route::resource('mata-pelajarans', \App\Http\Controllers\Admin\MataPelajaranController::class);

        // Nilai
        Route::resource('nilais', \App\Http\Controllers\Admin\NilaiController::class);
        Route::post('nilais/bulk', [\App\Http\Controllers\Admin\NilaiController::class, 'bulkStore'])
            ->name('nilais.bulk');

        // Raport
        Route::get('raports', [\App\Http\Controllers\Admin\RaportController::class, 'index'])
            ->name('raports.index');
        Route::post('raports/generate', [\App\Http\Controllers\Admin\RaportController::class, 'generate'])
            ->name('raports.generate');
        Route::get('raports/download/{filename}', [\App\Http\Controllers\Admin\RaportController::class, 'download'])
            ->name('raports.download');

        // Sertifikat
        Route::resource('sertifikats', \App\Http\Controllers\Admin\SertifikatController::class);
        Route::post('sertifikats/{sertifikat}/generate', [\App\Http\Controllers\Admin\SertifikatController::class, 'generate'])
            ->name('sertifikats.generate');

        // Pembayaran
        Route::resource('pembayarans', \App\Http\Controllers\Admin\PembayaranController::class);

        // Absensi
        Route::get('absensi', [\App\Http\Controllers\Admin\AbsensiController::class, 'index'])
            ->name('absensi.index');
        Route::post('absensi/scan', [\App\Http\Controllers\Admin\AbsensiController::class, 'scanQr'])
            ->name('absensi.scan');
        Route::post('absensi/face-scan', [\App\Http\Controllers\Admin\AbsensiController::class, 'scanFace'])
            ->name('absensi.face');
        Route::post('absensi/manual', [\App\Http\Controllers\Admin\AbsensiController::class, 'manual'])
            ->name('absensi.manual');
        Route::get('absensi/qr/{jadwal_id}', [\App\Http\Controllers\Admin\AbsensiController::class, 'generateQr'])
            ->name('absensi.qr');

        // Pengumuman
        Route::resource('pengumumans', \App\Http\Controllers\Admin\PengumumanController::class);

        // Laporan & Analytics
        Route::get('laporan', [\App\Http\Controllers\Admin\LaporanController::class, 'index'])
            ->name('laporan.index');
        Route::post('laporan/generate', [\App\Http\Controllers\Admin\LaporanController::class, 'generate'])
            ->name('laporan.generate');
        Route::get('laporan/export', [\App\Http\Controllers\Admin\LaporanController::class, 'export'])
            ->name('laporan.export');
        Route::get('laporan/cetak', [\App\Http\Controllers\Admin\LaporanController::class, 'cetak'])
            ->name('laporan.cetak');
        Route::get('laporan/download/{filename}', [\App\Http\Controllers\Admin\LaporanController::class, 'download'])
            ->name('laporan.download');

        // LMS: Materi & Modul Belajar
        Route::resource('materi', \App\Http\Controllers\Admin\MateriController::class);

        // LMS: Tugas & Evaluasi
        Route::resource('tugas', \App\Http\Controllers\Admin\TugasController::class);
        Route::post('tugas/{tuga}/grade/{pengumpulan}', [\App\Http\Controllers\Admin\TugasController::class, 'grade'])
            ->name('tugas.grade');

        // ===============================================
        // KHUSUS ADMINISTRATOR UTAMA (SUPER ADMIN ONLY)
        // ===============================================
        Route::middleware(['super_admin'])->group(function () {
            // Manajemen Pengguna (Users)
            Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
            Route::post('users/{user}/reset-password', [\App\Http\Controllers\Admin\UserController::class, 'resetPassword'])
                ->name('users.reset-password');
            Route::post('users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])
                ->name('users.toggle-status');

            // Settings PKBM
            Route::get('settings', [\App\Http\Controllers\Admin\SettingController::class, 'index'])
                ->name('settings.index');
            Route::post('settings', [\App\Http\Controllers\Admin\SettingController::class, 'update'])
                ->name('settings.update');
        });
    });

    // ==================================
    // TUTOR ROUTES
    // ==================================
    Route::middleware(['role:tutor'])->prefix('tutor')->name('tutor.')->group(function () {
        Route::get('dashboard', fn() => Inertia::render('Tutor/Dashboard'))->name('dashboard');
        Route::get('kelas', [\App\Http\Controllers\Tutor\KelasController::class, 'index'])->name('kelas.index');
        Route::get('absensi/{kelas_id}', [\App\Http\Controllers\Tutor\AbsensiController::class, 'index'])->name('absensi.index');
        Route::post('nilais/input', [\App\Http\Controllers\Tutor\NilaiController::class, 'store'])->name('nilais.store');
        
        // Tutor LMS
        Route::resource('materi', \App\Http\Controllers\Tutor\MateriController::class);
        Route::resource('tugas', \App\Http\Controllers\Tutor\TugasController::class);
        Route::post('tugas/{tuga}/grade/{pengumpulan}', [\App\Http\Controllers\Tutor\TugasController::class, 'grade'])->name('tugas.grade');
    });

    // ==================================
    // SISWA ROUTES
    // ==================================
    Route::middleware(['role:siswa'])->prefix('siswa-portal')->name('siswa.')->group(function () {
        Route::get('dashboard', fn() => Inertia::render('Siswa/Dashboard'))->name('dashboard');
        Route::get('nilai', fn() => Inertia::render('Siswa/Nilai'))->name('nilai');
        Route::get('absensi', fn() => Inertia::render('Siswa/Absensi'))->name('absensi');
        Route::get('raport', fn() => Inertia::render('Siswa/Raport'))->name('raport');
        Route::get('jadwal', fn() => Inertia::render('Siswa/Jadwal'))->name('jadwal');
        Route::get('pembayaran', fn() => Inertia::render('Siswa/Pembayaran'))->name('pembayaran');
        Route::post('absensi/qr-scan', [\App\Http\Controllers\Siswa\AbsensiController::class, 'scan'])->name('absensi.scan');

        // Siswa LMS
        Route::get('materi', [\App\Http\Controllers\Siswa\MateriController::class, 'index'])->name('materi.index');
        Route::get('tugas', [\App\Http\Controllers\Siswa\TugasController::class, 'index'])->name('tugas.index');
        Route::post('tugas/{tuga}/kumpul', [\App\Http\Controllers\Siswa\TugasController::class, 'submit'])->name('tugas.submit');
    });

});
