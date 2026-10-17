<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'

const props = defineProps({
    isValid: { type: Boolean, default: false },
    searchedNumber: { type: String, default: '' },
    data: { type: Object, default: null },
    namaPkbm: { type: String, default: 'PKBM Nusantara Banua' },
    npsn: { type: String, default: 'P9908123' },
})

const copied = ref(false)

function copyLink() {
    navigator.clipboard.writeText(window.location.href)
    copied.value = true
    setTimeout(() => { copied.value = false }, 3000)
}

const newQuery = ref('')
function searchAgain() {
    if (!newQuery.value.trim()) return
    router.post('/verifikasi/cari', { query: newQuery.value.trim() })
}
</script>

<template>
    <Head :title="isValid ? `Verifikasi Ijazah: ${data?.nama_siswa} — ${data?.nomor_sertifikat}` : 'Verifikasi Tidak Ditemukan — PKBM'" />

    <div class="min-h-screen bg-slate-900 text-slate-100 flex flex-col font-sans selection:bg-emerald-500 selection:text-white">
        <!-- Top Institutional Header -->
        <header class="border-b border-slate-800 bg-slate-950/80 backdrop-blur sticky top-0 z-50">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 h-16 sm:h-20 flex items-center justify-between">
                <Link href="/verifikasi" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-xl shadow-md shadow-emerald-500/20 group-hover:scale-105 transition">
                        🏛️
                    </div>
                    <div>
                        <div class="text-sm sm:text-base font-extrabold tracking-tight text-white flex items-center gap-2">
                            <span>{{ namaPkbm }}</span>
                        </div>
                        <p class="text-xs text-slate-400">Sistem Verifikasi Ijazah Digital • NPSN: {{ npsn }}</p>
                    </div>
                </Link>

                <div class="flex items-center gap-2.5">
                    <Link
                        href="/verifikasi"
                        class="px-3.5 py-1.5 text-xs font-semibold rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 transition border border-slate-700 flex items-center gap-1.5"
                    >
                        <span>&larr;</span>
                        <span>Cari Lainnya</span>
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 max-w-4xl w-full mx-auto px-4 sm:px-6 py-8 sm:py-12">
            <!-- CASE 1: VALID CERTIFICATE FOUND -->
            <div v-if="isValid && data" class="space-y-6">
                <!-- Verified Status Banner -->
                <div class="bg-gradient-to-r from-emerald-950/80 via-emerald-900/60 to-slate-900 border-2 border-emerald-500/40 rounded-3xl p-6 sm:p-8 relative overflow-hidden shadow-2xl shadow-emerald-950/50">
                    <div class="absolute -right-8 -top-8 w-44 h-44 bg-emerald-500/10 rounded-full blur-2xl pointer-events-none" />

                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-6 relative z-10">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-emerald-500 text-slate-950 flex items-center justify-center text-3xl font-black shadow-lg shadow-emerald-500/30 flex-shrink-0 animate-pulse">
                                ✓
                            </div>
                            <div>
                                <span class="inline-block px-3 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-500/30 text-emerald-400 text-xs font-bold uppercase tracking-wider mb-1">
                                    Dokumen Terverifikasi Asli
                                </span>
                                <h1 class="text-xl sm:text-2xl font-black text-white">Sertifikat Kelulusan Resmi</h1>
                                <p class="text-xs text-slate-300 mt-0.5">Tercatat dalam Pangkalan Data Kelulusan PKBM & Kemdikbud</p>
                            </div>
                        </div>

                        <!-- Certificate Number Pill -->
                        <div class="bg-slate-950/80 border border-emerald-500/30 px-4 py-2.5 rounded-2xl text-right sm:text-right w-full sm:w-auto">
                            <span class="block text-[10px] text-slate-400 uppercase font-semibold">Nomor Registrasi</span>
                            <span class="text-sm font-mono font-bold text-emerald-400">{{ data.nomor_sertifikat }}</span>
                        </div>
                    </div>
                </div>

                <!-- Main Verification Certificate Card -->
                <div class="bg-slate-800/90 border border-slate-700/80 rounded-3xl overflow-hidden shadow-xl">
                    <!-- Certificate Header Bar -->
                    <div class="bg-slate-950/60 px-6 py-4 border-b border-slate-700/80 flex flex-wrap items-center justify-between gap-3 text-xs">
                        <span class="text-slate-400">
                            Waktu Verifikasi Akses: <strong class="text-slate-200">{{ data.verified_at }}</strong>
                        </span>
                        <div class="flex items-center gap-2">
                            <button
                                @click="copyLink"
                                class="px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg border border-slate-600 transition flex items-center gap-1.5"
                            >
                                <span>📋</span>
                                <span>{{ copied ? 'Tautan Tersalin!' : 'Salin Tautan Resmi' }}</span>
                            </button>
                            <button
                                onclick="window.print()"
                                class="px-3 py-1 bg-slate-800 hover:bg-slate-700 text-slate-300 rounded-lg border border-slate-600 transition flex items-center gap-1.5"
                            >
                                <span>🖨️</span>
                                <span>Cetak Bukti</span>
                            </button>
                        </div>
                    </div>

                    <!-- Identity & Academic Details Grid -->
                    <div class="p-6 sm:p-8 space-y-8">
                        <!-- Student Profile Row -->
                        <div>
                            <h2 class="text-xs font-bold uppercase tracking-wider text-emerald-400 mb-4 flex items-center gap-2">
                                <span>👤</span>
                                <span>Identitas Warga Belajar (Lulusan)</span>
                            </h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-700/50">
                                    <span class="text-xs text-slate-400">Nama Lengkap Siswa</span>
                                    <p class="text-base font-extrabold text-white mt-1 uppercase">{{ data.nama_siswa }}</p>
                                </div>
                                <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-700/50">
                                    <span class="text-xs text-slate-400">Nomor Induk Siswa Nasional (NISN)</span>
                                    <p class="text-base font-mono font-bold text-emerald-300 mt-1">{{ data.nisn }}</p>
                                </div>
                                <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-700/50">
                                    <span class="text-xs text-slate-400">Nomor Induk Kependudukan (NIK)</span>
                                    <p class="text-sm font-mono text-slate-300 mt-1">{{ data.nik_masked }} <span class="text-[10px] text-slate-500">(Disamarkan)</span></p>
                                </div>
                                <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-700/50">
                                    <span class="text-xs text-slate-400">Tempat, Tanggal Lahir</span>
                                    <p class="text-sm font-semibold text-slate-200 mt-1">{{ data.tempat_tanggal_lahir }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Academic Graduation Row -->
                        <div>
                            <h2 class="text-xs font-bold uppercase tracking-wider text-teal-400 mb-4 flex items-center gap-2">
                                <span>🎓</span>
                                <span>Keterangan Kelulusan & Program Kesetaraan</span>
                            </h2>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-700/50">
                                    <span class="text-xs text-slate-400">Program Kesetaraan</span>
                                    <p class="text-sm font-bold text-white mt-1">{{ data.program_nama }}</p>
                                </div>
                                <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-700/50">
                                    <span class="text-xs text-slate-400">Tahun Pelajaran / Lulus</span>
                                    <p class="text-sm font-bold text-white mt-1">{{ data.tahun_lulus }}</p>
                                </div>
                                <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-700/50">
                                    <span class="text-xs text-slate-400">Nilai Akhir & Predikat</span>
                                    <p class="text-sm font-bold text-emerald-400 mt-1">{{ data.nilai_akhir }} ({{ data.predikat }})</p>
                                </div>
                            </div>
                        </div>

                        <!-- Institutional Validity Row -->
                        <div>
                            <h2 class="text-xs font-bold uppercase tracking-wider text-cyan-400 mb-4 flex items-center gap-2">
                                <span>🏛️</span>
                                <span>Kelembagaan & Pejabat Penerbit Dokumen</span>
                            </h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-700/50">
                                    <span class="text-xs text-slate-400">Lembaga Penyelenggara</span>
                                    <p class="text-sm font-semibold text-white mt-1">{{ data.nama_pkbm }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">NPSN: {{ data.npsn }}</p>
                                </div>
                                <div class="bg-slate-900/60 p-4 rounded-2xl border border-slate-700/50">
                                    <span class="text-xs text-slate-400">Kepala PKBM / Penandatangan</span>
                                    <p class="text-sm font-semibold text-white mt-1">{{ data.kepala_pkbm }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Diterbitkan: {{ data.tanggal_terbit }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action Bar -->
                    <div class="bg-slate-950/80 px-6 py-4 border-t border-slate-700/80 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-2 text-xs text-slate-400">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block" />
                            <span>Dokumen ini diterbitkan sah secara elektronik dan diakui oleh Kemdikbudristek.</span>
                        </div>

                        <div class="flex items-center gap-3 w-full sm:w-auto">
                            <a
                                v-if="data.file_pdf"
                                :href="data.file_pdf"
                                target="_blank"
                                class="w-full sm:w-auto px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/20 transition flex items-center justify-center gap-2"
                            >
                                <span>📄</span>
                                <span>Unduh Dokumen PDF Resmi</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CASE 2: INVALID OR NOT FOUND -->
            <div v-else class="space-y-6">
                <div class="bg-gradient-to-r from-rose-950/80 via-slate-900 to-slate-900 border-2 border-rose-500/40 rounded-3xl p-6 sm:p-8 shadow-2xl">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                        <div class="w-16 h-16 rounded-2xl bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center justify-center text-3xl font-black flex-shrink-0">
                            ⚠️
                        </div>
                        <div>
                            <span class="inline-block px-3 py-0.5 rounded-full bg-rose-500/20 border border-rose-500/30 text-rose-400 text-xs font-bold uppercase tracking-wider mb-1">
                                Data Tidak Ditemukan
                            </span>
                            <h1 class="text-xl sm:text-2xl font-black text-white">Dokumen Tidak Terdaftar</h1>
                            <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-xl">
                                Dokumen dengan nomor registrasi <strong class="text-rose-400 font-mono">{{ searchedNumber }}</strong> tidak ditemukan dalam pangkalan data resmi kelulusan {{ namaPkbm }}.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Explanation & Safe Guidance -->
                <div class="bg-slate-800/80 border border-slate-700 p-6 sm:p-8 rounded-3xl space-y-6">
                    <h2 class="text-base font-bold text-white">Kemungkinan Penyebab:</h2>
                    <ul class="space-y-3 text-xs sm:text-sm text-slate-300">
                        <li class="flex items-start gap-2.5">
                            <span class="text-rose-400 text-base">✖</span>
                            <span>Terdapat kesalahan ketik pada nomor sertifikat atau NISN yang dimasukkan.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-rose-400 text-base">✖</span>
                            <span>Sertifikat masih dalam status draf dan belum diterbitkan resmi oleh kepala lembaga PKBM.</span>
                        </li>
                        <li class="flex items-start gap-2.5">
                            <span class="text-rose-400 text-base">✖</span>
                            <span>Waspadai pemalsuan dokumen fisik yang mengatasnamakan lembaga pendidikan ini.</span>
                        </li>
                    </ul>

                    <!-- Try Again Input -->
                    <div class="pt-4 border-t border-slate-700">
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Coba Masukkan Kembali:</h3>
                        <form @submit.prevent="searchAgain" class="flex flex-col sm:flex-row gap-2.5">
                            <input
                                v-model="newQuery"
                                type="text"
                                placeholder="Masukkan nomor sertifikat atau NISN yang benar..."
                                class="flex-1 bg-slate-900 border border-slate-700 focus:border-emerald-500 text-white placeholder-slate-500 text-sm rounded-xl px-4 py-3 outline-none transition"
                            />
                            <button
                                type="submit"
                                class="px-6 py-3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-sm rounded-xl transition"
                            >
                                Cari Ulang
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="border-t border-slate-800/80 bg-slate-950 py-8 px-4 text-center text-xs text-slate-500">
            <p>&copy; {{ new Date().getFullYear() }} {{ namaPkbm }}. Hak Cipta Dilindungi Undang-Undang.</p>
            <p class="mt-1">NPSN: {{ npsn }}</p>
        </footer>
    </div>
</template>
