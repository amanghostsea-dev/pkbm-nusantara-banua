<script setup>
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps({
    kelas: Object,
    absensis: Object,
})
</script>

<template>
    <Head :title="`Presensi Kelas - ${kelas.nama_kelas}`" />

    <div class="min-h-screen bg-slate-50 font-sans">
        <!-- Top Navigation -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-emerald-500/20">
                        📋
                    </div>
                    <div>
                        <h1 class="text-base font-bold text-gray-900 leading-tight">Presensi Siswa</h1>
                        <p class="text-xs text-gray-500">{{ kelas.nama_kelas }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Link href="/tutor/kelas" class="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-100 text-xs font-semibold text-gray-700 transition">
                        &larr; Kembali ke Kelas
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-900">Riwayat Presensi Warga Belajar</h2>
                    <p class="text-sm text-gray-500">Catatan kehadiran sesi belajar tatap muka dan mandiri</p>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="py-3 px-4">Nama Siswa</th>
                            <th class="py-3 px-4">Waktu Presensi</th>
                            <th class="py-3 px-4">Metode</th>
                            <th class="py-3 px-4">Status Kehadiran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        <tr v-for="item in (absensis.data || absensis)" :key="item.id" class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4">
                                <div class="font-bold text-gray-900">{{ item.siswa?.nama_lengkap || 'Siswa' }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ item.siswa?.nisn }}</div>
                            </td>
                            <td class="py-3 px-4 text-gray-600 font-mono text-xs">{{ item.created_at ? new Date(item.created_at).toLocaleString('id-ID') : '-' }}</td>
                            <td class="py-3 px-4 capitalize text-xs font-bold text-gray-500">{{ item.metode || 'QR Code' }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold capitalize"
                                    :class="item.status === 'hadir' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'">
                                    {{ item.status || 'Hadir' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="!(absensis.data?.length || absensis?.length)">
                            <td colspan="4" class="py-8 text-center text-gray-400">Belum ada data presensi pada kelas ini.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</template>
