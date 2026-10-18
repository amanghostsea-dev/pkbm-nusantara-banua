<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import TutorLayout from '@/Layouts/TutorLayout.vue'

const props = defineProps({
    tugasList: Object,
    kelasList: Array,
    mapelList: Array,
})

const showModal = ref(false)
const form = ref({
    kelas_id: '',
    mata_pelajaran_id: '',
    judul: '',
    deskripsi: '',
    deadline: '',
    poin_maksimal: 100,
    file_lampiran: null,
})

function handleFileAttachment(e) {
    form.value.file_lampiran = e.target.files[0]
}

function submitTugas() {
    const formData = new FormData()
    formData.append('kelas_id', form.value.kelas_id)
    formData.append('mata_pelajaran_id', form.value.mata_pelajaran_id)
    formData.append('judul', form.value.judul)
    formData.append('deskripsi', form.value.deskripsi)
    formData.append('deadline', form.value.deadline)
    formData.append('poin_maksimal', form.value.poin_maksimal)
    if (form.value.file_lampiran) formData.append('file_lampiran', form.value.file_lampiran)

    router.post('/tutor/tugas', formData, {
        onSuccess: () => {
            showModal.value = false
            form.value = { kelas_id: '', mata_pelajaran_id: '', judul: '', deskripsi: '', deadline: '', poin_maksimal: 100, file_lampiran: null }
        }
    })
}
</script>

<template>
    <Head title="Tugas & Evaluasi — Portal Tutor" />

    <TutorLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-5 sm:p-6 rounded-3xl border border-gray-100 shadow-sm">
                <div>
                    <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900">Kelola Tugas & Penilaian</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Berikan penugasan mandiri terstruktur dan nilai hasil pengerjaan warga belajar.</p>
                </div>
                <button
                    @click="showModal = true"
                    class="w-full sm:w-auto justify-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-bold text-xs sm:text-sm rounded-xl shadow-lg shadow-indigo-600/20 transition flex items-center gap-2"
                >
                    <span>+</span>
                    <span>Buat Tugas Baru</span>
                </button>
            </div>

            <!-- List Table with Responsive Overflow -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm min-w-[650px]">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="py-4 px-6">Judul Tugas</th>
                                <th class="py-4 px-6">Kelas & Mapel</th>
                                <th class="py-4 px-6">Deadline</th>
                                <th class="py-4 px-6">Pengumpulan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="tugas in tugasList.data" :key="tugas.id" class="hover:bg-gray-50/70 transition">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-gray-900">{{ tugas.judul }}</div>
                                    <div class="text-xs text-gray-500 line-clamp-1 max-w-sm mt-0.5">{{ tugas.deskripsi }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="font-semibold text-gray-800">{{ tugas.mata_pelajaran?.nama_mapel }}</div>
                                    <div class="text-xs text-indigo-600 font-medium">{{ tugas.kelas?.nama_kelas }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="text-xs font-bold text-gray-800">
                                        {{ new Date(tugas.deadline).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
                                    </div>
                                    <span
                                        :class="[
                                            'text-[10px] font-bold px-2 py-0.5 rounded uppercase mt-0.5 inline-block',
                                            new Date() > new Date(tugas.deadline) ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600'
                                        ]"
                                    >
                                        {{ new Date() > new Date(tugas.deadline) ? 'Tenggat Lewat' : 'Aktif' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-xl border border-indigo-100">
                                        📥 {{ tugas.pengumpulans_count }} Siswa Mengumpulkan
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="tugasList.data.length === 0" class="p-12 text-center text-gray-400">
                    <p class="text-sm font-semibold">Belum ada tugas yang dibuat.</p>
                </div>
            </div>
        </div>

        <!-- Modal Buat Tugas (Responsive) -->
        <div v-if="showModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
            <div class="bg-white rounded-3xl max-w-lg w-full p-5 sm:p-7 space-y-5 shadow-2xl my-8 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h2 class="text-base sm:text-lg font-bold text-gray-900">Buat Penugasan Baru</h2>
                    <button @click="showModal = false" class="w-8 h-8 rounded-lg flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 text-xl font-bold">&times;</button>
                </div>

                <form @submit.prevent="submitTugas" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Judul Tugas</label>
                        <input
                            v-model="form.judul"
                            type="text"
                            required
                            placeholder="Contoh: Latihan Soal Persiapan Ujian Akhir"
                            class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 outline-none"
                        />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kelas Target</label>
                            <select
                                v-model="form.kelas_id"
                                required
                                class="w-full text-xs sm:text-sm px-3 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 outline-none"
                            >
                                <option value="" disabled>Pilih Kelas</option>
                                <option v-for="k in kelasList" :key="k.id" :value="k.id">{{ k.nama_kelas }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Mata Pelajaran</label>
                            <select
                                v-model="form.mata_pelajaran_id"
                                required
                                class="w-full text-xs sm:text-sm px-3 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 outline-none"
                            >
                                <option value="" disabled>Pilih Mapel</option>
                                <option v-for="m in mapelList" :key="m.id" :value="m.id">{{ m.nama_mapel }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tenggat Waktu</label>
                            <input
                                v-model="form.deadline"
                                type="datetime-local"
                                required
                                class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 outline-none"
                            />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Poin Maksimal</label>
                            <input
                                v-model="form.poin_maksimal"
                                type="number"
                                min="10"
                                max="100"
                                required
                                class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 outline-none"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Petunjuk / Deskripsi Tugas</label>
                        <textarea
                            v-model="form.deskripsi"
                            rows="4"
                            required
                            placeholder="Petunjuk pengerjaan tugas secara rinci..."
                            class="w-full text-xs sm:text-sm px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 outline-none"
                        ></textarea>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                        <button
                            type="button"
                            @click="showModal = false"
                            class="px-4 py-2.5 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 active:scale-95 text-white font-bold text-xs rounded-xl shadow-md transition"
                        >
                            Simpan Tugas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </TutorLayout>
</template>
