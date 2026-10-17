<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NilaiController extends Controller
{
    public function index(): Response
    {
        $nilais = Nilai::with(['siswa', 'mataPelajaran', 'kelas'])->latest()->paginate(20);
        $kelasList = Kelas::with(['siswas', 'angkatan.program'])->where('is_active', true)->get();
        $mapelList = MataPelajaran::where('is_active', true)->get();

        return Inertia::render('Admin/Nilai/Index', [
            'nilais' => $nilais,
            'kelasList' => $kelasList,
            'mapelList' => $mapelList,
        ]);
    }

    public function bulkStore(Request $request)
    {
        $validated = $request->validate([
            'nilais' => 'required|array',
            'nilais.*.siswa_id' => 'required|exists:siswa,id',
            'nilais.*.mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'nilais.*.kelas_id' => 'required|exists:kelas,id',
            'nilais.*.semester' => 'required|integer',
            'nilais.*.nilai_tugas' => 'nullable|numeric|min:0|max:100',
            'nilais.*.nilai_uts' => 'nullable|numeric|min:0|max:100',
            'nilais.*.nilai_uas' => 'nullable|numeric|min:0|max:100',
        ]);

        foreach ($validated['nilais'] as $item) {
            Nilai::updateOrCreate(
                [
                    'siswa_id' => $item['siswa_id'],
                    'mata_pelajaran_id' => $item['mata_pelajaran_id'],
                    'kelas_id' => $item['kelas_id'],
                    'semester' => $item['semester'],
                ],
                $item
            );
        }

        return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
    }
}
