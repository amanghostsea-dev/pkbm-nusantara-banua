<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    materis: Object,
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
    tipe: 'pdf',
    link_url: '',
    file: null,
})

function applyFilter() {
    router.get('/admin/materi', {
        search: search.value,
        kelas_id: selectedKelas.value,
        mata_pelajaran_id: selectedMapel.value,
    }, { preserveState: true })
}

function handleFileUpload(e) {
    form.value.file = e.target.files[0]
}

function submitMateri() {
    const formData = new FormData()
    formData.append('kelas_id', form.value.kelas_id)
    formData.append('mata_pelajaran_id', form.value.mata_pelajaran_id)
    formData.append('judul', form.value.judul)
    formData.append('deskripsi', form.value.deskripsi || '')
    formData.append('tipe', form.value.tipe)
    if (form.value.link_url) formData.append('link_url', form.value.link_url)
    if (form.value.file) formData.append('file', form.value.file)

    router.post('/admin/materi', formData, {
        onSuccess: () => {
            showModal.value = false
            form.value = { kelas_id: '', mata_pelajaran_id: '', judul: '', deskripsi: '', tipe: 'pdf', link_url: '', file: null }
        }
    })
}

function deleteMateri(id) {
    if (confirm('Yakin ingin menghapus materi ini?')) {
        router.delete(`/admin/materi/${id}`)
    }
}
</script>

<template>
    <Head title="Modul Belajar Digital (LMS) — Admin PKBM" />

    <AdminLayout>
        <template #breadcrumb>
            <span class="text-gray-400">Pembelajaran</span>
            <span class="text-gray-300">/</span>
            <span class="text-gray-800 font-semibold">Modul Belajar</span>
        </template>

        <div class="space-y-6">
            <!-- Header Banner -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <div>
                    <h1 class="text-2xl font-extrabold text-gray-900 tracking-tight">Pustaka Modul Belajar Digital</h1>
                    <p class="text-sm text-gray-500 mt-1">Kelola materi pelajaran, e-book PDF kesetaraan, dan video pembelajaran mandiri untuk siswa.</p>
                </div>
                <button
                    @click="showModal = true"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl font-bold text-sm shadow-lg shadow-emerald-600/20 transition flex items-center gap-2 self-start sm:self-auto"
                >
                    <span>+</span>
                    <span>Unggah Modul Baru</span>
                </button>
            </div>

            <!-- Filter Bar -->
            <div class="bg-white p-4 rounded-2xl border border-gray-100 shadow-sm flex flex-wrap gap-3 items-center justify-between">
                <div class="flex flex-wrap gap-2.5 items-center flex-1">
                    <input
                        v-model="search"
                        @keyup.enter="applyFilter"
                        type="text"
                        placeholder="Cari judul modul atau materi..."
                        class="px-3.5 py-2 text-xs rounded-xl border border-gray-200 focus:border-emerald-500 outline-none w-64"
                    />
                    <select
                        v-model="selectedKelas"
                        @change="applyFilter"
                        class="px-3 py-2 text-xs rounded-xl border border-gray-200 focus:border-emerald-500 outline-none"
                    >
                        <option value="">Semua Kelas</option>
                        <option v-for="k in kelasList" :key="k.id" :value="k.id">{{ k.nama_kelas }}</option>
                    </select>
                    <select
                        v-model="selectedMapel"
                        @change="applyFilter"
                        class="px-3 py-2 text-xs rounded-xl border border-gray-200 focus:border-emerald-500 outline-none"
                    >
                        <option value="">Semua Mata Pelajaran</option>
                        <option v-for="m in mapelList" :key="m.id" :value="m.id">{{ m.nama_mapel }}</option>
                    </select>
                </div>
                <button
                    @click="applyFilter"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-semibold rounded-xl transition"
                >
                    Terapkan Filter
                </button>
            </div>

            <!-- Grid Cards of Materi -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                <div
                    v-for="item in materis.data"
                    :key="item.id"
                    class="bg-white rounded-3xl border border-gray-100 shadow-sm hover:shadow-md transition p-6 flex flex-col justify-between space-y-4"
                >
                    <div class="space-y-3">
                        <div class="flex items-center justify-between gap-2">
                            <span
                                :class="[
                                    'px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider',
                                    item.tipe === 'video' ? 'bg-rose-50 text-rose-600 border border-rose-100' :
                                    item.tipe === 'pdf' ? 'bg-blue-50 text-blue-600 border border-blue-100' :
                                    'bg-emerald-50 text-emerald-600 border border-emerald-100'
                                ]"
                            >
                                {{ item.tipe === 'video' ? '🎥 Video' : item.tipe === 'pdf' ? '📄 PDF E-Book' : '📝 Dokumen' }}
                            </span>
                            <span class="text-xs text-gray-400">{{ item.kelas?.nama_kelas }}</span>
                        </div>

                        <h3 class="font-extrabold text-gray-900 text-base leading-snug hover:text-emerald-600 transition">
                            {{ item.judul }}
                        </h3>

                        <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">
                            {{ item.deskripsi || 'Tidak ada deskripsi pengantar tambahan.' }}
                        </p>

                        <div class="pt-2 flex items-center gap-2 text-xs text-gray-600 font-medium">
                            <span>📚 {{ item.mata_pelajaran?.nama_mapel }}</span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between gap-2">
                        <a
                            v-if="item.link_url"
                            :href="item.link_url"
                            target="_blank"
                            class="px-3.5 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5"
                        >
                            <span>🔗</span>
                            <span>Buka Materi</span>
                        </a>
                        <a
                            v-else-if="item.file_path"
                            :href="item.file_path"
                            target="_blank"
                            class="px-3.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5"
                        >
                            <span>⬇️</span>
                            <span>Unduh File</span>
                        </a>
                        <span v-else class="text-xs text-gray-400 italic">Hanya Teks</span>

                        <button
                            @click="deleteMateri(item.id)"
                            class="p-1.5 text-gray-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition"
                            title="Hapus Modul"
                        >
                            🗑️
                        </button>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-if="materis.data.length === 0" class="bg-white rounded-3xl p-12 text-center border border-gray-100">
                <div class="text-4xl mb-3">📖</div>
                <h3 class="text-base font-bold text-gray-800">Belum Ada Modul Belajar</h3>
                <p class="text-xs text-gray-500 mt-1 max-w-sm mx-auto">
                    Klik tombol "Unggah Modul Baru" untuk menambahkan modul PDF kesetaraan atau tautan video bagi warga belajar.
                </p>
            </div>
        </div>

        <!-- Modal Tambah Materi -->
        <div v-if="showModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <h2 class="text-lg font-bold text-gray-900">Unggah Modul / Materi Belajar</h2>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form @submit.prevent="submitMateri" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Judul Materi / Modul</label>
                        <input
                            v-model="form.judul"
                            type="text"
                            required
                            placeholder="Contoh: Modul 03: Kewirausahaan & Pemasaran Digital"
                            class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-500 outline-none"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kelas Target</label>
                            <select
                                v-model="form.kelas_id"
                                required
                                class="w-full text-xs px-3 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-500 outline-none"
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
                                class="w-full text-xs px-3 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-500 outline-none"
                            >
                                <option value="" disabled>Pilih Mapel</option>
                                <option v-for="m in mapelList" :key="m.id" :value="m.id">{{ m.nama_mapel }}</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Format Materi</label>
                            <select
                                v-model="form.tipe"
                                class="w-full text-xs px-3 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-500 outline-none"
                            >
                                <option value="pdf">📄 File PDF / E-Book</option>
                                <option value="video">🎥 Video (YouTube / Link)</option>
                                <option value="dokumen">📝 Dokumen Word / PPT</option>
                                <option value="artikel">📰 Artikel / Teks</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Link URL (Jika Ada)</label>
                            <input
                                v-model="form.link_url"
                                type="url"
                                placeholder="https://youtube.com/watch?v=..."
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-500 outline-none"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Unggah Berkas File (Opsional)</label>
                        <input
                            @change="handleFileUpload"
                            type="file"
                            accept=".pdf,.doc,.docx,.ppt,.pptx"
                            class="w-full text-xs px-3.5 py-2 rounded-xl border border-gray-200 focus:border-emerald-500 outline-none file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Deskripsi / Petunjuk Belajar</label>
                        <textarea
                            v-model="form.deskripsi"
                            rows="3"
                            placeholder="Berikan ringkasan materi atau instruksi bacaan..."
                            class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-500 outline-none"
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
                            class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-md transition"
                        >
                            Simpan Modul
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
