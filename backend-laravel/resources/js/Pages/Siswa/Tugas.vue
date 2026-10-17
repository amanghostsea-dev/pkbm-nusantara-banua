<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'

const props = defineProps({
    tugasList: Object,
    siswa: Object,
})

const submitModal = ref(false)
const selectedTugas = ref(null)
const submitForm = ref({
    catatan_siswa: '',
    file_tugas: null,
})

function openSubmit(tugas) {
    selectedTugas.value = tugas
    submitForm.value.catatan_siswa = tugas.my_submission?.catatan_siswa || ''
    submitForm.value.file_tugas = null
    submitModal.value = true
}

function handleFile(e) {
    submitForm.value.file_tugas = e.target.files[0]
}

function sendSubmission() {
    if (!selectedTugas.value) return
    const formData = new FormData()
    if (submitForm.value.catatan_siswa) formData.append('catatan_siswa', submitForm.value.catatan_siswa)
    if (submitForm.value.file_tugas) formData.append('file_tugas', submitForm.value.file_tugas)

    router.post(`/siswa-portal/tugas/${selectedTugas.value.id}/kumpul`, formData, {
        onSuccess: () => {
            submitModal.value = false
            selectedTugas.value = null
        }
    })
}
</script>

<template>
    <Head title="Pusat Tugas & Penugasan Mandiri — Portal Siswa" />

    <div class="min-h-screen bg-slate-50 font-sans">
        <!-- Top Navigation -->
        <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center font-bold text-xl shadow-md shadow-indigo-500/20">
                        👨‍🎓
                    </div>
                    <div>
                        <h1 class="text-base font-bold text-gray-900 leading-tight">Portal Siswa PKBM</h1>
                        <p class="text-xs text-gray-500">Pusat Tugas & Evaluasi Belajar</p>
                    </div>
                </div>

                <!-- Nav Links -->
                <nav class="hidden md:flex items-center gap-1">
                    <Link href="/siswa-portal/dashboard" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Dashboard
                    </Link>
                    <Link href="/siswa-portal/jadwal" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Jadwal
                    </Link>
                    <Link href="/siswa-portal/nilai" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Nilai
                    </Link>
                    <Link href="/siswa-portal/materi" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Modul Belajar
                    </Link>
                    <Link href="/siswa-portal/tugas" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-indigo-50 text-indigo-700 transition">
                        Tugas Siswa
                    </Link>
                </nav>

                <div class="flex items-center gap-3">
                    <Link href="/logout" method="post" as="button" class="px-3.5 py-1.5 rounded-lg border border-gray-200 hover:bg-gray-100 text-xs font-semibold text-gray-700 transition">
                        Keluar
                    </Link>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6">
            <!-- Header Banner -->
            <div class="bg-gradient-to-r from-indigo-700 via-blue-700 to-indigo-900 rounded-3xl p-6 sm:p-8 text-white shadow-xl shadow-indigo-600/10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur rounded-full text-xs font-semibold uppercase tracking-wider mb-2">
                        Pembelajaran Mandiri Terstruktur
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Daftar Tugas & Lembar Kerja</h2>
                    <p class="text-blue-100 text-sm mt-1 max-w-xl">
                        Selesaikan penugasan mandiri tepat waktu untuk melengkapi penilaian capaian kompetensi semester Anda.
                    </p>
                </div>
            </div>

            <!-- Task List Cards -->
            <div class="space-y-4">
                <div
                    v-for="tugas in tugasList.data"
                    :key="tugas.id"
                    class="bg-white rounded-3xl border border-gray-100 p-6 sm:p-8 shadow-sm hover:shadow-md transition space-y-5"
                >
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                        <div class="space-y-1.5">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-lg border border-indigo-100">
                                    {{ tugas.mata_pelajaran?.nama_mapel }}
                                </span>
                                <span class="text-xs text-gray-400">Poin Maks: {{ tugas.poin_maksimal }}</span>
                            </div>
                            <h3 class="text-lg font-extrabold text-gray-900">{{ tugas.judul }}</h3>
                            <p class="text-xs text-gray-500">
                                Batas Pengumpulan: <strong :class="tugas.is_overdue ? 'text-rose-600' : 'text-gray-800'">{{ new Date(tugas.deadline).toLocaleString('id-ID') }}</strong>
                            </p>
                        </div>

                        <!-- Status Badge -->
                        <div>
                            <span
                                v-if="tugas.is_graded"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-xl border border-emerald-100"
                            >
                                <span>✓</span>
                                <span>Nilai: {{ tugas.my_score }} / 100</span>
                            </span>
                            <span
                                v-else-if="tugas.is_submitted"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-700 text-xs font-bold rounded-xl border border-blue-100"
                            >
                                <span>🕒</span>
                                <span>Sudah Dikirim (Menunggu Nilai)</span>
                            </span>
                            <span
                                v-else-if="tugas.is_overdue"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-rose-50 text-rose-600 text-xs font-bold rounded-xl border border-rose-100"
                            >
                                <span>⚠️</span>
                                <span>Lewat Tenggat Waktu</span>
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-50 text-amber-700 text-xs font-bold rounded-xl border border-amber-100"
                            >
                                <span>✏️</span>
                                <span>Belum Dikerjakan</span>
                            </span>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="p-4 bg-gray-50 rounded-2xl text-xs sm:text-sm text-gray-700 leading-relaxed whitespace-pre-line border border-gray-100">
                        {{ tugas.deskripsi }}
                    </div>

                    <!-- Tutor Feedback if available -->
                    <div v-if="tugas.my_feedback" class="p-4 bg-emerald-50/60 rounded-2xl border border-emerald-100 text-xs space-y-1">
                        <span class="font-bold text-emerald-800 uppercase tracking-wider block">Catatan & Evaluasi Tutor:</span>
                        <p class="text-emerald-900 italic">"{{ tugas.my_feedback }}"</p>
                    </div>

                    <!-- Actions -->
                    <div class="pt-3 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <a
                                v-if="tugas.file_lampiran"
                                :href="tugas.file_lampiran"
                                target="_blank"
                                class="text-xs font-semibold text-indigo-600 hover:underline flex items-center gap-1"
                            >
                                <span>📎</span>
                                <span>Unduh Panduan / Soal Tutor</span>
                            </a>
                        </div>

                        <div class="flex items-center gap-2">
                            <button
                                @click="openSubmit(tugas)"
                                :class="[
                                    'px-5 py-2.5 rounded-xl font-bold text-xs shadow-sm transition flex items-center gap-2',
                                    tugas.is_submitted ? 'bg-gray-100 hover:bg-gray-200 text-gray-700' : 'bg-indigo-600 hover:bg-indigo-700 text-white shadow-indigo-600/20'
                                ]"
                            >
                                <span>{{ tugas.is_submitted ? '✏️ Perbarui Pengumpulan' : '📤 Kumpulkan Tugas' }}</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div v-if="tugasList.data.length === 0" class="bg-white rounded-3xl p-12 text-center border border-gray-100 text-gray-400">
                <div class="text-4xl mb-2">🎉</div>
                <p class="text-sm font-semibold text-gray-700">Tidak ada tugas aktif saat ini. Anda telah menyelesaikan semua penugasan!</p>
            </div>
        </main>

        <!-- Modal Submit Tugas -->
        <div v-if="submitModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Kumpulkan Tugas Belajar</h2>
                        <p class="text-xs text-gray-500">{{ selectedTugas?.judul }}</p>
                    </div>
                    <button @click="submitModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form @submit.prevent="sendSubmission" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Unggah Dokumen Tugas (PDF / Word / Foto Hasil)</label>
                        <input
                            @change="handleFile"
                            type="file"
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.zip"
                            class="w-full text-xs px-3.5 py-2 rounded-xl border border-gray-200 focus:border-indigo-500 outline-none file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Catatan Siswa / Jawaban Teks Singkat</label>
                        <textarea
                            v-model="submitForm.catatan_siswa"
                            rows="4"
                            placeholder="Tuliskan jawaban atau keterangan pengerjaan tugas kepada tutor..."
                            class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 outline-none"
                        ></textarea>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                        <button
                            type="button"
                            @click="submitModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition"
                        >
                            Kirimkan Sekarang
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
