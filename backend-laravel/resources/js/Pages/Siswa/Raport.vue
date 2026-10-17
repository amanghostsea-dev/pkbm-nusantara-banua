<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const raportList = [
    { semester: 'Semester 1', tahun: '2023/2024', rata_rata: 88.5, status: 'Naik Tingkat', file: 'raport_semester_1.pdf' },
    { semester: 'Semester 2', tahun: '2023/2024', rata_rata: 89.2, status: 'Naik Tingkat', file: 'raport_semester_2.pdf' },
    { semester: 'Semester 3', tahun: '2024/2025', rata_rata: 89.8, status: 'Aktif Berjalan', file: 'raport_semester_3.pdf' },
]
</script>

<template>
    <Head title="Buku Raport — Portal Siswa" />

    <div class="min-h-screen bg-slate-50 font-sans">
        <!-- Top Navigation -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-indigo-500/20">
                        📄
                    </div>
                    <div>
                        <h1 class="text-base font-bold text-gray-900 leading-tight">Buku Raport Digital</h1>
                        <p class="text-xs text-gray-500">Laporan Hasil Belajar Mandiri</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <Link href="/siswa-portal/dashboard" class="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-100 text-xs font-semibold text-gray-700 transition">
                        &larr; Dashboard
                    </Link>
                    <Link href="/logout" method="post" as="button" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-xs font-semibold text-gray-700 transition">
                        Keluar
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <div>
                <h2 class="text-2xl font-extrabold text-gray-900">Arsip Raport Hasil Belajar</h2>
                <p class="text-sm text-gray-500">Unduh lembar hasil penilaian dan capaian kompetensi semester berformat PDF</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div v-for="(item, i) in raportList" :key="i" class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-4 hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">
                                {{ item.tahun }}
                            </span>
                            <span class="text-xs font-bold text-emerald-600">{{ item.status }}</span>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mt-3">{{ item.semester }}</h3>
                        <p class="text-xs text-gray-400 mt-1">Paket C (Setara SMA) • PKBM Nusantara Banua</p>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-2xl flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Nilai Rata-rata</span>
                        <span class="text-2xl font-black text-indigo-600">{{ item.rata_rata }}</span>
                    </div>

                    <a
                        :href="`/admin/raports/download/${item.file}`"
                        target="_blank"
                        class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-center text-xs font-bold transition shadow-sm flex items-center justify-center gap-2"
                    >
                        <span>📥</span>
                        <span>Unduh Dokumen PDF</span>
                    </a>
                </div>
            </div>
        </main>
    </div>
</template>
