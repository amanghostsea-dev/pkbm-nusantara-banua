<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;

class MateriController extends Controller
{
    public function index(Request $request): Response
    {
        $query = Materi::with(['kelas', 'mataPelajaran', 'tutor'])->latest();

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

        $materis = $query->paginate(15)->withQueryString();
        $kelasList = Kelas::where('is_active', true)->get(['id', 'nama_kelas']);
        $mapelList = MataPelajaran::where('is_active', true)->get(['id', 'nama_mapel', 'kode_mapel']);

        return Inertia::render('Admin/Materi/Index', [
            'materis'   => $materis,
            'kelasList' => $kelasList,
            'mapelList' => $mapelList,
            'filters'   => $request->only(['kelas_id', 'mata_pelajaran_id', 'search']),
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
            'file'              => 'nullable|file|max:20480', // max 20MB
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

        return redirect()->back()->with('success', 'Modul materi berhasil ditambahkan!');
    }

    public function destroy(Materi $materi)
    {
        if ($materi->file_path) {
            $relative = str_replace('/storage/', '', $materi->file_path);
            Storage::disk('public')->delete($relative);
        }

        $materi->delete();
        return redirect()->back()->with('success', 'Modul materi berhasil dihapus!');
    }
}
