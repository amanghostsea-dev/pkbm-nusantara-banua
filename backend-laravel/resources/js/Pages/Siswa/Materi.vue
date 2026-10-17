<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'

const props = defineProps({
    materis: Object,
    filters: Object,
})

const search = ref(props.filters?.search || '')

function applySearch() {
    router.get('/siswa-portal/materi', { search: search.value }, { preserveState: true })
}
</script>

<template>
    <Head title="Pustaka Modul Belajar — Portal Siswa" />

    <div class="min-h-screen bg-slate-50 font-sans">
        <!-- Top Navigation -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-indigo-500/20">
                        👨‍🎓
                    </div>
                    <div>
                        <h1 class="text-base font-bold text-gray-900 leading-tight">Portal Siswa PKBM</h1>
                        <p class="text-xs text-gray-500">Pusat Sumber Belajar Mandiri</p>
                    </div>
                </div>

                <!-- Nav Links -->
                <nav class="hidden md:flex items-center gap-1">
                    <Link href="/siswa-portal/dashboard" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Dashboard
                    </Link>
                    <Link href="/siswa-portal/jadwal" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Jadwal
                    </Link>
                    <Link href="/siswa-portal/nilai" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Nilai
                    </Link>
                    <Link href="/siswa-portal/materi" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 transition">
                        Modul Belajar
                    </Link>
                    <Link href="/siswa-portal/tugas" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Tugas Siswa
                    </Link>
                </nav>

                <div class="flex items-center gap-3">
                    <Link href="/logout" method="post" as="button" class="px-3.5 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-100 text-xs font-semibold text-gray-700 transition">
                        Keluar
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <!-- Header Banner -->
            <div class="bg-gradient-to-r from-indigo-700 via-blue-700 to-indigo-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-indigo-600/10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur rounded-full text-xs font-semibold uppercase tracking-wider mb-2">
                        Pustaka Digital Terbuka
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Modul & Bahan Belajar Mandiri</h2>
                    <p class="text-blue-100 text-sm mt-1 max-w-xl">
                        Akses seluruh materi kurikulum kesetaraan, ringkasan teori, video tutorial, dan bacaan pengayaan secara fleksibel di mana saja.
                    </p>
                </div>
                <div class="w-full md:w-auto">
                    <div class="relative">
                        <input
                            v-model="search"
                            @keyup.enter="applySearch"
                            type="text"
                            placeholder="Cari materi pelajaran..."
                            class="w-full md:w-72 bg-white/10 border border-white/20 focus:bg-white focus:text-gray-900 text-white placeholder-blue-200 text-xs rounded-xl pl-9 pr-4 py-2.5 outline-none transition"
                        />
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-blue-200">🔍</span>
                    </div>
                </div>
            </div>

            <!-- Modul Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="item in materis.data"
                    :key="item.id"
                    class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-4"
                >
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span
                                :class="[
                                    'px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider',
                                    item.tipe === 'video' ? 'bg-rose-50 text-rose-600 border border-rose-100' :
                                    item.tipe === 'pdf' ? 'bg-blue-50 text-blue-600 border border-blue-100' :
                                    'bg-indigo-50 text-indigo-600 border border-indigo-100'
                                ]"
                            >
                                {{ item.tipe === 'video' ? '🎥 Video' : item.tipe === 'pdf' ? '📄 E-Book PDF' : '📝 Modul Belajar' }}
                            </span>
                            <span class="text-xs text-gray-400 font-medium">{{ item.kelas?.nama_kelas }}</span>
                        </div>

                        <h3 class="font-extrabold text-gray-900 text-base leading-snug hover:text-indigo-600 transition">
                            {{ item.judul }}
                        </h3>

                        <p class="text-xs text-gray-500 line-clamp-3 leading-relaxed">
                            {{ item.deskripsi || 'Silakan baca materi ini untuk memperdalam pemahaman kompetensi dasar.' }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs text-gray-500 font-semibold">📚 {{ item.mata_pelajaran?.nama_mapel }}</span>

                        <a
                            v-if="item.link_url"
                            :href="item.link_url"
                            target="_blank"
                            class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5"
                        >
                            <span>{{ item.tipe === 'video' ? '▶' : '🔗' }}</span>
                            <span>{{ item.tipe === 'video' ? 'Tonton Video' : 'Buka Modul' }}</span>
                        </a>
                        <a
                            v-else-if="item.file_path"
                            :href="item.file_path"
                            target="_blank"
                            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition flex items-center gap-1.5"
                        >
                            <span>⬇️</span>
                            <span>Unduh E-Book</span>
                        </a>
                        <span v-else class="text-xs text-gray-400 italic">Materi Teori</span>
                    </div>
                </div>
            </div>

            <div v-if="materis.data.length === 0" class="bg-white rounded-3xl p-12 text-center border border-gray-100 text-gray-400">
                <div class="text-4xl mb-2">📖</div>
                <p class="text-sm font-semibold text-gray-700">Belum ada modul yang tersedia untuk pencarian ini.</p>
            </div>
        </main>
    </div>
</template>
