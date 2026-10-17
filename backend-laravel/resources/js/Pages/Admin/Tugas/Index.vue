<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    tugasList: Object,
    kelasList: Array,
    mapelList: Array,
    filters: Object,
})

const showModal = ref(false)
const search = ref(props.filters?.search || '')
const selectedKelas = ref(props.filters?.kelas_id || '')
const selectedMapel = ref(props.filters?.mata_pelajaran_id || '')

const form = ref({
    kelas_id: '',
    mata_pelajaran_id: '',
    judul: '',
    deskripsi: '',
    deadline: '',
    poin_maksimal: 100,
    file_lampiran: null,
})

function applyFilter() {
    router.get('/admin/tugas', {
        search: search.value,
        kelas_id: selectedKelas.value,
        mata_pelajaran_id: selectedMapel.value,
    }, { preserveState: true })
}

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

    router.post('/admin/tugas', formData, {
        onSuccess: () => {
            showModal.value = false
            form.value = { kelas_id: '', mata_pelajaran_id: '', judul: '', deskripsi: '', deadline: '', poin_maksimal: 100, file_lampiran: null }
        }
    })
}

function deleteTugas(id) {
    if (confirm('Yakin ingin menghapus tugas ini? Semua pengumpulan tugas terkait juga akan terhapus.')) {
        router.delete(`/admin/tugas/${id}`)
    }
}
</script>

<template>
    <Head title="Manajemen Tugas & Evaluasi Siswa — Admin PKBM" />

    <AdminLayout>
        <template #breadcrumb>
            <span class="text-gray-400">Pembelajaran</span>
            <span class="text-gray-300">/</span>
            <span class="text-gray-800 font-semibold">Tugas Siswa</span>
        </template>

        <div class="space-y-6">
            <!-- Header Banner -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Manajemen Tugas & Evaluasi Mandiri</h1>
                    <p class="text-sm text-gray-500 mt-1">Buat instruksi penugasan, pantau tenggat waktu, dan evaluasi hasil belajar warga belajar.</p>
                </div>
                <button
                    @click="showModal = true"
                    class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-indigo-600/20 transition flex items-center gap-2 self-start sm:self-auto"
                >
                    <span>+</span>
                    <span>Buat Tugas Baru</span>
                </button>
            </div>

            <!-- Filters -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-wrap gap-3 items-center justify-between">
                <div class="flex flex-wrap gap-2.5 items-center flex-1">
                    <input
                        v-model="search"
                        @keyup.enter="applyFilter"
                        type="text"
                        placeholder="Cari tugas..."
                        class="px-3.5 py-2 text-xs rounded-xl border border-gray-200 focus:border-indigo-500 outline-none w-64"
                    />
                    <select
                        v-model="selectedKelas"
                        @change="applyFilter"
                        class="px-3 py-2 text-xs rounded-xl border border-gray-200 focus:border-indigo-500 outline-none"
                    >
                        <option value="">Semua Kelas</option>
                        <option v-for="k in kelasList" :key="k.id" :value="k.id">{{ k.nama_kelas }}</option>
                    </select>
                    <select
                        v-model="selectedMapel"
                        @change="applyFilter"
                        class="px-3 py-2 text-xs rounded-xl border border-gray-200 focus:border-indigo-500 outline-none"
                    >
                        <option value="">Semua Mata Pelajaran</option>
                        <option v-for="m in mapelList" :key="m.id" :value="m.id">{{ m.nama_mapel }}</option>
                    </select>
                </div>
                <button
                    @click="applyFilter"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition"
                >
                    Terapkan
                </button>
            </div>

            <!-- Tasks Table / Card List -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="py-4 px-6">Judul Tugas</th>
                                <th class="py-4 px-6">Kelas & Mapel</th>
                                <th class="py-4 px-6">Tenggat Waktu</th>
                                <th class="py-4 px-6">Pengumpulan</th>
                                <th class="py-4 px-6 text-right">Aksi</th>
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
                                    <div class="text-xs font-bold font-mono text-gray-800">
                                        {{ new Date(tugas.deadline).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' }) }}
                                    </div>
                                    <span
                                        :class="[
                                            'inline-block px-2 py-0.5 rounded text-[10px] font-bold mt-1 uppercase',
                                            new Date() > new Date(tugas.deadline) ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600'
                                        ]"
                                    >
                                        {{ new Date() > new Date(tugas.deadline) ? 'Tenggat Berakhir' : 'Aktif Berjalan' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-xl border border-indigo-100">
                                        <span>📥</span>
                                        <span>{{ tugas.pengumpulans_count }} Terkumpul</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="`/admin/tugas/${tugas.id}`"
                                            class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold text-xs rounded-xl transition"
                                        >
                                            Periksa & Nilai &rarr;
                                        </Link>
                                        <button
                                            @click="deleteTugas(tugas.id)"
                                            class="p-1.5 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition"
                                            title="Hapus Tugas"
                                        >
                                            🗑️
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="tugasList.data.length === 0" class="p-12 text-center text-gray-400">
                    <div class="text-4xl mb-2">📝</div>
                    <p class="text-sm font-semibold text-gray-600">Belum ada penugasan yang dibuat.</p>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Tugas -->
        <div v-if="showModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <h2 class="text-lg font-bold text-gray-900">Buat Instruksi Tugas Baru</h2>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form @submit.prevent="submitTugas" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Judul Penugasan</label>
                        <input
                            v-model="form.judul"
                            type="text"
                            required
                            placeholder="Contoh: Tugas 2: Pembuatan Analisis Swot Usaha"
                            class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 outline-none"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kelas Target</label>
                            <select
                                v-model="form.kelas_id"
                                required
                                class="w-full text-xs px-3 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 outline-none"
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
                                class="w-full text-xs px-3 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 outline-none"
                            >
                                <option value="" disabled>Pilih Mapel</option>
                                <option v-for="m in mapelList" :key="m.id" :value="m.id">{{ m.nama_mapel }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tenggat Waktu (Deadline)</label>
                            <input
                                v-model="form.deadline"
                                type="datetime-local"
                                required
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 outline-none"
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
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 outline-none"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Berkas Lampiran Soal / Panduan (Opsional)</label>
                        <input
                            @change="handleFileAttachment"
                            type="file"
                            class="w-full text-xs px-3.5 py-2 rounded-xl border border-gray-200 focus:border-indigo-500 outline-none file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Deskripsi & Petunjuk Pengerjaan</label>
                        <textarea
                            v-model="form.deskripsi"
                            rows="4"
                            required
                            placeholder="Jelaskan instruksi tugas secara rinci bagi siswa..."
                            class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-indigo-500 outline-none"
                        ></textarea>
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                        <button
                            type="button"
                            @click="showModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-gray-600 hover:bg-gray-100 transition"
                        >
                            Batal
                        </button>
                        <button
                            type="submit"
                            class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-md transition"
                        >
                            Publikasikan Tugas
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
