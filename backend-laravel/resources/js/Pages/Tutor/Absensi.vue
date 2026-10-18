<script setup>
import { Head, Link } from '@inertiajs/vue3'
import TutorLayout from '@/Layouts/TutorLayout.vue'

const props = defineProps({
    kelas: Object,
    absensis: Object,
})
</script>

<template>
    <Head :title="`Presensi Kelas - ${kelas.nama_kelas}`" />

    <TutorLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <Link href="/tutor/kelas" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                            &larr; Kembali ke Daftar Kelas
                        </Link>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 mt-1">Presensi: {{ kelas.nama_kelas }}</h2>
                    <p class="text-xs sm:text-sm text-gray-500">Catatan kehadiran sesi belajar tatap muka dan mandiri</p>
                </div>
            </div>

            <!-- Table with Responsive Overflow -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm min-w-[550px]">
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
                                <td colspan="4" class="py-8 text-center text-gray-400 text-sm">Belum ada data presensi pada kelas ini.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </TutorLayout>
</template>
