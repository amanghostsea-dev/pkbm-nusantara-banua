<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProgramController extends Controller
{
    public function index(): Response
    {
        $programs = Program::withCount(['angkatans', 'mataPelajarans'])->get();
        return Inertia::render('Admin/Program/Index', [
            'programs' => $programs,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenjang' => 'required|in:paket_a,paket_b,paket_c',
            'nama_program' => 'required|string|max:255',
            'kode_program' => 'required|string|max:20|unique:programs',
            'total_semester' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
        ]);

        Program::create($validated);
        return redirect()->back()->with('success', 'Program berhasil ditambahkan.');
    }

    public function update(Request $request, Program $program)
    {
        $validated = $request->validate([
            'nama_program' => 'required|string|max:255',
            'total_semester' => 'required|integer|min:1',
            'deskripsi' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $program->update($validated);
        return redirect()->back()->with('success', 'Program berhasil diperbarui.');
    }

    public function destroy(Program $program)
    {
        $program->delete();
        return redirect()->back()->with('success', 'Program berhasil dihapus.');
    }
}
