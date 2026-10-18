<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import SiswaLayout from '@/Layouts/SiswaLayout.vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)

const raportList = [
    { semester: 'Semester 1', tahun: '2023/2024', rata_rata: 88.5, status: 'Naik Tingkat', file: 'raport_semester_1.pdf' },
    { semester: 'Semester 2', tahun: '2023/2024', rata_rata: 89.2, status: 'Naik Tingkat', file: 'raport_semester_2.pdf' },
    { semester: 'Semester 3', tahun: '2024/2025', rata_rata: 89.8, status: 'Aktif Berjalan', file: 'raport_semester_3.pdf' },
]
</script>

<template>
    <Head title="Buku Raport — Portal Siswa" />

    <SiswaLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <div>
                <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900">Arsip Raport Hasil Belajar</h2>
                <p class="text-xs sm:text-sm text-gray-500">Unduh lembar hasil penilaian dan capaian kompetensi semester berformat PDF</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 sm:gap-6">
                <div v-for="(item, i) in raportList" :key="i" class="bg-white rounded-3xl border border-gray-100 shadow-sm p-5 sm:p-6 space-y-4 hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700">
                                {{ item.tahun }}
                            </span>
                            <span class="text-xs font-bold text-emerald-600">{{ item.status }}</span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 mt-3">{{ item.semester }}</h3>
                        <p class="text-xs text-gray-400 mt-1">Paket C (Setara SMA) • PKBM Nusantara Banua</p>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-2xl flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Nilai Rata-rata</span>
                        <span class="text-2xl font-black text-indigo-600">{{ item.rata_rata }}</span>
                    </div>

                    <a
                        :href="`/admin/raports/download/${item.file}`"
                        target="_blank"
                        class="w-full py-2.5 px-4 bg-indigo-600 hover:bg-indigo-700 active:scale-[0.98] text-white rounded-xl text-center text-xs font-bold transition shadow-sm flex items-center justify-center gap-2"
                    >
                        <span>📥</span>
                        <span>Unduh Dokumen PDF</span>
                    </a>
                </div>
            </div>
        </div>
    </SiswaLayout>
</template>
