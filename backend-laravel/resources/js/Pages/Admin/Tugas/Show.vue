<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    tugas: Object,
    pengumpulans: Array,
})

const gradingModal = ref(false)
const selectedSubmission = ref(null)
const gradeForm = ref({
    nilai: 85,
    feedback: '',
})

function openGrading(sub) {
    selectedSubmission.value = sub
    gradeForm.value.nilai = sub.nilai !== null ? sub.nilai : 85
    gradeForm.value.feedback = sub.feedback || ''
    gradingModal.value = true
}

function submitGrade() {
    if (!selectedSubmission.value) return
    router.post(`/admin/tugas/${props.tugas.id}/grade/${selectedSubmission.value.id}`, gradeForm.value, {
        onSuccess: () => {
            gradingModal.value = false
            selectedSubmission.value = null
        }
    })
}
</script>

<template>
    <Head :title="`Detail Penugasan: ${tugas.judul} — Admin PKBM`" />

    <AdminLayout>
        <template #breadcrumb>
            <Link href="/admin/tugas" class="text-gray-400 hover:text-gray-600">Tugas Siswa</Link>
            <span class="text-gray-300">/</span>
            <span class="text-gray-800 font-semibold">{{ tugas.judul }}</span>
        </template>

        <div class="space-y-6">
            <!-- Header Task Overview -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg border border-indigo-100">
                                {{ tugas.mata_pelajaran?.nama_mapel }}
                            </span>
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg border border-emerald-100">
                                {{ tugas.kelas?.nama_kelas }}
                            </span>
                        </div>
                        <h1 class="text-2xl font-black text-gray-900">{{ tugas.judul }}</h1>
                        <p class="text-xs text-gray-500 mt-1">
                            Tenggat Waktu: <strong class="text-gray-800">{{ new Date(tugas.deadline).toLocaleString('id-ID') }}</strong> • Poin Maksimal: {{ tugas.poin_maksimal }}
                        </p>
                    </div>

                    <div class="flex items-center gap-3">
                        <a
                            v-if="tugas.file_lampiran"
                            :href="tugas.file_lampiran"
                            target="_blank"
                            class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl transition flex items-center gap-1.5"
                        >
                            <span>📎</span>
                            <span>Unduh Berkas Panduan</span>
                        </a>
                        <Link
                            href="/admin/tugas"
                            class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold text-xs rounded-xl transition"
                        >
                            &larr; Kembali
                        </Link>
                    </div>
                </div>

                <!-- Description / Instructions -->
                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 text-xs sm:text-sm text-gray-700 leading-relaxed whitespace-pre-line">
                    <strong class="block text-xs uppercase font-bold text-gray-500 mb-1">Petunjuk Tugas:</strong>
                    {{ tugas.deskripsi }}
                </div>
            </div>

            <!-- Submissions Section -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden space-y-4 p-6 sm:p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Daftar Pengumpulan Siswa</h2>
                        <p class="text-xs text-gray-500">Hasil pengerjaan dan penilaian tugas warga belajar.</p>
                    </div>
                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-extrabold rounded-full">
                        {{ pengumpulans.length }} Siswa Mengumpulkan
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="py-3 px-4">Nama Siswa</th>
                                <th class="py-3 px-4">Waktu Kumpul</th>
                                <th class="py-3 px-4">Lampiran / Catatan</th>
                                <th class="py-3 px-4">Status & Nilai</th>
                                <th class="py-3 px-4 text-right">Evaluasi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="sub in pengumpulans" :key="sub.id" class="hover:bg-gray-50/70 transition">
                                <td class="py-4 px-4">
                                    <div class="font-bold text-gray-900">{{ sub.siswa?.nama_lengkap }}</div>
                                    <div class="text-xs text-gray-500 font-mono">NISN: {{ sub.siswa?.nisn }}</div>
                                </td>
                                <td class="py-4 px-4">
                                    <div class="text-xs text-gray-800 font-mono">
                                        {{ new Date(sub.tanggal_kumpul).toLocaleString('id-ID') }}
                                    </div>
                                    <span
                                        :class="[
                                            'text-[10px] font-bold px-2 py-0.5 rounded uppercase mt-0.5 inline-block',
                                            sub.status === 'terlambat' ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600'
                                        ]"
                                    >
                                        {{ sub.status === 'terlambat' ? 'Terlambat' : 'Tepat Waktu' }}
                                    </span>
                                </td>
                                <td class="py-4 px-4 max-w-xs">
                                    <a
                                        v-if="sub.file_tugas"
                                        :href="sub.file_tugas"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 text-xs text-indigo-600 hover:underline font-semibold"
                                    >
                                        <span>📄</span>
                                        <span>Lihat Dokumen Tugas</span>
                                    </a>
                                    <p v-if="sub.catatan_siswa" class="text-xs text-gray-600 mt-1 italic line-clamp-2">
                                        "{{ sub.catatan_siswa }}"
                                    </p>
                                </td>
                                <td class="py-4 px-4">
                                    <div v-if="sub.nilai !== null" class="flex items-center gap-2">
                                        <span class="text-xl font-black text-emerald-600">{{ sub.nilai }}</span>
                                        <span class="text-xs text-gray-400">/ 100</span>
                                    </div>
                                    <span v-else class="text-xs font-semibold text-amber-600 bg-amber-50 px-2 py-1 rounded-md">
                                        Menunggu Penilaian
                                    </span>
                                    <p v-if="sub.feedback" class="text-[11px] text-gray-500 mt-1 line-clamp-1">
                                        Eval: {{ sub.feedback }}
                                    </p>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <button
                                        @click="openGrading(sub)"
                                        class="px-3.5 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition"
                                    >
                                        {{ sub.nilai !== null ? 'Edit Nilai' : 'Beri Nilai' }}
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="pengumpulans.length === 0" class="p-8 text-center text-gray-400">
                    <p class="text-xs font-semibold">Belum ada siswa yang mengumpulkan tugas ini.</p>
                </div>
            </div>
        </div>

        <!-- Modal Beri Nilai & Evaluasi -->
        <div v-if="gradingModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 space-y-6 shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h2 class="text-base font-bold text-gray-900">Penilaian Tugas Siswa</h2>
                        <p class="text-xs text-gray-500">{{ selectedSubmission?.siswa?.nama_lengkap }}</p>
                    </div>
                    <button @click="gradingModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form @submit.prevent="submitGrade" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Skor Nilai (0 - 100)</label>
                        <input
                            v-model="gradeForm.nilai"
                            type="number"
                            min="0"
                            max="100"
                            required
                            class="w-full text-base font-extrabold text-emerald-600 px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 outline-none"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Catatan Evaluasi / Feedback Tutor</label>
                        <textarea
                            v-model="gradeForm.feedback"
                            rows="4"
                            placeholder="Tuliskan catatan apresiasi, koreksi pengerjaan, atau saran perbaikan bagi siswa..."
                            class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 outline-none"
                        ></textarea>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                        <button
                            type="button"
                            @click="gradingModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition"
                        >
                            Simpan Nilai
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
