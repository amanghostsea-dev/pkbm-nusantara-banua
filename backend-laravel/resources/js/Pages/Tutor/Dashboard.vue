<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const classes = [
    { id: 1, name: 'Paket A - Kelas 4 Mandiri', mapel: 'Bahasa Indonesia & Matematika', total_siswa: 18, schedule: 'Senin & Rabu, 08:00 - 10:00' },
    { id: 2, name: 'Paket B - Kelas 8 Terpadu', mapel: 'Ilmu Pengetahuan Alam', total_siswa: 24, schedule: 'Selasa & Kamis, 13:00 - 15:00' },
    { id: 3, name: 'Paket C - Kelas 11 Keterampilan', mapel: 'Kewirausahaan Digital', total_siswa: 30, schedule: 'Jumat, 14:00 - 17:00' },
]

const recentAbsensi = [
    { siswa: 'Ahmad Rizki', kelas: 'Paket C - Kelas 11', status: 'Hadir', time: '08:15 WIB', badge: 'bg-emerald-100 text-emerald-700' },
    { siswa: 'Siti Aminah', kelas: 'Paket B - Kelas 8', status: 'Hadir', time: '08:10 WIB', badge: 'bg-emerald-100 text-emerald-700' },
    { siswa: 'Doni Pratama', kelas: 'Paket A - Kelas 4', status: 'Izin', time: '07:55 WIB', badge: 'bg-amber-100 text-amber-700' },
]
</script>

<template>
    <Head title="Portal Tutor — Sistem PKBM" />

    <div class="min-h-screen bg-slate-50 font-sans">
        <!-- Top Navigation -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-emerald-500/20">
                        👨‍🏫
                    </div>
                    <div>
                        <h1 class="text-base font-bold text-gray-900 leading-tight">Portal Tutor PKBM</h1>
                        <p class="text-xs text-gray-500">Pusat Pembelajaran & Pengajaran</p>
                    </div>
                </div>

                <!-- Nav Links -->
                <nav class="hidden md:flex items-center gap-1">
                    <Link href="/tutor/dashboard" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 transition">
                        Dashboard
                    </Link>
                    <Link href="/tutor/kelas" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Daftar Kelas
                    </Link>
                    <Link href="/tutor/materi" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Modul Belajar
                    </Link>
                    <Link href="/tutor/tugas" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Tugas & Evaluasi
                    </Link>
                </nav>

                <div class="flex items-center gap-4">
                    <div class="text-right hidden sm:block">
                        <div class="text-sm font-semibold text-gray-800">{{ user?.name || 'Budi Santoso, S.Pd' }}</div>
                        <div class="text-xs text-emerald-600 font-medium">Tutor Pengajar</div>
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
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-700 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-emerald-600/10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur rounded-full text-xs font-semibold uppercase tracking-wider mb-3">
                        Tahun Ajaran 2024/2025
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang, {{ user?.name || 'Tutor' }}!</h2>
                    <p class="text-emerald-100 text-sm mt-1 max-w-xl">
                        Kelola absensi kelas, pantau kehadiran warga belajar, dan input nilai raport dengan mudah dan terintegrasi.
                    </p>
                </div>
                <div class="flex gap-3">
                    <button class="px-5 py-2.5 bg-white text-emerald-700 hover:bg-emerald-50 rounded-xl font-bold text-sm shadow-lg transition">
                        + Generate QR Presensi
                    </button>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Kelas Diampu</span>
                        <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl text-lg">🏫</span>
                    </div>
                    <div class="text-3xl font-extrabold text-gray-900 mt-2">3 Kelas</div>
                    <div class="text-xs text-gray-500 mt-1">Paket A, B, & C</div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Total Warga Belajar</span>
                        <span class="p-2 bg-blue-50 text-blue-600 rounded-xl text-lg">👨‍🎓</span>
                    </div>
                    <div class="text-3xl font-extrabold text-gray-900 mt-2">72 Siswa</div>
                    <div class="text-xs text-emerald-600 mt-1 font-medium">96% Kehadiran rata-rata</div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Jadwal Minggu Ini</span>
                        <span class="p-2 bg-purple-50 text-purple-600 rounded-xl text-lg">🗓️</span>
                    </div>
                    <div class="text-3xl font-extrabold text-gray-900 mt-2">5 Sesi</div>
                    <div class="text-xs text-gray-500 mt-1">Tatap muka & daring</div>
                </div>
            </div>

            <!-- Class Cards -->
            <div>
                <h3 class="text-lg font-bold text-gray-900 mb-4">Daftar Kelas Binaan Anda</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div
                        v-for="c in classes"
                        :key="c.id"
                        class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-100">
                                    {{ c.name.split(' - ')[0] }}
                                </span>
                                <span class="text-xs text-gray-500">{{ c.total_siswa }} Warga Belajar</span>
                            </div>
                            <h4 class="font-bold text-gray-900 text-base mb-1">{{ c.name }}</h4>
                            <p class="text-xs text-gray-600 mb-4">{{ c.mapel }}</p>
                            <div class="text-xs text-gray-500 flex items-center gap-1.5 mb-4">
                                <span>⏰</span>
                                <span>{{ c.schedule }}</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex gap-2">
                            <button class="flex-1 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold transition">
                                Buka Absensi
                            </button>
                            <button class="flex-1 py-2 border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-semibold transition">
                                Input Nilai
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Table -->
            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Aktivitas Presensi Terkini</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="text-xs uppercase text-gray-400 border-b border-gray-100">
                            <tr>
                                <th class="py-3 px-4">Nama Warga Belajar</th>
                                <th class="py-3 px-4">Program / Kelas</th>
                                <th class="py-3 px-4">Waktu Presensi</th>
                                <th class="py-3 px-4 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            <tr v-for="(ab, idx) in recentAbsensi" :key="idx" class="hover:bg-gray-50/70 transition">
                                <td class="py-3 px-4 font-semibold text-gray-900">{{ ab.siswa }}</td>
                                <td class="py-3 px-4 text-gray-600">{{ ab.kelas }}</td>
                                <td class="py-3 px-4 text-gray-500 font-mono text-xs">{{ ab.time }}</td>
                                <td class="py-3 px-4 text-right">
                                    <span :class="['px-2.5 py-0.5 rounded-full text-xs font-bold', ab.badge]">
                                        {{ ab.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</template>
