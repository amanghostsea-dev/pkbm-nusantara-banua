<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Angkatan;
use App\Models\Program;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AngkatanController extends Controller
{
    public function index(): Response
    {
        $angkatans = Angkatan::with('program')->withCount(['siswas', 'kelas'])->latest()->get();
        $programs = Program::where('is_active', true)->get();

        return Inertia::render('Admin/Angkatan/Index', [
            'angkatans' => $angkatans,
            'programs' => $programs,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'nama_angkatan' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:10',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'nullable|date|after:tanggal_mulai',
            'status' => 'required|in:aktif,selesai,tutup',
        ]);

        Angkatan::create($validated);
        return redirect()->back()->with('success', 'Angkatan berhasil ditambahkan.');
    }

    public function update(Request $request, Angkatan $angkatan)
    {
        $validated = $request->validate([
            'nama_angkatan' => 'required|string|max:255',
            'tahun_ajaran' => 'required|string|max:10',
            'status' => 'required|in:aktif,selesai,tutup',
        ]);

        $angkatan->update($validated);
        return redirect()->back()->with('success', 'Angkatan berhasil diperbarui.');
    }

    public function destroy(Angkatan $angkatan)
    {
        $angkatan->delete();
        return redirect()->back()->with('success', 'Angkatan berhasil dihapus.');
    }
}
