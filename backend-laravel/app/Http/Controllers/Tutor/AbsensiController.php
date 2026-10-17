<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Kelas;
use Inertia\Inertia;
use Inertia\Response;

class AbsensiController extends Controller
{
    public function index($kelasId): Response
    {
        $kelas = Kelas::with('siswas')->findOrFail($kelasId);
        $absensis = Absensi::whereHas('jadwal', fn($q) => $q->where('kelas_id', $kelasId))
            ->with('siswa')
            ->latest()
            ->paginate(30);

        return Inertia::render('Tutor/Absensi', [
            'kelas' => $kelas,
            'absensis' => $absensis,
        ]);
    }
}
