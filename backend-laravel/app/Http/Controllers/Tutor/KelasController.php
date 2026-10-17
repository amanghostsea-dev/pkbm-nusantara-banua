<?php

namespace App\Http\Controllers\Tutor;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use Inertia\Inertia;
use Inertia\Response;

class KelasController extends Controller
{
    public function index(): Response
    {
        $tutorId = auth()->user()->tutor?->id;
        $kelas = Kelas::where('tutor_id', $tutorId)->with(['angkatan.program', 'siswas'])->get();

        return Inertia::render('Tutor/Kelas', [
            'kelas' => $kelas,
        ]);
    }
}
