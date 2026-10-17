<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Raport;
use App\Models\Kelas;
use App\Services\PythonApiService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RaportController extends Controller
{
    public function __construct(private PythonApiService $pythonApi) {}

    public function index(): Response
    {
        $raports = Raport::with(['siswa', 'kelas'])->latest()->paginate(20);
        $kelasList = Kelas::all();

        return Inertia::render('Admin/Raport/Index', [
            'raports' => $raports,
            'kelasList' => $kelasList,
        ]);
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'kelas_id' => 'required|exists:kelas,id',
            'semester' => 'required|integer',
        ]);

        $siswa = \App\Models\Siswa::findOrFail($validated['siswa_id']);
        $result = $this->pythonApi->generateRaport($siswa, $validated['semester'], $validated['kelas_id']);

        return redirect()->back()->with('success', 'Raport berhasil diproses.');
    }

    public function download($filename)
    {
        return $this->pythonApi->downloadRaport($filename);
    }
}
