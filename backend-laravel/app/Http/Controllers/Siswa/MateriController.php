<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MateriController extends Controller
{
    public function index(Request $request): Response
    {
        $user = auth()->user();
        $siswa = $user->siswa ?? Siswa::first();

        $kelasIds = $siswa ? $siswa->kelas()->pluck('kelas_id')->toArray() : [];

        $query = Materi::with(['kelas', 'mataPelajaran', 'tutor'])
            ->where('is_published', true);

        if (!empty($kelasIds)) {
            $query->whereIn('kelas_id', $kelasIds);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $materis = $query->latest()->paginate(12)->withQueryString();

        return Inertia::render('Siswa/Materi', [
            'materis' => $materis,
            'filters' => $request->only(['search']),
        ]);
    }
}
