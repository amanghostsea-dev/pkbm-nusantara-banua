<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Tampilkan formulir pengaturan profil dan kata sandi pengguna mandiri.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();

        // Muat relasi siswa atau tutor jika ada
        if ($user->role === 'siswa') {
            $user->load(['siswa.angkatan.program']);
        } elseif ($user->role === 'tutor') {
            $user->load(['tutor']);
        }

        return Inertia::render('Profile/Edit', [
            'user' => $user,
            'status' => session('status'),
            'success' => session('success'),
            'error' => session('error'),
        ]);
    }

    /**
     * Perbarui informasi akun mandiri (Nama, Email, dan No. Telepon).
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:20'],
        ], [
            'name.required'  => 'Nama lengkap wajib diisi.',
            'name.max'       => 'Nama lengkap maksimal 255 karakter.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email'    => 'Format alamat email tidak valid.',
            'email.unique'   => 'Alamat email ini sudah terdaftar oleh pengguna lain.',
            'phone.max'      => 'Nomor telepon maksimal 20 digit.',
        ]);

        $user->update([
            'name'  => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        // Sinkronisasi ke profil Tutor jika akun adalah Tutor
        if ($user->role === 'tutor' && $user->tutor) {
            $user->tutor->update([
                'nama_lengkap' => $validated['name'],
                'no_telepon'   => $validated['phone'] ?? $user->tutor->no_telepon,
            ]);
        }

        // Sinkronisasi ke profil Siswa jika akun adalah Siswa
        if ($user->role === 'siswa' && $user->siswa) {
            $user->siswa->update([
                'nama_lengkap' => $validated['name'],
                'no_telepon'   => $validated['phone'] ?? $user->siswa->no_telepon,
            ]);
        }

        return redirect()->route('profile.edit')->with('success', 'Informasi profil dan akun Anda berhasil diperbarui!');
    }

    /**
     * Perbarui kata sandi mandiri oleh pengguna.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password'         => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
            'password.required'         => 'Kata sandi baru wajib diisi.',
            'password.min'              => 'Kata sandi baru minimal 6 karakter.',
            'password.confirmed'        => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        // Verifikasi kata sandi lama
        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors([
                'current_password' => 'Kata sandi saat ini yang Anda masukkan salah.',
            ]);
        }

        // Simpan kata sandi baru
        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile.edit')->with('success', 'Kata sandi Anda berhasil diperbarui! Gunakan kata sandi baru untuk login berikutnya.');
    }
}
