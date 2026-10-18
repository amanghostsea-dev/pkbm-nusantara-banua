<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import SiswaLayout from '@/Layouts/SiswaLayout.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const jadwalList = [
    { hari: 'Senin', waktu: '08:00 - 10:00', mapel: 'Pendidikan Kewarganegaraan', tutor: 'Dra. Sri Wahyuni', ruangan: 'Ruang C-01' },
    { hari: 'Senin', waktu: '10:30 - 12:30', mapel: 'Bahasa Indonesia', tutor: 'Budi Santoso, S.Pd', ruangan: 'Ruang C-01' },
    { hari: 'Rabu', waktu: '13:00 - 15:00', mapel: 'Matematika Terapan', tutor: 'Hendro Kusumo, M.Pd', ruangan: 'Lab Komputer' },
    { hari: 'Jumat', waktu: '14:00 - 16:30', mapel: 'Kewirausahaan Digital & Vokasi', tutor: 'Rina Kartika, S.E', ruangan: 'Lab Vokasi & Digital' },
]
</script>

<template>
    <Head title="Jadwal Pelajaran — Portal Siswa" />

    <SiswaLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900">Jadwal Sesi Pembelajaran Mingguan</h2>
                <p class="text-xs sm:text-sm text-gray-500">Alokasi waktu tatap muka, bimbingan mandiri terstruktur, dan kelas vokasi</p>
            </div>

            <!-- Table Responsive Wrapper -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm min-w-[600px]">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="py-4 px-6">Hari</th>
                                <th class="py-4 px-6">Waktu</th>
                                <th class="py-4 px-6">Mata Pelajaran</th>
                                <th class="py-4 px-6">Tutor Pengajar</th>
                                <th class="py-4 px-6">Ruangan / Tempat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            <tr v-for="(j, i) in jadwalList" :key="i" class="hover:bg-gray-50/80 transition">
                                <td class="py-4 px-6 font-bold text-indigo-700">{{ j.hari }}</td>
                                <td class="py-4 px-6 font-mono font-medium text-xs sm:text-sm">{{ j.waktu }}</td>
                                <td class="py-4 px-6 font-bold text-gray-900">{{ j.mapel }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ j.tutor }}</td>
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700 whitespace-nowrap">
                                        {{ j.ruangan }}
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
