<script setup>
import { ref, computed, watch } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    nilais: Object,
    kelasList: Array,
    mapelList: Array,
})

const activeTab = ref('rekap') // 'rekap' | 'bulk_input'
const selectedKelasId = ref('')
const selectedMapelId = ref('')
const selectedSemester = ref(1)

const bulkForm = useForm({
    nilais: [],
})

const currentKelas = computed(() => {
    return (props.kelasList || []).find(k => k.id === selectedKelasId.value)
})

const currentMapel = computed(() => {
    return (props.mapelList || []).find(m => m.id === selectedMapelId.value)
})

// Saat kelas berganti, inisialisasi daftar siswa untuk form kolektif
watch([selectedKelasId, selectedMapelId, selectedSemester], () => {
    if (!currentKelas.value) {
        bulkForm.nilais = []
        return
    }

    const siswas = currentKelas.value.siswas || []
    bulkForm.nilais = siswas.map(s => ({
        siswa_id: s.id,
        siswa_nama: s.nama_lengkap,
        siswa_nisn: s.nisn,
        kelas_id: currentKelas.value.id,
        mata_pelajaran_id: selectedMapelId.value || (props.mapelList?.[0]?.id || 1),
        semester: selectedSemester.value,
        nilai_tugas: 80,
        nilai_uts: 80,
        nilai_uas: 85,
    }))
})

// Quick Fill helper
const quickTugas = ref('')
const quickUts = ref('')
const quickUas = ref('')

const applyQuickTugas = () => {
    if (quickTugas.value !== '') {
        bulkForm.nilais.forEach(row => { row.nilai_tugas = Number(quickTugas.value) })
    }
}

const applyQuickUts = () => {
    if (quickUts.value !== '') {
        bulkForm.nilais.forEach(row => { row.nilai_uts = Number(quickUts.value) })
    }
}

const applyQuickUas = () => {
    if (quickUas.value !== '') {
        bulkForm.nilais.forEach(row => { row.nilai_uas = Number(quickUas.value) })
    }
}

const calculateAkhir = (row) => {
    const t = Number(row.nilai_tugas) || 0
    const uts = Number(row.nilai_uts) || 0
    const uas = Number(row.nilai_uas) || 0
    return ((t * 0.3) + (uts * 0.3) + (uas * 0.4)).toFixed(1)
}

const submitBulkNilai = () => {
    if (!selectedKelasId.value || !selectedMapelId.value) {
        alert('Mohon pilih Kelas dan Mata Pelajaran terlebih dahulu.')
        return
    }

    if (bulkForm.nilais.length === 0) {
        alert('Tidak ada siswa di kelas ini.')
        return
    }

    bulkForm.post('/admin/nilais/bulk', {
        onSuccess: () => {
            alert('Nilai kolektif berhasil disimpan untuk seluruh rombel!')
            activeTab.value = 'rekap'
        },
    })
}
</script>

<template>
    <Head title="Rekap & Input Nilai Siswa PKBM" />

    <AdminLayout>
        <div class="p-6 max-w-7xl mx-auto space-y-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Penilaian Hasil Belajar</h1>
                    <p class="text-gray-500 text-sm">Rekapitulasi dan input nilai akademik secara kolektif per rombongan belajar</p>
                </div>
                <div class="flex gap-2">
                    <button
                        @click="activeTab = 'rekap'"
                        :class="['px-4 py-2 rounded-xl text-xs font-bold transition', activeTab === 'rekap' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white border text-gray-700 hover:bg-gray-50']"
                    >
                        📊 Rekap Nilai Masuk
                    </button>
                    <button
                        @click="activeTab = 'bulk_input'"
                        :class="['px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5', activeTab === 'bulk_input' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white border text-gray-700 hover:bg-gray-50']"
                    >
                        <span>⚡</span>
                        <span>Input Nilai Kolektif Satu Rombel</span>
                    </button>
                </div>
            </div>

            <!-- TAB 1: REKAPITULASI TABLE -->
            <div v-if="activeTab === 'rekap'" class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b border-gray-100 text-gray-500 uppercase text-xs">
                        <tr>
                            <th class="py-3.5 px-4">Nama Siswa</th>
                            <th class="py-3.5 px-4">Kelas</th>
                            <th class="py-3.5 px-4">Mata Pelajaran</th>
                            <th class="py-3.5 px-4">Tugas</th>
                            <th class="py-3.5 px-4">UTS</th>
                            <th class="py-3.5 px-4">UAS</th>
                            <th class="py-3.5 px-4">Nilai Akhir</th>
                            <th class="py-3.5 px-4">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        <tr v-for="item in (nilais.data || nilais)" :key="item.id" class="hover:bg-gray-50/80 transition">
                            <td class="py-3.5 px-4">
                                <div class="font-semibold text-gray-900">{{ item.siswa?.nama_lengkap || 'Siswa' }}</div>
                                <div class="text-xs text-gray-400 font-mono">{{ item.siswa?.nisn }}</div>
                            </td>
                            <td class="py-3.5 px-4 font-medium">{{ item.kelas?.nama_kelas || '-' }}</td>
                            <td class="py-3.5 px-4">{{ item.mata_pelajaran?.nama_mapel || '-' }}</td>
                            <td class="py-3.5 px-4">{{ item.nilai_tugas ?? '-' }}</td>
                            <td class="py-3.5 px-4">{{ item.nilai_uts ?? '-' }}</td>
                            <td class="py-3.5 px-4">{{ item.nilai_uas ?? '-' }}</td>
                            <td class="py-3.5 px-4 font-black text-indigo-600">{{ item.nilai_akhir ?? '-' }}</td>
                            <td class="py-3.5 px-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                    :class="(item.nilai_akhir || 0) >= (item.mata_pelajaran?.kkm || 65) ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'">
                                    {{ (item.nilai_akhir || 0) >= (item.mata_pelajaran?.kkm || 65) ? 'Tuntas' : 'Remedial' }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="!(nilais.data?.length || nilais?.length)">
                            <td colspan="8" class="py-12 text-center text-gray-400">
                                Belum ada rekap nilai. Gunakan tab "Input Nilai Kolektif Satu Rombel" di atas untuk memasukkan nilai seluruh kelas sekaligus.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- TAB 2: BULK INPUT SATU ROMBEL -->
            <div v-if="activeTab === 'bulk_input'" class="space-y-5">
                <!-- Selector Card -->
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-gray-900 text-base">Pilih Rombongan Belajar & Mata Pelajaran</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Rombel / Kelas</label>
                            <select v-model="selectedKelasId" class="w-full px-3 py-2 border rounded-xl text-sm bg-gray-50">
                                <option value="" disabled>Pilih Rombel / Kelas</option>
                                <option v-for="k in kelasList" :key="k.id" :value="k.id">
                                    {{ k.nama_kelas }} ({{ k.siswas?.length || 0 }} Siswa)
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Mata Pelajaran</label>
                            <select v-model="selectedMapelId" class="w-full px-3 py-2 border rounded-xl text-sm bg-gray-50">
                                <option value="" disabled>Pilih Mata Pelajaran</option>
                                <option v-for="m in mapelList" :key="m.id" :value="m.id">
                                    {{ m.nama_mapel }} (KKM: {{ m.kkm }})
                                </option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Semester</label>
                            <select v-model.number="selectedSemester" class="w-full px-3 py-2 border rounded-xl text-sm bg-gray-50">
                                <option :value="1">Semester 1 (Ganjil)</option>
                                <option :value="2">Semester 2 (Genap)</option>
                                <option :value="3">Semester 3</option>
                                <option :value="4">Semester 4</option>
                                <option :value="5">Semester 5</option>
                                <option :value="6">Semester 6</option>
                            </select>
                        </div>
                    </div>

                    <!-- Quick Fill Bar -->
                    <div v-if="bulkForm.nilais.length > 0" class="pt-4 border-t flex flex-wrap items-center gap-4 bg-slate-50 -mx-6 -mb-6 p-4 rounded-b-3xl text-xs">
                        <span class="font-bold text-slate-700 uppercase tracking-wider">⚡ Isi Nilai Cepat Semua Siswa:</span>
                        <div class="flex items-center gap-1.5">
                            <span>Tugas:</span>
                            <input v-model="quickTugas" type="number" min="0" max="100" placeholder="85" class="w-16 px-2 py-1 border rounded text-xs text-center" />
                            <button type="button" @click="applyQuickTugas" class="px-2 py-1 bg-white border rounded hover:bg-gray-100 font-semibold">Terapkan</button>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span>UTS:</span>
                            <input v-model="quickUts" type="number" min="0" max="100" placeholder="80" class="w-16 px-2 py-1 border rounded text-xs text-center" />
                            <button type="button" @click="applyQuickUts" class="px-2 py-1 bg-white border rounded hover:bg-gray-100 font-semibold">Terapkan</button>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span>UAS:</span>
                            <input v-model="quickUas" type="number" min="0" max="100" placeholder="88" class="w-16 px-2 py-1 border rounded text-xs text-center" />
                            <button type="button" @click="applyQuickUas" class="px-2 py-1 bg-white border rounded hover:bg-gray-100 font-semibold">Terapkan</button>
                        </div>
                    </div>
                </div>

                <!-- Input Grid -->
                <div v-if="bulkForm.nilais.length > 0" class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-4 border-b flex items-center justify-between">
                        <div class="text-sm font-bold text-gray-800">
                            Daftar Siswa Kelas {{ currentKelas?.nama_kelas }} ({{ bulkForm.nilais.length }} Siswa)
                        </div>
                        <div class="text-xs text-gray-500">Bobot: 30% Tugas + 30% UTS + 40% UAS</div>
                    </div>

                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b text-gray-500 uppercase text-xs">
                            <tr>
                                <th class="py-3 px-4 w-12">No</th>
                                <th class="py-3 px-4">Nama Siswa</th>
                                <th class="py-3 px-4 w-32">Nilai Tugas (30%)</th>
                                <th class="py-3 px-4 w-32">Nilai UTS (30%)</th>
                                <th class="py-3 px-4 w-32">Nilai UAS (40%)</th>
                                <th class="py-3 px-4 w-28 text-center">Nilai Akhir</th>
                                <th class="py-3 px-4 w-28 text-center">Predikat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="(row, idx) in bulkForm.nilais" :key="row.siswa_id" class="hover:bg-gray-50/50">
                                <td class="py-2.5 px-4 text-gray-400 font-mono text-xs">{{ idx + 1 }}</td>
                                <td class="py-2.5 px-4">
                                    <div class="font-bold text-gray-900 text-sm">{{ row.siswa_nama }}</div>
                                    <div class="text-xs text-gray-400 font-mono">{{ row.siswa_nisn }}</div>
                                </td>
                                <td class="py-2.5 px-4">
                                    <input v-model.number="row.nilai_tugas" type="number" min="0" max="100" class="w-full px-2.5 py-1.5 border rounded-lg text-sm text-center font-bold" />
                                </td>
                                <td class="py-2.5 px-4">
                                    <input v-model.number="row.nilai_uts" type="number" min="0" max="100" class="w-full px-2.5 py-1.5 border rounded-lg text-sm text-center font-bold" />
                                </td>
                                <td class="py-2.5 px-4">
                                    <input v-model.number="row.nilai_uas" type="number" min="0" max="100" class="w-full px-2.5 py-1.5 border rounded-lg text-sm text-center font-bold" />
                                </td>
                                <td class="py-2.5 px-4 text-center font-black text-indigo-700 text-base">
                                    {{ calculateAkhir(row) }}
                                </td>
                                <td class="py-2.5 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold"
                                        :class="calculateAkhir(row) >= (currentMapel?.kkm || 65) ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'">
                                        {{ calculateAkhir(row) >= (currentMapel?.kkm || 65) ? 'Tuntas' : 'Remedial' }}
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="p-6 border-t flex justify-end gap-3 bg-gray-50">
                        <button
                            type="button"
                            @click="submitBulkNilai"
                            :disabled="bulkForm.processing"
                            class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white font-extrabold rounded-2xl shadow-lg shadow-indigo-600/20 text-sm transition flex items-center gap-2"
                        >
                            <span>💾</span>
                            <span>{{ bulkForm.processing ? 'Menyimpan Seluruh Nilai...' : 'Simpan Semua Nilai Rombel Ini' }}</span>
                        </button>
                    </div>
                </div>

                <div v-else-if="selectedKelasId" class="py-12 text-center text-gray-400 bg-white rounded-3xl border border-gray-100">
                    Tidak ada siswa terdaftar di rombel kelas ini. Tambahkan siswa terlebih dahulu pada menu Kelas.
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
