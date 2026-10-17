<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    raports: Object,
    kelasList: Array,
})
</script>

<template>
    <Head title="Manajemen Raport Siswa PKBM" />

    <AdminLayout>
        <div class="p-6 max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Manajemen Raport Belajar</h1>
                    <p class="text-gray-500 text-sm">Penerbitan dan pengunduhan buku laporan hasil belajar (PDF) berbasis microservice Python</p>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="py-3 px-4">Nama Siswa</th>
                            <th class="py-3 px-4">Kelas</th>
                            <th class="py-3 px-4">Semester</th>
                            <th class="py-3 px-4">Tahun Ajaran</th>
                            <th class="py-3 px-4">File Raport</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        <tr v-for="item in (raports.data || raports)" :key="item.id" class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4">
                                <div class="font-semibold text-gray-900">{{ item.siswa?.nama_lengkap || 'Siswa' }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ item.siswa?.nisn }}</div>
                            </td>
                            <td class="py-3 px-4 font-medium">{{ item.kelas?.nama_kelas || '-' }}</td>
                            <td class="py-3 px-4">Semester {{ item.semester }}</td>
                            <td class="py-3 px-4">{{ item.tahun_ajaran }}</td>
                            <td class="py-3 px-4">
                                <span class="font-mono text-xs text-gray-500">{{ item.file_pdf || 'raport.pdf' }}</span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <a
                                    v-if="item.file_pdf"
                                    :href="`/admin/raports/download/${item.file_pdf}`"
                                    target="_blank"
                                    class="px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-lg text-xs font-bold transition inline-flex items-center gap-1.5"
                                >
                                    <span>📥</span>
                                    <span>Unduh PDF</span>
                                </a>
                                <span v-else class="text-xs text-gray-400">Belum di-generate</span>
                            </td>
                        </tr>
                        <tr v-if="!(raports.data?.length || raports?.length)">
                            <td colspan="6" class="py-8 text-center text-gray-400">
                                Belum ada raport yang diterbitkan. Anda dapat menerbitkan raport langsung dari halaman detail masing-masing siswa.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>
