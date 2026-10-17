<script setup>
import { ref, computed } from 'vue'
import { Head, Link, usePage, router } from '@inertiajs/vue3'

const props = defineProps({
    materis: Object,
    kelasList: Array,
    mapelList: Array,
})

const page = usePage()
const user = computed(() => page.props.auth?.user)

const showModal = ref(false)
const form = ref({
    kelas_id: '',
    mata_pelajaran_id: '',
    judul: '',
    deskripsi: '',
    tipe: 'pdf',
    link_url: '',
    file: null,
})

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

    router.post('/tutor/materi', formData, {
        onSuccess: () => {
            showModal.value = false
            form.value = { kelas_id: '', mata_pelajaran_id: '', judul: '', deskripsi: '', tipe: 'pdf', link_url: '', file: null }
        }
    })
}
</script>

<template>
    <Head title="Modul Pembelajaran — Portal Tutor" />

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
                        <p class="text-xs text-gray-500">Pusat Pembelajaran Digital</p>
                    </div>
                </div>

                <!-- Nav Links -->
                <nav class="hidden md:flex items-center gap-1">
                    <Link href="/tutor/dashboard" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Dashboard
                    </Link>
                    <Link href="/tutor/kelas" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Daftar Kelas
                    </Link>
                    <Link href="/tutor/materi" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 transition">
                        Modul Belajar
                    </Link>
                    <Link href="/tutor/tugas" class="px-3 py-1.5 rounded-lg text-xs font-semibold text-gray-600 hover:bg-gray-100 transition">
                        Tugas & Evaluasi
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
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-3xl border border-gray-100 shadow-sm">
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-900">Kelola Modul & Materi Ajar</h2>
                    <p class="text-sm text-gray-500 mt-1">Unggah bahan ajar digital, presentasi, atau video interaktif untuk siswa bimbingan Anda.</p>
                </div>
                <button
                    @click="showModal = true"
                    class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-emerald-600/20 transition flex items-center gap-2"
                >
                    <span>+</span>
                    <span>Tambah Materi Baru</span>
                </button>
            </div>

            <!-- List Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div
                    v-for="item in materis.data"
                    :key="item.id"
                    class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm hover:shadow-md transition flex flex-col justify-between space-y-4"
                >
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded-lg border border-emerald-100 uppercase">
                                {{ item.tipe }}
                            </span>
                            <span class="text-xs text-gray-400 font-medium">{{ item.kelas?.nama_kelas }}</span>
                        </div>
                        <h3 class="font-extrabold text-gray-900 text-base leading-snug">{{ item.judul }}</h3>
                        <p class="text-xs text-gray-500 line-clamp-3 leading-relaxed">{{ item.deskripsi }}</p>
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs text-gray-500">📚 {{ item.mata_pelajaran?.nama_mapel }}</span>
                        <a
                            v-if="item.link_url"
                            :href="item.link_url"
                            target="_blank"
                            class="px-3 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-xs font-bold rounded-lg transition"
                        >
                            Buka &rarr;
                        </a>
                        <a
                            v-else-if="item.file_path"
                            :href="item.file_path"
                            target="_blank"
                            class="px-3 py-1 bg-blue-50 hover:bg-blue-100 text-blue-700 text-xs font-bold rounded-lg transition"
                        >
                            Unduh &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <div v-if="materis.data.length === 0" class="bg-white rounded-3xl p-12 text-center border border-gray-100 text-gray-400">
                <p class="text-sm font-semibold">Belum ada modul yang diunggah.</p>
            </div>
        </main>

        <!-- Modal Tambah Materi -->
        <div v-if="showModal" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 space-y-6 shadow-2xl">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h2 class="text-lg font-bold text-gray-900">Unggah Materi Belajar</h2>
                    <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <form @submit.prevent="submitMateri" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Judul Materi</label>
                        <input
                            v-model="form.judul"
                            type="text"
                            required
                            placeholder="Contoh: Modul 02: Pemasaran & Media Sosial"
                            class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-500 outline-none"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Kelas</label>
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
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tipe</label>
                            <select
                                v-model="form.tipe"
                                class="w-full text-xs px-3 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-500 outline-none"
                            >
                                <option value="pdf">📄 PDF / E-Book</option>
                                <option value="video">🎥 Video Link</option>
                                <option value="dokumen">📝 Dokumen File</option>
                                <option value="artikel">📰 Artikel</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Link Video / URL</label>
                            <input
                                v-model="form.link_url"
                                type="url"
                                placeholder="https://..."
                                class="w-full text-xs px-3.5 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-500 outline-none"
                            />
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">File Dokumen / PDF (Opsional)</label>
                        <input
                            @change="handleFileUpload"
                            type="file"
                            class="w-full text-xs px-3.5 py-2 rounded-xl border border-gray-200 focus:border-emerald-500 outline-none file:mr-3 file:py-1 file:px-2.5 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Deskripsi Singkat</label>
                        <textarea
                            v-model="form.deskripsi"
                            rows="3"
                            placeholder="Instruksi bacaan atau ringkasan..."
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
                            Simpan Materi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>
