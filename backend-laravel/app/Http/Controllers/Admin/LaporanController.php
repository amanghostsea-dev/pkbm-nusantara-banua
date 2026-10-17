<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PythonApiService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LaporanController extends Controller
{
    public function __construct(private PythonApiService $pythonApi) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Laporan/Index');
    }

    public function generate(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|in:bulanan,semester,tahunan,kelulusan',
            'tahun' => 'required|integer',
            'program' => 'nullable|string',
            'format' => 'required|in:excel,pdf',
        ]);

        $result = $this->pythonApi->generateLaporanDinas(
            $validated['jenis'],
            $validated['tahun'],
            $validated['program'] ?? null,
            $validated['format']
        );

        return response()->json($result);
    }
}
