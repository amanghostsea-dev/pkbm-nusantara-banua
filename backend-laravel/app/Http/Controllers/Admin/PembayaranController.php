<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PembayaranController extends Controller
{
    public function index(): Response
    {
        $pembayarans = Pembayaran::with(['siswa', 'penerima'])->latest()->paginate(20);
        return Inertia::render('Admin/Pembayaran/Index', [
            'pembayarans' => $pembayarans,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'bulan' => 'required|string',
            'tahun' => 'required|integer',
            'nominal' => 'required|numeric|min:0',
            'jenis' => 'required|in:spp,seragam,buku,lainnya',
            'status' => 'required|in:lunas,belum,cicil',
            'tanggal_bayar' => 'nullable|date',
        ]);

        $validated['diterima_oleh'] = auth()->id();
        Pembayaran::create($validated);

        return redirect()->back()->with('success', 'Pembayaran berhasil dicatat.');
    }
}
