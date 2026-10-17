<script setup>
import { ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    siswa: Object,
    angkatans: Array,
    filters: Object,
    stats: Object,
})

// Modals state
const showPredictionModal = ref(false)
const showImportModal = ref(false)
const showBulkModal = ref(false)

// AI Prediction
const selectedPrediction = ref(null)
const loadingPrediction = ref(false)
const targetSiswa = ref(null)

const runPrediction = async (item) => {
    targetSiswa.value = item
    showPredictionModal.value = true
    loadingPrediction.value = true
    selectedPrediction.value = null
    try {
        const res = await fetch(`/admin/siswa/${item.id}/prediksi`)
        const data = await res.json()
        selectedPrediction.value = data
    } catch (e) {
        selectedPrediction.value = {
            prediksi: 'Error',
            probabilitas: 0,
            rekomendasi: ['Gagal menghubungi layanan microservice AI Python: ' + e.message],
            akurasi_model: '94.2%',
        }
    } finally {
        loadingPrediction.value = false
    }
}

// 1. Import CSV
const importForm = useForm({
    file: null,
    angkatan_id: '',
})

const handleFileChange = (e) => {
    importForm.file = e.target.files[0]
}

const submitImport = () => {
    importForm.post('/admin/siswa/import', {
        onSuccess: () => {
            showImportModal.value = false
            importForm.reset()
        },
    })
}

// 2. Bulk Multi-Row Entry
const bulkForm = useForm({
    angkatan_id: '',
    siswas: [
        { nisn: '', nama_lengkap: '', jenis_kelamin: 'L', tanggal_lahir: '2005-01-01', tempat_lahir: 'Jakarta', alamat: 'Alamat Siswa' },
        { nisn: '', nama_lengkap: '', jenis_kelamin: 'P', tanggal_lahir: '2005-01-01', tempat_lahir: 'Jakarta', alamat: 'Alamat Siswa' },
        { nisn: '', nama_lengkap: '', jenis_kelamin: 'L', tanggal_lahir: '2005-01-01', tempat_lahir: 'Jakarta', alamat: 'Alamat Siswa' },
    ],
})

const addRow = () => {
    bulkForm.siswas.push({
        nisn: '',
        nama_lengkap: '',
        jenis_kelamin: 'L',
        tanggal_lahir: '2005-01-01',
        tempat_lahir: 'Jakarta',
        alamat: 'Alamat Siswa',
    })
}

const removeRow = (index) => {
    if (bulkForm.siswas.length > 1) {
        bulkForm.siswas.splice(index, 1)
    }
}

const submitBulk = () => {
    const validRows = bulkForm.siswas.filter(s => s.nisn && s.nama_lengkap)
    if (validRows.length === 0) {
        alert('Mohon isi minimal satu baris siswa lengkap dengan NISN dan Nama Lengkap.')
        return
    }

    bulkForm.post('/admin/siswa/bulk', {
        onSuccess: () => {
            showBulkModal.value = false
            bulkForm.reset()
        },
    })
}

const deleteSiswa = (item) => {
    if (confirm(`Apakah Anda yakin ingin menghapus data siswa ${item.nama_lengkap}?`)) {
        router.delete(`/admin/siswa/${item.id}`)
    }
}
</script>

<template>
    <Head title="Manajemen Siswa PKBM" />

    <AdminLayout>
        <div class="p-6 max-w-7xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Data Siswa PKBM</h1>
                    <p class="text-gray-500 text-sm">Kelola siswa Paket A, Paket B, dan Paket C dengan fitur input data tunggal maupun kolektif</p>
                </div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Tombol Import Kolektif -->
                    <button
                        @click="showImportModal = true"
                        class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-xl text-xs shadow-sm transition flex items-center gap-1.5"
                    >
                        <span>📥</span>
                        <span>Import Excel / CSV</span>
                    </button>

                    <!-- Tombol Input Kolektif Multi-Row -->
                    <button
                        @click="showBulkModal = true"
                        class="px-3.5 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl text-xs shadow-sm transition flex items-center gap-1.5"
                    >
                        <span>📋</span>
                        <span>Tambah Kolektif (Banyak Siswa)</span>
                    </button>

                    <!-- Tambah Tunggal -->
                    <Link
                        href="/admin/siswa/create"
                        class="px-3.5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl text-xs shadow-sm transition flex items-center gap-1.5"
                    >
                        <span>+</span>
                        <span>Tambah Formulir</span>
                    </Link>

                    <!-- Export -->
                    <a
                        href="/admin/siswa/export"
                        class="px-3 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 font-semibold rounded-xl text-xs transition flex items-center gap-1"
                        title="Ekspor CSV"
                    >
                        <span>📊</span>
                        <span>Ekspor CSV</span>
                    </a>
                </div>
            </div>

            <!-- Stats Bar -->
            <div v-if="stats" class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs text-gray-500 font-medium uppercase">Total Siswa</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ stats.total || 0 }}</p>
                </div>
                <div class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs text-blue-600 font-medium uppercase">Paket A (SD)</p>
                    <p class="text-2xl font-bold text-blue-600 mt-1">{{ stats.paket_a || 0 }}</p>
                </div>
                <div class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs text-emerald-600 font-medium uppercase">Paket B (SMP)</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ stats.paket_b || 0 }}</p>
                </div>
                <div class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm">
                    <p class="text-xs text-amber-600 font-medium uppercase">Paket C (SMA)</p>
                    <p class="text-2xl font-bold text-amber-600 mt-1">{{ stats.paket_c || 0 }}</p>
                </div>
            </div>

            <!-- Data Table -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-left border-collapse text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="py-3 px-4">NISN</th>
                            <th class="py-3 px-4">Nama Lengkap</th>
                            <th class="py-3 px-4">Program / Angkatan</th>
                            <th class="py-3 px-4">L/P</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        <tr v-for="item in siswa.data" :key="item.id" class="hover:bg-gray-50/80 transition">
                            <td class="py-3 px-4 font-mono font-medium">{{ item.nisn }}</td>
                            <td class="py-3 px-4 font-semibold text-gray-800">{{ item.nama_lengkap }}</td>
                            <td class="py-3 px-4 text-gray-600">
                                {{ item.angkatan?.program?.nama_program || 'Paket C' }}
                            </td>
                            <td class="py-3 px-4">{{ item.jenis_kelamin }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-700">
                                    {{ item.status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right space-x-2">
                                <button
                                    @click="runPrediction(item)"
                                    class="px-2.5 py-1 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded text-xs font-semibold transition"
                                >
                                    🤖 AI Prediksi
                                </button>
                                <Link :href="`/admin/siswa/${item.id}`" class="text-blue-600 hover:underline text-xs font-medium">Detail</Link>
                                <Link :href="`/admin/siswa/${item.id}/edit`" class="text-amber-600 hover:underline text-xs font-medium">Edit</Link>
                                <button @click="deleteSiswa(item)" class="text-red-500 hover:underline text-xs font-medium">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="!siswa.data || siswa.data.length === 0">
                            <td colspan="6" class="py-8 text-center text-gray-400">
                                Belum ada data siswa terdaftar. Gunakan tombol Tambah Siswa atau Import CSV Kolektif.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- MODAL 1: IMPORT CSV / EXCEL KOLEKTIF -->
        <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-3xl shadow-2xl max-w-lg w-full p-6 space-y-5">
                <div class="flex items-center justify-between border-b pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">📥</span>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">Import Data Siswa Kolektif</h3>
                            <p class="text-xs text-gray-500">Unggah file CSV/Excel untuk menambahkan ratusan siswa sekaligus</p>
                        </div>
                    </div>
                    <button @click="showImportModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <div class="p-4 bg-blue-50 border border-blue-100 rounded-2xl flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold text-blue-900 uppercase">Format Template Resmi</div>
                        <p class="text-xs text-blue-700 mt-0.5">Gunakan format kolom yang sudah disesuaikan agar import berhasil.</p>
                    </div>
                    <a
                        href="/admin/siswa/template-import"
                        class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-bold transition shadow-sm flex items-center gap-1.5"
                    >
                        <span>⬇️</span>
                        <span>Download Template</span>
                    </a>
                </div>

                <form @submit.prevent="submitImport" class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Pilih Angkatan Tujuan (Default)</label>
                        <select v-model="importForm.angkatan_id" class="w-full px-3 py-2 border rounded-xl text-sm">
                            <option value="">Otomatis (Berdasarkan Kolom Angkatan / Aktif)</option>
                            <option v-for="a in angkatans" :key="a.id" :value="a.id">
                                {{ a.nama_angkatan }} - {{ a.program?.nama_program }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Pilih File CSV / Excel</label>
                        <input
                            type="file"
                            accept=".csv,.txt,.xlsx,.xls"
                            @change="handleFileChange"
                            required
                            class="w-full px-3 py-2 border border-dashed border-gray-300 rounded-xl text-sm bg-gray-50"
                        />
                        <p class="text-xs text-gray-400 mt-1">Akun login siswa akan dibuat otomatis dengan password default: <code class="font-bold">password</code></p>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t">
                        <button type="button" @click="showImportModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition">
                            Batal
                        </button>
                        <button
                            type="submit"
                            :disabled="importForm.processing || !importForm.file"
                            class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white rounded-xl text-sm font-bold shadow-md transition"
                        >
                            {{ importForm.processing ? 'Mengunggah & Memproses...' : 'Mulai Import Kolektif' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL 2: TAMBAH SISWA KOLEKTIF MULTI-ROW -->
        <div v-if="showBulkModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-3xl shadow-2xl max-w-4xl w-full p-6 space-y-5 max-h-[90vh] flex flex-col">
                <div class="flex items-center justify-between border-b pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">📋</span>
                        <div>
                            <h3 class="font-bold text-gray-900 text-lg">Tambah Siswa Kolektif (Multi-Row)</h3>
                            <p class="text-xs text-gray-500">Input beberapa warga belajar secara cepat dalam satu layar sekaligus</p>
                        </div>
                    </div>
                    <button @click="showBulkModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <div class="flex items-center justify-between">
                    <div class="w-72">
                        <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Angkatan Target</label>
                        <select v-model="bulkForm.angkatan_id" class="w-full px-3 py-1.5 border rounded-xl text-xs bg-gray-50">
                            <option value="">Gunakan Angkatan Aktif</option>
                            <option v-for="a in angkatans" :key="a.id" :value="a.id">
                                {{ a.nama_angkatan }} ({{ a.program?.nama_program }})
                            </option>
                        </select>
                    </div>

                    <button
                        type="button"
                        @click="addRow"
                        class="px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl text-xs font-bold transition flex items-center gap-1"
                    >
                        <span>+</span>
                        <span>Tambah Baris</span>
                    </button>
                </div>

                <!-- Table Rows -->
                <div class="flex-1 overflow-y-auto border border-gray-100 rounded-2xl">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50 text-gray-500 uppercase sticky top-0 border-b">
                            <tr>
                                <th class="py-2.5 px-3 w-10">No</th>
                                <th class="py-2.5 px-3">NISN *</th>
                                <th class="py-2.5 px-3">Nama Lengkap *</th>
                                <th class="py-2.5 px-3 w-24">L/P</th>
                                <th class="py-2.5 px-3">Tgl Lahir</th>
                                <th class="py-2.5 px-3">Tempat Lahir</th>
                                <th class="py-2.5 px-3 w-10"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="(row, idx) in bulkForm.siswas" :key="idx" class="hover:bg-gray-50/50">
                                <td class="py-2 px-3 text-gray-400 font-mono">{{ idx + 1 }}</td>
                                <td class="py-2 px-3">
                                    <input v-model="row.nisn" placeholder="1234..." type="text" class="w-full px-2 py-1 border rounded text-xs font-mono" />
                                </td>
                                <td class="py-2 px-3">
                                    <input v-model="row.nama_lengkap" placeholder="Nama Warga Belajar" type="text" class="w-full px-2 py-1 border rounded text-xs" />
                                </td>
                                <td class="py-2 px-3">
                                    <select v-model="row.jenis_kelamin" class="w-full px-2 py-1 border rounded text-xs">
                                        <option value="L">L</option>
                                        <option value="P">P</option>
                                    </select>
                                </td>
                                <td class="py-2 px-3">
                                    <input v-model="row.tanggal_lahir" type="date" class="w-full px-2 py-1 border rounded text-xs" />
                                </td>
                                <td class="py-2 px-3">
                                    <input v-model="row.tempat_lahir" type="text" class="w-full px-2 py-1 border rounded text-xs" />
                                </td>
                                <td class="py-2 px-3 text-center">
                                    <button type="button" @click="removeRow(idx)" class="text-rose-500 hover:text-rose-700 font-bold" title="Hapus baris">
                                        &times;
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between pt-3 border-t">
                    <span class="text-xs text-gray-400">{{ bulkForm.siswas.length }} baris input siswa</span>
                    <div class="flex gap-3">
                        <button type="button" @click="showBulkModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-sm font-semibold transition">
                            Batal
                        </button>
                        <button
                            type="button"
                            @click="submitBulk"
                            :disabled="bulkForm.processing"
                            class="px-6 py-2 bg-purple-600 hover:bg-purple-700 disabled:opacity-50 text-white rounded-xl text-sm font-bold shadow-md transition"
                        >
                            {{ bulkForm.processing ? 'Menyimpan Semua Data...' : 'Simpan Semua Siswa' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL 3: AI PREDICTION -->
        <div v-if="showPredictionModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl max-w-lg w-full p-6 space-y-4 border border-gray-100">
                <div class="flex items-center justify-between border-b pb-3">
                    <div class="flex items-center gap-2">
                        <span class="text-2xl">🤖</span>
                        <div>
                            <h3 class="font-bold text-gray-900">Analisis AI Kelulusan Siswa</h3>
                            <p class="text-xs text-gray-500">{{ targetSiswa?.nama_lengkap }} (NISN: {{ targetSiswa?.nisn }})</p>
                        </div>
                    </div>
                    <button @click="showPredictionModal = false" class="text-gray-400 hover:text-gray-600 text-xl font-bold">&times;</button>
                </div>

                <div v-if="loadingPrediction" class="py-8 text-center space-y-3">
                    <div class="inline-block animate-spin text-3xl text-indigo-600">⏳</div>
                    <p class="text-sm text-gray-600">Sedang menjalankan inferensi Machine Learning...</p>
                </div>

                <div v-else-if="selectedPrediction" class="space-y-4">
                    <div class="p-4 rounded-xl flex items-center justify-between"
                        :class="selectedPrediction.prediksi === 'Lulus' ? 'bg-emerald-50 border border-emerald-200 text-emerald-800' : 'bg-rose-50 border border-rose-200 text-rose-800'"
                    >
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider">Hasil Prediksi</span>
                            <h4 class="text-xl font-black">{{ selectedPrediction.prediksi }}</h4>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold">Tingkat Keyakinan</span>
                            <div class="text-lg font-bold">{{ ((selectedPrediction.probabilitas || 0) * 100).toFixed(1) }}%</div>
                        </div>
                    </div>

                    <div>
                        <h5 class="text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Rekomendasi AI untuk Siswa</h5>
                        <ul class="space-y-1.5">
                            <li v-for="(rec, i) in (selectedPrediction.rekomendasi || [])" :key="i" class="text-xs text-gray-700 flex items-start gap-2 bg-gray-50 p-2.5 rounded-lg border border-gray-100">
                                <span class="text-indigo-600 font-bold">•</span>
                                <span>{{ rec }}</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-2 border-t flex justify-between items-center text-xs text-gray-400">
                        <span>Model: Gradient Boosting Classifier</span>
                        <span>Akurasi: {{ selectedPrediction.akurasi_model || '94.2%' }}</span>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button @click="showPredictionModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg text-sm font-semibold transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
