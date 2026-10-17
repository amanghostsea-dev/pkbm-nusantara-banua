<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tugas;
use App\Models\TugasPengumpulan;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;

class TugasController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Tugas::with(['kelas', 'mataPelajaran', 'tutor'])
            ->withCount('pengumpulans')
            ->latest();

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('mata_pelajaran_id')) {
            $query->where('mata_pelajaran_id', $request->mata_pelajaran_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $tugasList = $query->paginate(15)->withQueryString();
        $kelasList = Kelas::where('is_active', true)->get(['id', 'nama_kelas']);
        $mapelList = MataPelajaran::where('is_active', true)->get(['id', 'nama_mapel', 'kode_mapel']);

        return Inertia::render('Admin/Tugas/Index', [
            'tugasList' => $tugasList,
            'kelasList' => $kelasList,
            'mapelList' => $mapelList,
            'filters'   => $request->only(['kelas_id', 'mata_pelajaran_id', 'search']),
        ]);
    }

    public function show(Tugas $tuga): Response
    {
        $tuga->load(['kelas.siswas', 'mataPelajaran', 'tutor']);
        $pengumpulans = TugasPengumpulan::with('siswa')
            ->where('tugas_id', $tuga->id)
            ->latest()
            ->get();

        return Inertia::render('Admin/Tugas/Show', [
            'tugas'        => $tuga,
            'pengumpulans' => $pengumpulans,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id'          => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'judul'             => 'required|string|max:255',
            'deskripsi'         => 'required|string',
            'deadline'          => 'required|date',
            'poin_maksimal'     => 'nullable|integer|min:10|max:100',
            'file_lampiran'     => 'nullable|file|max:20480',
        ]);

        $filePath = null;
        if ($request->hasFile('file_lampiran')) {
            $filePath = $request->file('file_lampiran')->store('tugas_attachments', 'public');
        }

        Tugas::create([
            'kelas_id'          => $validated['kelas_id'],
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'tutor_id'          => auth()->user()?->tutor?->id,
            'judul'             => $validated['judul'],
            'deskripsi'         => $validated['deskripsi'],
            'file_lampiran'     => $filePath ? "/storage/{$filePath}" : null,
            'deadline'          => $validated['deadline'],
            'poin_maksimal'     => $validated['poin_maksimal'] ?? 100,
            'status'            => 'aktif',
        ]);

        return redirect()->route('admin.tugas.index')->with('success', 'Tugas berhasil dibuat dan diumumkan ke siswa!');
    }

    public function grade(Request $request, Tugas $tuga, TugasPengumpulan $pengumpulan)
    {
        $validated = $request->validate([
            'nilai'    => 'required|integer|min:0|max:100',
            'feedback' => 'nullable|string',
        ]);

        $pengumpulan->update([
            'nilai'      => $validated['nilai'],
            'feedback'   => $validated['feedback'] ?? null,
            'status'     => 'dinilai',
            'dinilai_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Nilai dan evaluasi berhasil disimpan!');
    }

    public function destroy(Tugas $tuga)
    {
        $tuga->delete();
        return redirect()->route('admin.tugas.index')->with('success', 'Tugas berhasil dihapus!');
    }
}
