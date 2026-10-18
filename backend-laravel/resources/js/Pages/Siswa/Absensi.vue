<script setup>
import { ref, computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import SiswaLayout from '@/Layouts/SiswaLayout.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
const scanSuccess = ref(false)

const riwayatAbsensi = [
    { tanggal: '16 Oktober 2024', sesi: 'Kewirausahaan Digital', tutor: 'Rina Kartika, S.E', waktu: '14:05 WIB', status: 'Hadir', metode: 'QR Code' },
    { tanggal: '14 Oktober 2024', sesi: 'Bahasa Indonesia', tutor: 'Budi Santoso, S.Pd', waktu: '08:12 WIB', status: 'Hadir', metode: 'Face Scan' },
    { tanggal: '11 Oktober 2024', sesi: 'Matematika Terapan', tutor: 'Hendro Kusumo, M.Pd', waktu: '09:00 WIB', status: 'Hadir', metode: 'QR Code' },
    { tanggal: '09 Oktober 2024', sesi: 'Pendidikan Kewarganegaraan', tutor: 'Dra. Sri Wahyuni', waktu: '10:15 WIB', status: 'Hadir', metode: 'QR Code' },
]

const triggerDemoScan = () => {
    scanSuccess.value = true
    setTimeout(() => {
        scanSuccess.value = false
    }, 4000)
}
</script>

<template>
    <Head title="Presensi & Kehadiran — Portal Siswa" />

    <SiswaLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-900">Presensi Warga Belajar</h2>
                    <p class="text-sm text-gray-500">Lakukan pemindaian QR Code saat sesi tatap muka atau tutorial daring</p>
                </div>
                <button
                    @click="triggerDemoScan"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm shadow-md transition flex items-center gap-2"
                >
                    <span>📷</span>
                    <span>Pindai QR Presensi Kelas</span>
                </button>
            </div>

            <!-- Notification if scan simulated -->
            <div v-if="scanSuccess" class="p-4 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800 text-sm flex items-center gap-3">
                <span class="text-xl">✅</span>
                <div>
                    <p class="font-bold">Presensi Berhasil Diverifikasi!</p>
                    <p class="text-xs">Kehadiran Anda pada sesi belajar hari ini telah dicatat ke dalam database sistem.</p>
                </div>
            </div>

            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Tingkat Kehadiran</span>
                    <div class="text-3xl font-black text-gray-900 mt-2">96.5%</div>
                    <div class="text-xs text-emerald-600 font-semibold mt-1">Sangat Tertib</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Sesi Hadir</span>
                    <div class="text-3xl font-black text-indigo-600 mt-2">28 Sesi</div>
                    <div class="text-xs text-gray-400 mt-1">Dari 30 sesi terjadwal</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Izin / Sakit</span>
                    <div class="text-3xl font-black text-amber-600 mt-2">2 Sesi</div>
                    <div class="text-xs text-gray-400 mt-1">Surat keterangan terlampir</div>
                </div>
            </div>

            <!-- Table Responsive Wrapper -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm min-w-[600px]">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="py-4 px-6">Tanggal</th>
                                <th class="py-4 px-6">Sesi Belajar</th>
                                <th class="py-4 px-6">Tutor</th>
                                <th class="py-4 px-6">Waktu Presensi</th>
                                <th class="py-4 px-6">Metode</th>
                                <th class="py-4 px-6">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            <tr v-for="(item, i) in riwayatAbsensi" :key="i" class="hover:bg-gray-50/80 transition">
                                <td class="py-4 px-6 font-semibold text-gray-900 whitespace-nowrap">{{ item.tanggal }}</td>
                                <td class="py-4 px-6 font-medium text-gray-800">{{ item.sesi }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ item.tutor }}</td>
                                <td class="py-4 px-6 font-mono text-xs text-gray-500 whitespace-nowrap">{{ item.waktu }}</td>
                                <td class="py-4 px-6 text-xs font-bold text-gray-500">{{ item.metode }}</td>
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 whitespace-nowrap">
                                        {{ item.status }}
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
