<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import TutorLayout from '@/Layouts/TutorLayout.vue'

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

    <TutorLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6 sm:space-y-8">
            <!-- Welcome Banner -->
            <div class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-700 rounded-3xl p-5 sm:p-8 text-white shadow-xl shadow-emerald-600/10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur rounded-full text-xs font-semibold uppercase tracking-wider mb-3">
                        Tahun Ajaran 2024/2025
                    </span>
                    <h2 class="text-xl sm:text-3xl font-extrabold tracking-tight">Selamat Datang, {{ user?.name || 'Tutor' }}!</h2>
                    <p class="text-emerald-100 text-xs sm:text-sm mt-1 max-w-xl">
                        Kelola absensi kelas, pantau kehadiran warga belajar, dan input nilai raport dengan mudah dan terintegrasi.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2.5 w-full sm:w-auto">
                    <Link href="/tutor/kelas" class="flex-1 sm:flex-initial text-center px-4 py-2.5 bg-white text-emerald-700 hover:bg-emerald-50 active:scale-95 rounded-xl font-bold text-xs sm:text-sm shadow-lg transition">
                        + Akses Kelas Binaan
                    </Link>
                    <Link
                        href="/profile"
                        class="flex-1 sm:flex-initial text-center px-4 py-2.5 bg-white/10 hover:bg-white/20 border border-white/30 text-white rounded-xl font-bold text-xs sm:text-sm transition flex items-center justify-center gap-1.5"
                        title="Pengaturan Profil & Ganti Kata Sandi Mandiri"
                    >
                        <span>👤</span>
                        <span>Profil & Sandi</span>
                    </Link>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-5">
                <div class="bg-white p-5 sm:p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Kelas Diampu</span>
                        <span class="p-2 bg-emerald-50 text-emerald-600 rounded-xl text-lg">🏫</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-2">3 Kelas</div>
                    <div class="text-xs text-gray-500 mt-1">Paket A, B, & C</div>
                </div>

                <div class="bg-white p-5 sm:p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Total Warga Belajar</span>
                        <span class="p-2 bg-blue-50 text-blue-600 rounded-xl text-lg">👨‍🎓</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-2">72 Siswa</div>
                    <div class="text-xs text-emerald-600 mt-1 font-medium">96% Kehadiran rata-rata</div>
                </div>

                <div class="bg-white p-5 sm:p-6 rounded-2xl border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Jadwal Minggu Ini</span>
                        <span class="p-2 bg-purple-50 text-purple-600 rounded-xl text-lg">🗓️</span>
                    </div>
                    <div class="text-2xl sm:text-3xl font-extrabold text-gray-900 mt-2">5 Sesi</div>
                    <div class="text-xs text-gray-500 mt-1">Tatap muka & daring</div>
                </div>
            </div>

            <!-- Class Cards -->
            <div>
                <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-4">Daftar Kelas Binaan Anda</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                    <div
                        v-for="c in classes"
                        :key="c.id"
                        class="bg-white rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between"
                    >
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-100">
                                    {{ c.name.split(' - ')[0] }}
                                </span>
                                <span class="text-xs text-gray-500">{{ c.total_siswa }} Warga Belajar</span>
                            </div>
                            <h4 class="font-bold text-gray-900 text-base mb-1">{{ c.name }}</h4>
                            <p class="text-xs text-gray-600 mb-3">{{ c.mapel }}</p>
                            <div class="text-xs text-gray-500 flex items-center gap-1.5 mb-4">
                                <span>⏰</span>
                                <span>{{ c.schedule }}</span>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100 flex gap-2">
                            <Link href="/tutor/kelas" class="flex-1 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl text-xs font-semibold text-center transition">
                                Buka Kelas
                            </Link>
                            <Link href="/tutor/absensi" class="flex-1 py-2.5 border border-gray-200 hover:bg-gray-50 active:scale-95 text-gray-700 rounded-xl text-xs font-semibold text-center transition">
                                Presensi
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity Table with Scroll -->
            <div class="bg-white rounded-2xl border border-gray-200 p-5 sm:p-6 shadow-sm">
                <h3 class="text-base sm:text-lg font-bold text-gray-900 mb-4">Aktivitas Presensi Terkini</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm min-w-[550px]">
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
        </div>
    </TutorLayout>
</template>
