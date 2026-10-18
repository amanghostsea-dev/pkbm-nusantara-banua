<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import SiswaLayout from '@/Layouts/SiswaLayout.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const nilaiList = [
    { mapel: 'Pendidikan Kewarganegaraan', tugas: 85, uts: 90, uas: 88, akhir: 88, status: 'Tuntas', predikat: 'A' },
    { mapel: 'Bahasa Indonesia', tugas: 90, uts: 92, uas: 93, akhir: 92, status: 'Tuntas', predikat: 'A' },
    { mapel: 'Matematika Terapan', tugas: 80, uts: 85, uas: 86, akhir: 84, status: 'Tuntas', predikat: 'B+' },
    { mapel: 'Kewirausahaan Digital', tugas: 95, uts: 95, uas: 96, akhir: 95, status: 'Tuntas', predikat: 'A+' },
    { mapel: 'Ilmu Pengetahuan Alam', tugas: 82, uts: 80, uas: 85, akhir: 82, status: 'Tuntas', predikat: 'B+' },
]
</script>

<template>
    <Head title="Transkrip Nilai — Portal Siswa" />

    <SiswaLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900">Transkrip Nilai Belajar</h2>
                    <p class="text-xs sm:text-sm text-gray-500">Hasil penilaian tugas mandiri, evaluasi tengah semester, dan ujian akhir</p>
                </div>
            </div>

            <!-- Table Responsive Wrapper -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm min-w-[650px]">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="py-4 px-6">Mata Pelajaran</th>
                                <th class="py-4 px-6">Tugas Mandiri</th>
                                <th class="py-4 px-6">UTS</th>
                                <th class="py-4 px-6">UAS</th>
                                <th class="py-4 px-6">Nilai Akhir</th>
                                <th class="py-4 px-6">Predikat</th>
                                <th class="py-4 px-6">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            <tr v-for="(n, i) in nilaiList" :key="i" class="hover:bg-gray-50/80 transition">
                                <td class="py-4 px-6 font-bold text-gray-900">{{ n.mapel }}</td>
                                <td class="py-4 px-6 font-medium">{{ n.tugas }}</td>
                                <td class="py-4 px-6 font-medium">{{ n.uts }}</td>
                                <td class="py-4 px-6 font-medium">{{ n.uas }}</td>
                                <td class="py-4 px-6 font-black text-indigo-600 text-base">{{ n.akhir }}</td>
                                <td class="py-4 px-6 font-bold text-gray-800">{{ n.predikat }}</td>
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 whitespace-nowrap">
                                        {{ n.status }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </SiswaLayout>
</template>
