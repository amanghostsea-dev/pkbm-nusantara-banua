<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use App\Models\Program;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MataPelajaranController extends Controller
{
    public function index(): Response
    {
        $mapelList = MataPelajaran::with('program')->latest()->get();
        $programs = Program::where('is_active', true)->get();

        return Inertia::render('Admin/MataPelajaran/Index', [
            'mapelList' => $mapelList,
            'programs' => $programs,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'kode_mapel' => 'required|string|max:10|unique:mata_pelajarans',
            'nama_mapel' => 'required|string|max:255',
            'semester' => 'required|integer|min:1|max:6',
            'kkm' => 'required|integer|min:0|max:100',
            'jam_per_minggu' => 'required|integer|min:1',
        ]);

        MataPelajaran::create($validated);
        return redirect()->back()->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }
}
