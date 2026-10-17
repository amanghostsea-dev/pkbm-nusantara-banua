<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Program;
use App\Models\Tutor;
use App\Models\Pengumuman;
use App\Services\SiswaService;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __construct(private SiswaService $siswaService) {}

    public function index()
    {
        $user = auth()->user();
        if ($user && $user->role === 'siswa') {
            return redirect()->route('siswa.dashboard');
        } elseif ($user && $user->role === 'tutor') {
            return redirect()->route('tutor.dashboard');
        }

        $stats = $this->siswaService->getStats();
        $recentSiswa = Siswa::with('angkatan.program')
            ->latest()
            ->take(5)
            ->get();

        $pengumumans = Pengumuman::latest()
            ->take(4)
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'judul' => $p->judul,
                'target' => ucfirst($p->target),
                'created_at_human' => $p->created_at->diffForHumans(),
            ]);

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentSiswa' => $recentSiswa,
            'pengumumans' => $pengumumans,
        ]);
    }
}
