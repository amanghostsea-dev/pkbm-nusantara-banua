<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MateriController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();
        $tutor = $user->tutor;

        $materis = Materi::with(['kelas', 'mataPelajaran'])
            ->latest()
            ->paginate(15);

        $kelasList = Kelas::where('is_active', true)->get(['id', 'nama_kelas']);
        $mapelList = MataPelajaran::where('is_active', true)->get(['id', 'nama_mapel', 'kode_mapel']);

        return Inertia::render('Tutor/Materi', [
            'materis'   => $materis,
            'kelasList' => $kelasList,
            'mapelList' => $mapelList,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kelas_id'          => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'judul'             => 'required|string|max:255',
            'deskripsi'         => 'nullable|string',
            'tipe'              => 'required|in:pdf,video,artikel,dokumen',
            'link_url'          => 'nullable|url',
            'file'              => 'nullable|file|max:20480',
        ]);

        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('materi_files', 'public');
        }

        Materi::create([
            'kelas_id'          => $validated['kelas_id'],
            'mata_pelajaran_id' => $validated['mata_pelajaran_id'],
            'tutor_id'          => auth()->user()?->tutor?->id,
            'judul'             => $validated['judul'],
            'deskripsi'         => $validated['deskripsi'] ?? null,
            'tipe'              => $validated['tipe'],
            'file_path'         => $filePath ? "/storage/{$filePath}" : null,
            'link_url'          => $validated['link_url'] ?? null,
            'is_published'      => true,
        ]);

        return redirect()->back()->with('success', 'Materi berhasil diunggah!');
    }
}
