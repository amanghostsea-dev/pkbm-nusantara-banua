<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SiswaResource;
use App\Models\Siswa;
use App\Models\Angkatan;
use App\Services\SiswaService;
use App\Services\PythonApiService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller Manajemen Siswa PKBM
 * Handles CRUD siswa + integrasi dengan Python API untuk prediksi & raport
 */
class SiswaController extends Controller
{
    public function __construct(
        private SiswaService $siswaService,
        private PythonApiService $pythonApi,
    ) {}

    /**
     * Daftar semua siswa dengan filter & pagination
     */
    public function index(Request $request): Response
    {
        $filters = $request->only(['search', 'status', 'angkatan_id', 'program', 'per_page']);
        
        $siswa = $this->siswaService->getAll($filters);
        $angkatans = Angkatan::with('program')->aktif()->get();
        
        return Inertia::render('Admin/Siswa/Index', [
            'siswa' => SiswaResource::collection($siswa),
            'angkatans' => $angkatans,
            'filters' => $filters,
            'stats' => $this->siswaService->getStats(),
        ]);
    }

    /**
     * Form tambah siswa baru
     */
    public function create(): Response
    {
        return Inertia::render('Admin/Siswa/Create', [
            'angkatans' => Angkatan::with('program')->aktif()->get(),
        ]);
    }

    /**
     * Simpan data siswa baru
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'angkatan_id'       => 'required|exists:angkatans,id',
            'nisn'              => 'required|string|max:20|unique:siswa,nisn',
            'nik'               => 'nullable|string|max:20|unique:siswa,nik',
            'nama_lengkap'      => 'required|string|max:255',
            'tempat_lahir'      => 'required|string|max:100',
            'tanggal_lahir'     => 'required|date',
            'jenis_kelamin'     => 'required|in:L,P',
            'alamat'            => 'required|string',
            'no_telepon'        => 'nullable|string|max:20',
            'nama_orang_tua'    => 'required|string|max:255',
            'telepon_orang_tua' => 'nullable|string|max:20',
            'foto'              => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'create_user_account' => 'boolean',
        ]);

        $siswa = $this->siswaService->create($validated, $request->file('foto'));

        return redirect()
            ->route('admin.siswa.show', $siswa)
            ->with('success', "Siswa {$siswa->nama_lengkap} berhasil ditambahkan!");
    }

    /**
     * Detail profil siswa
     */
    public function show(Siswa $siswa): Response
    {
        $siswa->load([
            'angkatan.program',
            'kelasAktif.tutor',
            'nilais.mataPelajaran',
            'raports',
            'pembayarans',
        ]);

        return Inertia::render('Admin/Siswa/Show', [
            'siswa' => new SiswaResource($siswa),
            'rata_rata' => $siswa->getRataRataNilai(),
            'kehadiran' => $siswa->getPersentaseKehadiran(),
            'pembayaran_lancar' => $siswa->isPembayaranLancar(),
        ]);
    }

    /**
     * Form edit siswa
     */
    public function edit(Siswa $siswa): Response
    {
        return Inertia::render('Admin/Siswa/Edit', [
            'siswa' => new SiswaResource($siswa),
            'angkatans' => Angkatan::with('program')->aktif()->get(),
        ]);
    }

    /**
     * Update data siswa
     */
    public function update(Request $request, Siswa $siswa): RedirectResponse
    {
        $validated = $request->validate([
            'angkatan_id'       => 'required|exists:angkatans,id',
            'nisn'              => "required|string|max:20|unique:siswa,nisn,{$siswa->id}",
            'nama_lengkap'      => 'required|string|max:255',
            'tempat_lahir'      => 'required|string|max:100',
            'tanggal_lahir'     => 'required|date',
            'jenis_kelamin'     => 'required|in:L,P',
            'alamat'            => 'required|string',
            'status'            => 'required|in:aktif,lulus,dropout,cuti,pindah',
            'foto'              => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $this->siswaService->update($siswa, $validated, $request->file('foto'));

        return redirect()
            ->route('admin.siswa.show', $siswa)
            ->with('success', 'Data siswa berhasil diperbarui!');
    }

    /**
     * Soft delete siswa
     */
    public function destroy(Siswa $siswa): RedirectResponse
    {
        $this->siswaService->delete($siswa);
        
        return redirect()
            ->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil dihapus!');
    }

    /**
     * Generate raport PDF via Python API
     */
    public function generateRaport(Request $request, Siswa $siswa)
    {
        $request->validate([
            'semester' => 'required|integer|min:1|max:12',
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        $result = $this->pythonApi->generateRaport(
            siswa: $siswa,
            semester: $request->semester,
            kelasId: $request->kelas_id,
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'download_url' => $result['download_url'],
                'message' => 'Raport berhasil dibuat!',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Gagal membuat raport: ' . $result['message'],
        ], 500);
    }

    /**
     * Prediksi kelulusan siswa via Python ML API
     */
    public function prediksiKelulusan(Siswa $siswa)
    {
        $result = $this->pythonApi->prediksiKelulusan($siswa);
        
        return response()->json($result);
    }

    /**
     * Export data siswa ke Excel
     */
    public function export(Request $request)
    {
        $filters = $request->only(['status', 'angkatan_id', 'program']);
        
        return $this->siswaService->export($filters);
    }

    /**
     * Download template CSV import siswa
     */
    public function downloadTemplate()
    {
        return $this->siswaService->downloadTemplate();
    }

    /**
     * Import siswa dari Excel/CSV
     */
    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|max:5120',
            'angkatan_id' => 'nullable|exists:angkatans,id',
        ]);

        $result = $this->siswaService->import(
            $request->file('file'),
            $request->filled('angkatan_id') ? (int)$request->angkatan_id : null
        );

        $msg = "Import berhasil! {$result['imported']} siswa ditambahkan.";
        if (!empty($result['errors'])) {
            $msg .= ' Catatan: ' . implode(' ', array_slice($result['errors'], 0, 3));
        }

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', $msg);
    }

    /**
     * Tambah siswa secara kolektif (multi-row)
     */
    public function bulkStore(Request $request): RedirectResponse
    {
        $request->validate([
            'siswas' => 'required|array|min:1',
            'siswas.*.nisn' => 'required|string|max:20',
            'siswas.*.nama_lengkap' => 'required|string|max:255',
            'angkatan_id' => 'nullable|exists:angkatans,id',
        ]);

        $count = $this->siswaService->bulkStore(
            $request->input('siswas'),
            $request->filled('angkatan_id') ? (int)$request->angkatan_id : null
        );

        return redirect()
            ->route('admin.siswa.index')
            ->with('success', "Berhasil menambahkan {$count} siswa secara kolektif.");
    }
}
