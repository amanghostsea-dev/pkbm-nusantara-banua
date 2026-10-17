<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Nilai;
use Illuminate\Http\Request;

class NilaiController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'kelas_id' => 'required|exists:kelas,id',
            'semester' => 'required|integer',
            'nilai_tugas' => 'nullable|numeric|min:0|max:100',
            'nilai_uts' => 'nullable|numeric|min:0|max:100',
            'nilai_uas' => 'nullable|numeric|min:0|max:100',
            'nilai_praktek' => 'nullable|numeric|min:0|max:100',
        ]);

        Nilai::updateOrCreate(
            [
                'siswa_id' => $validated['siswa_id'],
                'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
                'kelas_id' => $validated['kelas_id'],
                'semester' => $validated['semester'],
            ],
            $validated
        );

        return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
    }
}
