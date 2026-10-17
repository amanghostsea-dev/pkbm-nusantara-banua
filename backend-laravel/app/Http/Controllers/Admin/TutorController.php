<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class TutorController extends Controller
{
    public function index(): Response
    {
        $tutors = Tutor::with('user')->withCount(['kelas', 'jadwals'])->get();
        return Inertia::render('Admin/Tutor/Index', [
            'tutors' => $tutors,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lengkap' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'nip' => 'nullable|string|max:30|unique:tutors,nip',
            'spesialisasi' => 'nullable|string|max:255',
            'no_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $validated['nama_lengkap'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'tutor',
            'phone' => $validated['no_telepon'] ?? null,
        ]);

        Tutor::create([
            'user_id' => $user->id,
            'nip' => $validated['nip'] ?? null,
            'nama_lengkap' => $validated['nama_lengkap'],
            'spesialisasi' => $validated['spesialisasi'] ?? null,
            'no_telepon' => $validated['no_telepon'] ?? null,
            'alamat' => $validated['alamat'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Tutor berhasil didaftarkan.');
    }

    public function destroy(Tutor $tutor)
    {
        if ($tutor->user) {
            $tutor->user->delete();
        }
        $tutor->delete();
        return redirect()->back()->with('success', 'Tutor berhasil dihapus.');
    }
}
