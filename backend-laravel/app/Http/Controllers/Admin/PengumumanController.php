<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PengumumanController extends Controller
{
    public function index(): Response
    {
        $pengumumans = Pengumuman::with('user')->latest()->get();
        return Inertia::render('Admin/Pengumuman/Index', [
            'pengumumans' => $pengumumans,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'target' => 'required|in:semua,siswa,tutor,admin',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['published_at'] = now();

        Pengumuman::create($validated);
        return redirect()->back()->with('success', 'Pengumuman berhasil diterbitkan.');
    }

    public function destroy(Pengumuman $pengumuman)
    {
        $pengumuman->delete();
        return redirect()->back()->with('success', 'Pengumuman berhasil dihapus.');
    }
}
