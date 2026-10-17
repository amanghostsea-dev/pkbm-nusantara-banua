<script setup>
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
    isValid: { type: Boolean, default: false },
    raport: { type: Object, default: null },
    namaPkbm: { type: String, default: 'PKBM Nusantara Banua' },
    npsn: { type: String, default: 'P9908123' },
})
</script>

<template>
    <Head :title="isValid ? `Verifikasi Raport: ${raport?.nama_siswa}` : 'Verifikasi Raport — PKBM'" />

    <div class="min-h-screen bg-slate-900 text-slate-100 flex flex-col font-sans selection:bg-emerald-500 selection:text-white">
        <!-- Top Institutional Header -->
        <header class="border-b border-slate-800 bg-slate-950/80 backdrop-blur sticky top-0 z-50">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 h-16 sm:h-20 flex items-center justify-between">
                <Link href="/verifikasi" class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-xl shadow-md shadow-emerald-500/20">
                        🏛️
                    </div>
                    <div>
                        <div class="text-sm sm:text-base font-extrabold tracking-tight text-white">{{ namaPkbm }}</div>
                        <p class="text-xs text-slate-400">Verifikasi Raport Digital • NPSN: {{ npsn }}</p>
                    </div>
                </Link>

                <Link
                    href="/verifikasi"
                    class="px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 transition border border-slate-700"
                >
                    &larr; Halaman Depan
                </Link>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 py-8 sm:py-12">
            <div v-if="isValid && raport" class="space-y-6">
                <!-- Verified Banner -->
                <div class="bg-gradient-to-r from-emerald-950/80 via-emerald-900/60 to-slate-900 border-2 border-emerald-500/40 rounded-3xl p-6 sm:p-8 relative overflow-hidden shadow-2xl">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-emerald-500 text-slate-950 flex items-center justify-center text-3xl font-black shadow-lg shadow-emerald-500/30 flex-shrink-0 animate-pulse">
                                ✓
                            </div>
                            <div>
                                <span class="inline-block px-3 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 text-xs font-bold uppercase tracking-wider mb-1">
                                    Raport Resmi Terdaftar
                                </span>
                                <h1 class="text-xl sm:text-2xl font-black text-white">Lembar Hasil Belajar (Raport)</h1>
                                <p class="text-xs text-slate-300 mt-0.5">{{ raport.nama_kelas }} • Semester {{ raport.semester }} ({{ raport.tahun_ajaran }})</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Info Grid -->
                <div class="bg-slate-800/90 border border-slate-700 rounded-3xl p-6 sm:p-8 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-700/50">
                            <span class="text-xs text-slate-400">Nama Siswa</span>
                            <p class="text-base font-extrabold text-white mt-1 uppercase">{{ raport.nama_siswa }}</p>
                        </div>
                        <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-700/50">
                            <span class="text-xs text-slate-400">NISN</span>
                            <p class="text-base font-mono font-bold text-emerald-300 mt-1">{{ raport.nisn }}</p>
                        </div>
                        <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-700/50">
                            <span class="text-xs text-slate-400">Rata-Rata Nilai</span>
                            <p class="text-xl font-black text-emerald-400 mt-1">{{ raport.rata_rata }}</p>
                        </div>
                        <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-700/50">
                            <span class="text-xs text-slate-400">Kehadiran</span>
                            <p class="text-sm font-semibold text-white mt-1">
                                Hadir: {{ raport.total_hadir }} | Sakit: {{ raport.total_sakit }} | Izin: {{ raport.total_izin }} | Alpha: {{ raport.total_alpha }}
                            </p>
                        </div>
                    </div>

                    <div v-if="raport.catatan_wali_kelas" class="bg-slate-900/40 p-4 rounded-2xl border border-slate-700/40">
                        <span class="text-xs font-bold text-slate-400 uppercase">Catatan Tutor / Wali Kelas:</span>
                        <p class="text-sm text-slate-200 mt-1 italic">"{{ raport.catatan_wali_kelas }}"</p>
                    </div>
                </div>
            </div>

            <!-- NOT FOUND -->
            <div v-else class="bg-slate-800/80 border border-slate-700 p-8 rounded-3xl text-center space-y-4">
                <div class="text-4xl">⚠️</div>
                <h1 class="text-xl font-bold text-white">Data Raport Tidak Ditemukan</h1>
                <p class="text-xs text-slate-400 max-w-md mx-auto">
                    Data raport yang Anda cari tidak ditemukan pada sistem. Silakan periksa kembali tautan atau hubungi tutor/tata usaha.
                </p>
                <div>
                    <Link href="/verifikasi" class="inline-block px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-bold hover:bg-emerald-500 transition">
                        Kembali ke Pusat Verifikasi
                    </Link>
                </div>
            </div>
        </main>
    </div>
</template>
