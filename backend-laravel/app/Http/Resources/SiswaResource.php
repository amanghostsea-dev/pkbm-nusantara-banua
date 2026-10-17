<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SiswaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nisn' => $this->nisn,
            'nik' => $this->nik,
            'nama_lengkap' => $this->nama_lengkap,
            'tempat_lahir' => $this->tempat_lahir,
            'tanggal_lahir' => $this->tanggal_lahir?->format('Y-m-d'),
            'jenis_kelamin' => $this->jenis_kelamin,
            'alamat' => $this->alamat,
            'no_telepon' => $this->no_telepon,
            'status' => $this->status,
            'foto_url' => $this->foto_url,
            'usia' => $this->usia,
            'angkatan' => $this->whenLoaded('angkatan', function () {
                return [
                    'id' => $this->angkatan->id,
                    'nama_angkatan' => $this->angkatan->nama_angkatan,
                    'tahun_ajaran' => $this->angkatan->tahun_ajaran,
                    'program' => $this->angkatan->program ? [
                        'id' => $this->angkatan->program->id,
                        'nama_program' => $this->angkatan->program->nama_program,
                        'jenjang' => $this->angkatan->program->jenjang,
                    ] : null,
                ];
            }),
        ];
    }
}
