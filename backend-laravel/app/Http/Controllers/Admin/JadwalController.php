<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Jadwal;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JadwalController extends Controller
{
    public function index(): Response
    {
        $jadwals = Jadwal::with(['kelas', 'mataPelajaran', 'tutor'])->latest()->get();
        $kelasList = Kelas::where('is_active', true)->get();
        $mapelList = MataPelajaran::where('is_active', true)->get();
        $tutors = Tutor::all();

        return Inertia::render('Admin/Jadwal/Index', [
            'jadwals' => $jadwals,
            'kelasList' => $kelasList,
            'mapelList' => $mapelList,
            'tutors' => $tutors,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'tutor_id' => 'required|exists:tutors,id',
            'hari' => 'required|in:senin,selasa,rabu,kamis,jumat,sabtu',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required|after:jam_mulai',
            'ruangan' => 'nullable|string|max:50',
        ]);

        Jadwal::create($validated);
        return redirect()->back()->with('success', 'Jadwal berhasil ditambahkan.');
    }
}
