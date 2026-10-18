<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import SiswaLayout from '@/Layouts/SiswaLayout.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const paymentHistory = [
    { periode: 'Oktober 2024', jenis: 'SPP Bulanan', nominal: 150000, status: 'Lunas', tgl: '05 Okt 2024', ref: 'TRX-202410-001' },
    { periode: 'September 2024', jenis: 'SPP Bulanan', nominal: 150000, status: 'Lunas', tgl: '04 Sep 2024', ref: 'TRX-202409-082' },
    { periode: 'Agustus 2024', jenis: 'SPP Bulanan', nominal: 150000, status: 'Lunas', tgl: '02 Agu 2024', ref: 'TRX-202408-019' },
    { periode: 'Tahun Ajaran 2024', jenis: 'Modul & Buku Digital', nominal: 350000, status: 'Lunas', tgl: '15 Jul 2024', ref: 'TRX-202407-005' },
]
</script>

<template>
    <Head title="Informasi Pembayaran — Portal Siswa" />

    <SiswaLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900">Riwayat Pembayaran & Tagihan</h2>
                <p class="text-xs sm:text-sm text-gray-500">Informasi pembayaran SPP bulanan, modul pembelajaran, dan bukti kwitansi</p>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Status Pembayaran SPP</span>
                    <div class="text-xl sm:text-2xl font-black text-emerald-600 mt-2">Lancar / Lunas</div>
                    <div class="text-xs text-gray-500 mt-1">Sampai bulan berjalan (Oktober 2024)</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total Terbayar Semester Ini</span>
                    <div class="text-xl sm:text-2xl font-black text-gray-900 mt-2 font-mono">Rp 800.000</div>
                    <div class="text-xs text-emerald-600 font-semibold mt-1">✓ Bebas Tunggakan</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm sm:col-span-2 md:col-span-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Tagihan Bulan Depan</span>
                    <div class="text-xl sm:text-2xl font-black text-indigo-600 mt-2 font-mono">Rp 150.000</div>
                    <div class="text-xs text-gray-500 mt-1">Jatuh tempo 10 November 2024</div>
                </div>
            </div>

            <!-- Table with Responsive Overflow -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm min-w-[650px]">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="py-4 px-6">No. Referensi</th>
                                <th class="py-4 px-6">Periode</th>
                                <th class="py-4 px-6">Jenis Pembayaran</th>
                                <th class="py-4 px-6">Nominal</th>
                                <th class="py-4 px-6">Tanggal Pembayaran</th>
                                <th class="py-4 px-6">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            <tr v-for="(p, i) in paymentHistory" :key="i" class="hover:bg-gray-50/80 transition">
                                <td class="py-4 px-6 font-mono font-medium text-xs text-gray-500">{{ p.ref }}</td>
                                <td class="py-4 px-6 font-bold text-gray-900">{{ p.periode }}</td>
                                <td class="py-4 px-6">{{ p.jenis }}</td>
                                <td class="py-4 px-6 font-mono font-bold text-gray-800">Rp {{ p.nominal.toLocaleString('id-ID') }}</td>
                                <td class="py-4 px-6 text-gray-600">{{ p.tgl }}</td>
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                        {{ p.status }}
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
