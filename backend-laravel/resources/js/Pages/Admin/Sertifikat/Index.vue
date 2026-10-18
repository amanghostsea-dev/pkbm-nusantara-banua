<script setup>
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    sertifikats: Object,
    programs: Array,
})

const generateSertifikat = (item) => {
    router.post(`/admin/sertifikats/${item.id}/generate`)
}
</script>

<template>
    <Head title="Manajemen Sertifikat Kelulusan PKBM" />

    <AdminLayout>
        <div class="p-6 max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Sertifikat Kelulusan & Ijazah Kesetaraan</h1>
                    <p class="text-gray-500 text-sm">Penerbitan sertifikat digital berbarcode / QR Code verifikasi kelulusan resmi</p>
                </div>
            </div>

            <!-- Table with Responsive Overflow -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm min-w-[700px]">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="py-3 px-4">Nomor Sertifikat</th>
                                <th class="py-3 px-4">Nama Siswa</th>
                                <th class="py-3 px-4">Program</th>
                                <th class="py-3 px-4">Tgl Terbit</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            <tr v-for="item in (sertifikats.data || sertifikats)" :key="item.id" class="hover:bg-gray-50/80 transition">
                                <td class="py-3 px-4 font-mono font-bold text-indigo-700">{{ item.nomor_sertifikat }}</td>
                                <td class="py-3 px-4 font-semibold text-gray-900">{{ item.siswa?.nama_lengkap }}</td>
                                <td class="py-3 px-4">{{ item.program?.nama_program }}</td>
                                <td class="py-3 px-4 text-gray-500">{{ item.tanggal_terbit }}</td>
                                <td class="py-3 px-4">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold" :class="item.file_pdf ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'">
                                        {{ item.file_pdf ? 'Telah Diterbitkan' : 'Menunggu Generate' }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <button
                                        @click="generateSertifikat(item)"
                                        class="px-3 py-1.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 active:scale-95 rounded-lg text-xs font-bold transition"
                                    >
                                        🖨️ Cetak Ulang PDF
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!(sertifikats.data?.length || sertifikats?.length)">
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    Belum ada sertifikat kelulusan yang diterbitkan. Sertifikat diterbitkan saat warga belajar dinyatakan lulus program.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
