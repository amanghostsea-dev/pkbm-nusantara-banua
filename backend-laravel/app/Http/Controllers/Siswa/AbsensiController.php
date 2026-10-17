<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use Illuminate\Http\Request;

class AbsensiController extends Controller
{
    public function scan(Request $request)
    {
        $validated = $request->validate([
            'qr_token' => 'required|string',
        ]);

        $payload = json_decode(base64_decode($validated['qr_token']), true);
        if (!$payload || !isset($payload['jadwal_id'])) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid'], 422);
        }

        $siswa = auth()->user()->siswa;
        if (!$siswa) {
            return response()->json(['success' => false, 'message' => 'Profil siswa tidak ditemukan'], 404);
        }

        Absensi::updateOrCreate(
            [
                'jadwal_id' => $payload['jadwal_id'],
                'siswa_id' => $siswa->id,
                'tanggal' => now()->toDateString(),
            ],
            [
                'status' => 'hadir',
                'qr_token' => $validated['qr_token'],
                'waktu_absen' => now(),
            ]
        );

        return response()->json(['success' => true, 'message' => 'Absensi berhasil dicatat']);
    }
}
