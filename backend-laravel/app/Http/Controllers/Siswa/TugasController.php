<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use App\Models\TugasPengumpulan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class TugasController extends Controller
{
    public function index(Request $request): Response
    {
        $user = auth()->user();
        $siswa = $user->siswa ?? Siswa::first();
        $siswaId = $siswa?->id ?? 1;

        $kelasIds = $siswa ? $siswa->kelas()->pluck('kelas.id')->toArray() : [];

        $query = Tugas::with(['kelas', 'mataPelajaran', 'tutor'])
            ->with(['pengumpulans' => function ($q) use ($siswaId) {
                $q->where('siswa_id', $siswaId);
            }])
            ->where('status', '!=', 'draft');

        if (!empty($kelasIds)) {
            $query->whereIn('kelas_id', $kelasIds);
        }

        $tugasList = $query->latest('deadline')->paginate(12);

        // Format data with user submission status
        $tugasList->getCollection()->transform(function ($tugas) use ($siswaId) {
            $sub = $tugas->pengumpulans->first();
            $tugas->my_submission = $sub;
            $tugas->is_submitted = (bool) $sub;
            $tugas->is_graded = $sub && $sub->status === 'dinilai';
            $tugas->my_score = $sub?->nilai;
            $tugas->my_feedback = $sub?->feedback;
            $tugas->is_overdue = now()->greaterThan($tugas->deadline) && !$sub;
            return $tugas;
        });

        return Inertia::render('Siswa/Tugas', [
            'tugasList' => $tugasList,
            'siswa'     => $siswa,
        ]);
    }

    public function submit(Request $request, Tugas $tuga)
    {
        $user = auth()->user();
        $siswa = $user->siswa ?? Siswa::first();
        $siswaId = $siswa?->id ?? 1;

        $validated = $request->validate([
            'catatan_siswa' => 'nullable|string',
            'file_tugas'    => 'nullable|file|max:20480',
        ]);

        $filePath = null;
        if ($request->hasFile('file_tugas')) {
            $filePath = $request->file('file_tugas')->store('tugas_submissions', 'public');
        }

        $isOverdue = now()->greaterThan($tuga->deadline);

        TugasPengumpulan::updateOrCreate(
            ['tugas_id' => $tuga->id, 'siswa_id' => $siswaId],
            [
                'file_tugas'     => $filePath ? "/storage/{$filePath}" : null,
                'catatan_siswa'  => $validated['catatan_siswa'] ?? null,
                'tanggal_kumpul' => now(),
                'status'         => $isOverdue ? 'terlambat' : 'dikirim',
            ]
        );

        return redirect()->back()->with('success', 'Tugas Anda berhasil dikumpulkan!');
    }
}
