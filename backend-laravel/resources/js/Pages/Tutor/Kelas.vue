<script setup>
import { computed } from 'vue'
import { Head, Link, usePage } from '@inertiajs/vue3'
import TutorLayout from '@/Layouts/TutorLayout.vue'

const props = defineProps({
    kelas: Array,
})

const page = usePage()
const user = computed(() => page.props.auth?.user)
</script>

<template>
    <Head title="Kelas Binaan Saya — Portal Tutor" />

    <TutorLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900">Daftar Kelas yang Diampu</h2>
                    <p class="text-xs sm:text-sm text-gray-500">Pantau kehadiran, warga belajar, dan pelaksanaan pembelajaran per rombel</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                <div v-for="item in kelas" :key="item.id" class="bg-white rounded-3xl border border-gray-100 shadow-sm p-5 sm:p-6 space-y-4 hover:shadow-md transition flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 uppercase">
                                {{ item.angkatan?.program?.nama_program || 'Kesetaraan' }}
                            </span>
                            <span class="text-xs font-mono font-bold text-indigo-600">Semester {{ item.semester }}</span>
                        </div>
                        <h3 class="text-base sm:text-lg font-bold text-gray-900 mt-3">{{ item.nama_kelas }}</h3>
                        <p class="text-xs text-gray-400 mt-1">Angkatan: {{ item.angkatan?.nama_angkatan }}</p>
                    </div>

                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 flex items-center justify-between text-sm">
                        <span class="text-gray-500 text-xs sm:text-sm">Jumlah Siswa:</span>
                        <span class="font-bold text-gray-900 text-xs sm:text-sm">{{ item.siswas?.length || 0 }} Orang</span>
                    </div>

                    <div class="pt-2 flex gap-2">
                        <Link :href="`/tutor/absensi/${item.id}`" class="flex-1 py-2.5 px-3 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl text-center text-xs font-bold transition shadow-sm">
                            Presensi & Kehadiran &rarr;
                        </Link>
                    </div>
                </div>

                <div v-if="!kelas || kelas.length === 0" class="col-span-full py-12 text-center text-gray-400 bg-white rounded-2xl border border-gray-100 text-sm">
                    Belum ada kelas binaan yang ditugaskan kepada Anda. Hubungi administrator PKBM.
                </div>
            </div>
        </div>
    </TutorLayout>
</template>
