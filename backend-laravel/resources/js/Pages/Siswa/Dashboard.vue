<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const myProgram = {
    jenjang: 'Paket C (Setara SMA)',
    kelas: 'Kelas 12 IPS Mandiri',
    nisn: '0054321987',
    semester: 'Semester Genap 2024/2025',
    status: 'Aktif Belajar',
}

const subjects = [
    { name: 'Pendidikan Kewarganegaraan', tutor: 'Dra. Sri Wahyuni', score: 88, status: 'Tuntas' },
    { name: 'Bahasa Indonesia', tutor: 'Budi Santoso, S.Pd', score: 92, status: 'Tuntas' },
    { name: 'Matematika Terapan', tutor: 'Hendro Kusumo, M.Pd', score: 84, status: 'Tuntas' },
    { name: 'Kewirausahaan Digital', tutor: 'Rina Kartika, S.E', score: 95, status: 'Tuntas' },
]

const upcomingSessions = [
    { day: 'Hari Ini', time: '14:00 - 16:00', title: 'Kewirausahaan Digital (Praktik E-Commerce)', room: 'Lab Komputer / Daring' },
    { day: 'Kamis, 16 Okt', time: '09:00 - 11:30', title: 'Matematika Terapan (Persiapan Ujian)', room: 'Ruang Belajar C' },
]
</script>

<template>
    <Head title="Portal Siswa — Sistem PKBM" />

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
                        <p class="text-xs text-gray-500">Ruang Warga Belajar Mandiri</p>
                    </div>
                </div>

                <!-- Nav Links -->
                <nav class="hidden md:flex items-center gap-1">
                    <Link href="/siswa-portal/dashboard" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 transition">
                        Dashboard
                    </Link>
                    <Link href="/siswa-portal/jadwal" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Jadwal
                    </Link>
                    <Link href="/siswa-portal/nilai" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Nilai
                    </Link>
                    <Link href="/siswa-portal/materi" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Modul Belajar
                    </Link>
                    <Link href="/siswa-portal/tugas" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Tugas Siswa
                    </Link>
                </nav>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-semibold text-gray-800">{{ user?.name || 'Ahmad Rizki' }}</div>
                        <div class="text-xs text-indigo-600 font-medium">{{ myProgram.jenjang }}</div>
                    </div>
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="px-3.5 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-100 text-xs font-semibold text-gray-700 transition"
                    >
                        Keluar
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
            <!-- Student Hero Card -->
            <div class="bg-gradient-to-r from-indigo-700 via-blue-700 to-indigo-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-indigo-600/10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur rounded-full text-xs font-semibold uppercase tracking-wider mb-3">
                        NISN: {{ myProgram.nisn }} • {{ myProgram.status }}
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Halo, {{ user?.name || 'Warga Belajar' }}!</h2>
                    <p class="text-blue-100 text-sm mt-1 max-w-xl">
                        {{ myProgram.jenjang }} • {{ myProgram.kelas }} ({{ myProgram.semester }})
                    </p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <button class="px-5 py-2.5 bg-white text-indigo-700 hover:bg-blue-50 rounded-xl font-bold text-sm shadow-lg transition flex items-center gap-2">
                        <span>📲</span>
                        <span>Scan Presensi QR</span>
                    </button>
                    <button class="px-5 py-2.5 bg-white/10 hover:bg-white/20 border border-white/30 text-white rounded-xl font-bold text-sm transition flex items-center gap-2">
                        <span>📄</span>
                        <span>Lihat Raport</span>
                    </button>
                </div>
            </div>

            <!-- 3 Stat Widgets -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Rata-Rata Nilai</span>
                        <span class="p-2 bg-indigo-50 text-indigo-600 rounded-xl text-lg">📊</span>
                    </div>
                    <div class="text-3xl font-extrabold text-gray-900 mt-2">89.75</div>
                    <div class="text-xs text-emerald-600 mt-1 font-semibold">Predikat: Amat Baik (A)</div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Tingkat Kehadiran</span>
                        <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl text-lg">✅</span>
                    </div>
                    <div class="text-3xl font-extrabold text-gray-900 mt-2">95.8%</div>
                    <div class="text-xs text-gray-500 mt-1">23 dari 24 sesi hadir</div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Peluang Kelulusan (AI)</span>
                        <span class="p-2 bg-purple-50 text-purple-600 rounded-xl text-lg">🤖</span>
                    </div>
                    <div class="text-3xl font-extrabold text-purple-600 mt-2">98.2%</div>
                    <div class="text-xs text-purple-600 mt-1 font-semibold">Probabilitas Sangat Tinggi</div>
                </div>
            </div>

            <!-- Two Columns: Upcoming Schedule & Academic Scores -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Upcoming Schedule -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center justify-between">
                        <span>Jadwal Belajar Terdekat</span>
                        <span class="text-xs text-indigo-600 font-semibold cursor-pointer hover:underline">Lihat Kalender &rarr;</span>
                    </h3>
                    <div class="space-y-4">
                        <div
                            v-for="(session, idx) in upcomingSessions"
                            :key="idx"
                            class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex items-start gap-4"
                        >
                            <div class="p-3 bg-white rounded-xl shadow-sm text-center min-w-[70px] border border-gray-100">
                                <div class="text-xs font-bold text-indigo-600">{{ session.day.split(',')[0] }}</div>
                                <div class="text-[11px] text-gray-400 mt-0.5">{{ session.time.split(' ')[0] }}</div>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-gray-800 text-sm">{{ session.title }}</h4>
                                <p class="text-xs text-gray-500 mt-0.5">{{ session.room }}</p>
                                <span class="inline-block mt-2 text-[11px] px-2 py-0.5 bg-indigo-100 text-indigo-700 rounded-md font-medium">
                                    {{ session.time }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic Subjects -->
                <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center justify-between">
                        <span>Ringkasan Nilai Mata Pelajaran</span>
                        <span class="text-xs text-indigo-600 font-semibold cursor-pointer hover:underline">Detail Transkrip &rarr;</span>
                    </h3>
                    <div class="space-y-3">
                        <div
                            v-for="(sub, idx) in subjects"
                            :key="idx"
                            class="p-3.5 rounded-xl border border-gray-100 hover:bg-slate-50 transition flex items-center justify-between"
                        >
                            <div>
                                <div class="font-semibold text-gray-800 text-sm">{{ sub.name }}</div>
                                <div class="text-xs text-gray-400">Tutor: {{ sub.tutor }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-extrabold text-indigo-600 text-base">{{ sub.score }}</div>
                                <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">{{ sub.status }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</template>
