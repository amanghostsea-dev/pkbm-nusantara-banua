<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna sistem (Hanya Admin Utama)
     */
    public function index(Request $request): Response
    {
        $query = User::query();

        // Pencarian Nama, Email, atau No. Telepon
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        // Filter Role
        if ($role = $request->input('role')) {
            if ($role === 'super_admin') {
                $query->where('role', 'admin')->where('is_super_admin', true);
            } elseif ($role === 'staff_admin') {
                $query->where('role', 'admin')->where('is_super_admin', false);
            } elseif (in_array($role, ['admin', 'tutor', 'siswa'])) {
                $query->where('role', $role);
            }
        }

        // Filter Status Aktif
        if ($request->has('status') && $request->input('status') !== '' && $request->input('status') !== null) {
            $query->where('is_active', (bool) $request->input('status'));
        }

        $users = $query->orderBy('is_super_admin', 'desc')->orderBy('id', 'desc')->paginate(10)->withQueryString();

        $stats = [
            'total'             => User::count(),
            'total_super_admin' => User::where('role', 'admin')->where('is_super_admin', true)->count(),
            'total_staff_admin' => User::where('role', 'admin')->where('is_super_admin', false)->count(),
            'total_tutor'       => User::where('role', 'tutor')->count(),
            'total_siswa'       => User::where('role', 'siswa')->count(),
            'total_active'      => User::where('is_active', true)->count(),
        ];

        return Inertia::render('Admin/Users/Index', [
            'users'   => $users,
            'filters' => [
                'search' => $request->input('search', ''),
                'role'   => $request->input('role', ''),
                'status' => $request->input('status', ''),
            ],
            'stats'   => $stats,
        ]);
    }

    /**
     * Tambah pengguna baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|string|email|max:255|unique:users,email',
            'password'       => 'required|string|min:6',
            'role'           => 'required|in:admin,tutor,siswa',
            'is_super_admin' => 'nullable|boolean',
            'phone'          => 'nullable|string|max:20',
            'is_active'      => 'boolean',
        ], [
            'name.required'     => 'Nama lengkap wajib diisi.',
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'email.unique'      => 'Email ini sudah terdaftar di sistem.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min'      => 'Kata sandi minimal 6 karakter.',
            'role.required'     => 'Pilih role pengguna.',
        ]);

        $isSuperAdmin = $validated['role'] === 'admin' ? $request->boolean('is_super_admin', false) : false;

        $user = User::create([
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'password'       => Hash::make($validated['password']),
            'role'           => $validated['role'],
            'is_super_admin' => $isSuperAdmin,
            'phone'          => $validated['phone'] ?? null,
            'is_active'      => $request->boolean('is_active', true),
        ]);

        $roleLabel = $isSuperAdmin ? 'Administrator Utama' : ($user->role === 'admin' ? 'Staf Admin / Tata Usaha' : $user->role);
        return redirect()->back()->with('success', "Pengguna {$user->name} berhasil ditambahkan sebagai {$roleLabel}.");
    }

    /**
     * Perbarui data pengguna
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'       => 'nullable|string|min:6',
            'role'           => 'required|in:admin,tutor,siswa',
            'is_super_admin' => 'nullable|boolean',
            'phone'          => 'nullable|string|max:20',
            'is_active'      => 'boolean',
        ], [
            'name.required'  => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.unique'   => 'Email ini sudah dipakai oleh pengguna lain.',
            'password.min'   => 'Kata sandi baru minimal 6 karakter.',
        ]);

        // Pencegahan: Administrator Utama tidak boleh menonaktifkan dirinya sendiri
        if ($user->id === auth()->id() && !$request->boolean('is_active', true)) {
            return redirect()->back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri yang sedang aktif digunakan.');
        }

        // Pencegahan: Administrator Utama tidak boleh menurunkan role dirinya sendiri
        if ($user->id === auth()->id() && ($validated['role'] !== 'admin' || !$request->boolean('is_super_admin', true))) {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah atau menurunkan role akun Anda sendiri.');
        }

        $isSuperAdmin = false;
        if ($validated['role'] === 'admin') {
            if ($user->id === auth()->id()) {
                $isSuperAdmin = true;
            } else {
                $isSuperAdmin = $request->boolean('is_super_admin', false);
            }
        }

        $updateData = [
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'role'           => $validated['role'],
            'is_super_admin' => $isSuperAdmin,
            'phone'          => $validated['phone'] ?? null,
            'is_active'      => $request->boolean('is_active', true),
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->back()->with('success', "Data pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * Hapus pengguna dari sistem
     */
    public function destroy(User $user)
    {
        // Pencegahan: Tidak boleh menghapus akun yang sedang dipakai login
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        // Pencegahan: Tidak boleh menghapus Administrator Utama utama
        if ($user->is_super_admin && User::where('is_super_admin', true)->count() <= 1) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus Administrator Utama satu-satunya dalam sistem.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->back()->with('success', "Akun pengguna {$userName} berhasil dihapus dari sistem.");
    }

    /**
     * Reset password cepat untuk pengguna
     */
    public function resetPassword(Request $request, User $user)
    {
        $newPassword = $request->input('new_password', 'password');
        
        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return redirect()->back()->with('success', "Kata sandi untuk {$user->name} berhasil di-reset menjadi: {$newPassword}");
    }

    /**
     * Toggle status aktif/nonaktif akun
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menonaktifkan akun sendiri.');
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $statusText = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Akun {$user->name} berhasil {$statusText}.");
    }
}
