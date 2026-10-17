<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sertifikat;
use App\Models\Siswa;
use App\Models\Program;
use App\Services\PythonApiService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SertifikatController extends Controller
{
    public function __construct(private PythonApiService $pythonApi) {}

    public function index(): Response
    {
        $sertifikats = Sertifikat::with(['siswa', 'program'])->latest()->paginate(20);
        $programs = Program::all();

        return Inertia::render('Admin/Sertifikat/Index', [
            'sertifikats' => $sertifikats,
            'programs' => $programs,
        ]);
    }

    public function generate(Sertifikat $sertifikat)
    {
        $result = $this->pythonApi->generateSertifikat($sertifikat);
        return redirect()->back()->with('success', 'Sertifikat berhasil dibuat.');
    }
}
