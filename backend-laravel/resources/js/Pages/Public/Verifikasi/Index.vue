<script setup>
import { ref } from 'vue'
import { Head, router, Link } from '@inertiajs/vue3'

const props = defineProps({
    totalSertifikat: { type: Number, default: 0 },
    totalRaport: { type: Number, default: 0 },
    namaPkbm: { type: String, default: 'PKBM Nusantara Banua' },
    npsn: { type: String, default: 'P9908123' },
    kepalaPkbm: { type: String, default: 'Drs. H. Mulyadi, M.Pd' },
})

const query = ref('')

function handleSearch() {
    if (!query.value.trim()) return
    router.post('/verifikasi/cari', { query: query.value.trim() })
}

function quickSearch(sample) {
    query.value = sample
    handleSearch()
}
</script>

<template>
    <Head title="Pusat Verifikasi Keaslian Ijazah & Sertifikat — PKBM" />

    <div class="min-h-screen bg-slate-900 text-slate-100 flex flex-col font-sans selection:bg-emerald-500 selection:text-white">
        <!-- Top Institutional Header -->
        <header class="border-b border-slate-800 bg-slate-950/80 backdrop-blur sticky top-0 z-50">
            <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 sm:h-20 flex items-center justify-between">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-600 to-teal-500 flex items-center justify-center text-2xl shadow-lg shadow-emerald-500/20">
                        🏛️
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-base sm:text-lg font-black tracking-tight text-white">{{ namaPkbm }}</span>
                            <span class="hidden sm:inline-block px-2 py-0.5 text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 rounded-full">RESMI</span>
                        </div>
                        <p class="text-xs text-slate-400">Pusat Validasi Dokumen Kelulusan • NPSN: {{ npsn }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        href="/login"
                        class="px-4 py-2 text-xs font-semibold rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 transition border border-slate-700"
                    >
                        Login Portal &rarr;
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Hero & Search Section -->
        <main class="flex-1 flex flex-col items-center justify-center px-4 sm:px-6 py-12 sm:py-20 relative overflow-hidden">
            <!-- Background Glows -->
            <div class="absolute -top-40 left-1/2 -translate-x-1/2 w-[700px] h-[400px] bg-emerald-600/15 blur-[120px] rounded-full pointer-events-none" />
            <div class="absolute -bottom-40 right-10 w-[500px] h-[300px] bg-teal-600/10 blur-[100px] rounded-full pointer-events-none" />

            <div class="max-w-3xl w-full text-center relative z-10 space-y-8">
                <!-- Badge -->
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-950/60 border border-emerald-500/40 text-emerald-400 text-xs font-semibold tracking-wide">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping" />
                    Sistem Verifikasi Digital Terpusat Kemdikbudristek
                </div>

                <!-- Headline -->
                <div class="space-y-3">
                    <h1 class="text-3xl sm:text-5xl font-black tracking-tight text-white leading-tight">
                        Cek Keaslian <span class="bg-gradient-to-r from-emerald-400 via-teal-300 to-cyan-400 bg-clip-text text-transparent">Ijazah & Sertifikat</span>
                    </h1>
                    <p class="text-sm sm:text-base text-slate-400 max-w-xl mx-auto leading-relaxed">
                        Layanan resmi untuk instansi, perusahaan, dan perguruan tinggi guna memastikan keabsahan data kelulusan warga belajar PKBM secara real-time.
                    </p>
                </div>

                <!-- Search Card -->
                <div class="bg-slate-800/80 backdrop-blur-xl border border-slate-700 p-3 sm:p-4 rounded-3xl shadow-2xl shadow-black/40">
                    <form @submit.prevent="handleSearch" class="flex flex-col sm:flex-row gap-2.5">
                        <div class="relative flex-1">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-lg">🔍</span>
                            <input
                                v-model="query"
                                type="text"
                                placeholder="Masukkan Nomor Sertifikat (cth: SRT/PKBM/2024/001) atau NISN..."
                                class="w-full bg-slate-900/90 border border-slate-700 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/30 text-white placeholder-slate-500 text-sm rounded-2xl pl-12 pr-4 py-3.5 outline-none transition"
                                autofocus
                            />
                        </div>
                        <button
                            type="submit"
                            class="px-7 py-3.5 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-400 hover:to-teal-500 text-slate-950 font-extrabold text-sm rounded-2xl shadow-lg shadow-emerald-500/25 transition flex items-center justify-center gap-2"
                        >
                            <span>Verifikasi</span>
                            <span>&rarr;</span>
                        </button>
                    </form>

                    <!-- Quick Suggestions -->
                    <div class="mt-4 pt-3 border-t border-slate-700/60 flex flex-wrap items-center justify-center gap-2 text-xs text-slate-400">
                        <span>Contoh Uji Coba:</span>
                        <button
                            type="button"
                            @click="quickSearch('SRT/PKBM/2024/001')"
                            class="px-2.5 py-1 bg-slate-700/60 hover:bg-slate-700 text-emerald-300 font-mono rounded-lg transition border border-slate-600/50"
                        >
                            SRT/PKBM/2024/001
                        </button>
                        <button
                            type="button"
                            @click="quickSearch('1234567890')"
                            class="px-2.5 py-1 bg-slate-700/60 hover:bg-slate-700 text-teal-300 font-mono rounded-lg transition border border-slate-600/50"
                        >
                            NISN: 1234567890
                        </button>
                    </div>
                </div>

                <!-- Trust Badges & Stats -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-4 text-left">
                    <div class="p-4 rounded-2xl bg-slate-800/40 border border-slate-800">
                        <div class="text-emerald-400 font-bold text-2xl font-mono">{{ totalSertifikat }}</div>
                        <div class="text-xs text-slate-400 mt-1">Sertifikat Terbit</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-800/40 border border-slate-800">
                        <div class="text-teal-400 font-bold text-2xl font-mono">100% Sah</div>
                        <div class="text-xs text-slate-400 mt-1">Standar Dapodik & Kemdikbud</div>
                    </div>
                    <div class="p-4 rounded-2xl bg-slate-800/40 border border-slate-800 col-span-2 sm:col-span-1">
                        <div class="text-cyan-400 font-bold text-2xl font-mono">QR Secure</div>
                        <div class="text-xs text-slate-400 mt-1">Enkripsi Digital Anti-Pemalsuan</div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Informational 3-Column Section -->
        <section class="border-t border-slate-800 bg-slate-950/40 py-12 px-4 sm:px-6">
            <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xl">
                        📲
                    </div>
                    <h3 class="font-bold text-white text-base">Pemindaian QR Cepat</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Arahkan kamera smartphone ke QR Code yang tertera di sudut kiri bawah sertifikat fisik. Anda akan langsung diarahkan ke halaman validasi resmi ini.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-500/10 text-teal-400 flex items-center justify-center text-xl">
                        🛡️
                    </div>
                    <h3 class="font-bold text-white text-base">Perlindungan Data Pribadi</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Nomor Induk Kependudukan (NIK) disamarkan secara otomatis sesuai standar regulasi PDP demi keamanan data warga belajar.
                    </p>
                </div>

                <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3">
                    <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-xl">
                        🎓
                    </div>
                    <h3 class="font-bold text-white text-base">Pengakuan Kesetaraan</h3>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Ijazah Paket A, B, dan C memiliki hak dan kedudukan hukum yang setara dengan pendidikan formal (SD, SMP, SMA/SMK) di Indonesia.
                    </p>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="border-t border-slate-800/80 bg-slate-950 py-8 px-4 text-center text-xs text-slate-500">
            <p>&copy; {{ new Date().getFullYear() }} {{ namaPkbm }}. Hak Cipta Dilindungi Undang-Undang.</p>
            <p class="mt-1">NPSN: {{ npsn }} • Kepala PKBM: {{ kepalaPkbm }}</p>
        </footer>
    </div>
</template>
