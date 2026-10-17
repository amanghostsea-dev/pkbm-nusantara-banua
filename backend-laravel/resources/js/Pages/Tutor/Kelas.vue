<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'

const props = defineProps({
    kelas: Array,
})

const page = usePage()
const user = computed(() => page.props.auth?.user)
</script>

<template>
    <Head title="Kelas Binaan Saya — Portal Tutor" />

    <div class="min-h-screen bg-slate-50 font-sans">
        <!-- Top Navigation -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-emerald-500/20">
                        👨‍🏫
                    </div>
                    <div>
                        <h1 class="text-base font-bold text-gray-900 leading-tight">Portal Tutor PKBM</h1>
                        <p class="text-xs text-gray-500">Rombongan Belajar & Bimbingan</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <Link href="/tutor/dashboard" class="px-3 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-100 text-xs font-semibold text-gray-700 transition">
                        &larr; Dashboard
                    </Link>
                    <Link href="/logout" method="post" as="button" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-xs font-semibold text-gray-700 transition">
                        Keluar
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-900">Daftar Kelas yang Diampu</h2>
                    <p class="text-sm text-gray-500">Pantau kehadiran, warga belajar, dan pelaksanaan pembelajaran per rombel</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="item in kelas" :key="item.id" class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-4 hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 uppercase">
                                {{ item.angkatan?.program?.nama_program || 'Kesetaraan' }}
                            </span>
                            <span class="text-xs font-mono font-bold text-indigo-600">Semester {{ item.semester }}</span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 mt-3">{{ item.nama_kelas }}</h3>
                        <p class="text-xs text-gray-400 mt-1">Angkatan: {{ item.angkatan?.nama_angkatan }}</p>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between text-sm">
                        <span class="text-gray-500">Jumlah Siswa:</span>
                        <span class="font-bold text-gray-900">{{ item.siswas?.length || 0 }} Orang</span>
                    </div>

                    <div class="pt-2 flex gap-2">
                        <Link :href="`/tutor/absensi/${item.id}`" class="flex-1 py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-center text-xs font-bold transition shadow-sm">
                            Presensi & Kehadiran &rarr;
                        </Link>
                    </div>
                </div>

                <div v-if="!kelas || kelas.length === 0" class="col-span-full py-12 text-center text-gray-400 bg-white rounded-2xl border border-gray-100">
                    Belum ada kelas binaan yang ditugaskan kepada Anda. Hubungi administrator PKBM.
                </div>
            </div>
        </main>
    </div>
</template>
