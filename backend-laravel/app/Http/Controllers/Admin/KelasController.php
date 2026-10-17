<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Angkatan;
use App\Models\Tutor;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KelasController extends Controller
{
    public function index(): Response
    {
        $kelasList = Kelas::with(['angkatan.program', 'tutor'])->withCount('siswas')->latest()->get();
        $angkatans = Angkatan::aktif()->get();
        $tutors = Tutor::all();

        return Inertia::render('Admin/Kelas/Index', [
            'kelasList' => $kelasList,
            'angkatans' => $angkatans,
            'tutors' => $tutors,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'angkatan_id' => 'required|exists:angkatans,id',
            'tutor_id' => 'nullable|exists:tutors,id',
            'nama_kelas' => 'required|string|max:50',
            'semester' => 'required|integer|min:1|max:6',
            'kapasitas' => 'required|integer|min:5|max:100',
        ]);

        Kelas::create($validated);
        return redirect()->back()->with('success', 'Kelas berhasil dibuat.');
    }

    public function show(Kelas $kelas): Response
    {
        $kelas->load(['angkatan.program', 'tutor', 'siswas']);
        $availableSiswa = Siswa::where('angkatan_id', $kelas->angkatan_id)
            ->where('status', 'aktif')
            ->whereDoesntHave('kelas', fn($q) => $q->where('kelas_id', $kelas->id))
            ->get();

        return Inertia::render('Admin/Kelas/Show', [
            'kelas' => $kelas,
            'availableSiswa' => $availableSiswa,
        ]);
    }

    public function addSiswa(Request $request, Kelas $kelas)
    {
        $validated = $request->validate([
            'siswa_id' => 'nullable|exists:siswa,id',
            'siswa_ids' => 'nullable|array',
            'siswa_ids.*' => 'exists:siswa,id',
        ]);

        $ids = $validated['siswa_ids'] ?? ($validated['siswa_id'] ? [$validated['siswa_id']] : []);
        $count = 0;
        foreach ($ids as $id) {
            if (!$kelas->siswas()->where('siswa_id', $id)->exists()) {
                $kelas->siswas()->attach($id, [
                    'status' => 'aktif',
                    'tanggal_masuk' => now()->toDateString(),
                ]);
                $count++;
            }
        }

        return redirect()->back()->with('success', "{$count} siswa berhasil dimasukkan ke kelas secara kolektif.");
    }
}
