<script setup>
import { ref } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    siswa: Object,
    rata_rata: Number,
    kehadiran: Number,
    pembayaran_lancar: Boolean,
})

const activeTab = ref('profil')
const aiPrediction = ref(null)
const loadingPrediction = ref(false)
const generatingRaport = ref(false)

const runPrediction = async () => {
    loadingPrediction.value = true
    try {
        const res = await fetch(`/admin/siswa/${props.siswa.data?.id || props.siswa.id}/prediksi`)
        aiPrediction.value = await res.json()
    } catch (e) {
        aiPrediction.value = {
            prediksi: 'Error',
            probabilitas: 0,
            rekomendasi: ['Gagal menghubungi layanan microservice AI: ' + e.message],
        }
    } finally {
        loadingPrediction.value = false
    }
}

const generateRaport = async () => {
    generatingRaport.value = true
    try {
        const id = props.siswa.data?.id || props.siswa.id
        const res = await fetch(`/admin/siswa/${id}/raport`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({
                semester: 1,
                kelas_id: props.siswa.data?.kelas_aktif?.id || 1,
            }),
        })
        const data = await res.json()
        if (data.success && data.download_url) {
            window.open(data.download_url, '_blank')
        } else {
            alert('Raport berhasil diproses: ' + (data.message || 'Selesai'))
        }
    } catch (e) {
        alert('Gagal membuat raport: ' + e.message)
    } finally {
        generatingRaport.value = false
    }
}
</script>

<template>
    <Head :title="`Detail Siswa - ${siswa.data?.nama_lengkap || siswa.nama_lengkap}`" />

    <AdminLayout>
        <div class="p-6 max-w-7xl mx-auto space-y-6">
            <!-- Breadcrumb & Nav -->
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">{{ siswa.data?.nama_lengkap || siswa.nama_lengkap }}</h1>
                    <p class="text-gray-500 text-sm">NISN: {{ siswa.data?.nisn || siswa.nisn }} • {{ siswa.data?.angkatan?.program?.nama_program || 'Pendidikan Kesetaraan' }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <Link href="/admin/siswa" class="px-3.5 py-2 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-semibold transition">
                        &larr; Kembali
                    </Link>
                    <Link :href="`/admin/siswa/${siswa.data?.id || siswa.id}/edit`" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg text-sm font-semibold transition">
                        ✏️ Edit Data
                    </Link>
                </div>
            </div>

            <!-- Hero Summary Card -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold">
                        📊
                    </div>
                    <div>
                        <div class="text-xs uppercase font-bold text-gray-400">Rata-rata Nilai</div>
                        <div class="text-2xl font-black text-gray-800">{{ Number(rata_rata || 0).toFixed(1) }}</div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold">
                        ✅
                    </div>
                    <div>
                        <div class="text-xs uppercase font-bold text-gray-400">Kehadiran</div>
                        <div class="text-2xl font-black text-gray-800">{{ Number(kehadiran || 0).toFixed(0) }}%</div>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-bold">
                        💰
                    </div>
                    <div>
                        <div class="text-xs uppercase font-bold text-gray-400">Status SPP</div>
                        <div class="text-lg font-bold" :class="pembayaran_lancar ? 'text-emerald-600' : 'text-rose-600'">
                            {{ pembayaran_lancar ? 'Lancar / Lunas' : 'Ada Tunggakan' }}
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-5 rounded-2xl text-white shadow-md flex flex-col justify-between">
                    <div class="text-xs font-semibold uppercase tracking-wider text-indigo-100">AI Kelulusan Engine</div>
                    <div class="flex gap-2 mt-2">
                        <button @click="runPrediction" :disabled="loadingPrediction" class="flex-1 px-3 py-1.5 bg-white text-indigo-700 hover:bg-indigo-50 rounded-lg text-xs font-bold transition">
                            {{ loadingPrediction ? 'Menghitung...' : '🤖 Prediksi AI' }}
                        </button>
                        <button @click="generateRaport" :disabled="generatingRaport" class="px-3 py-1.5 bg-white/20 hover:bg-white/30 text-white rounded-lg text-xs font-bold transition">
                            {{ generatingRaport ? '...' : '📄 Raport' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- AI Prediction Banner if Loaded -->
            <div v-if="aiPrediction" class="p-6 rounded-2xl bg-gradient-to-br from-indigo-50 via-white to-purple-50 border border-indigo-100 shadow-sm space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🤖</span>
                        <h3 class="font-bold text-indigo-900">Hasil Prediksi Kelulusan Machine Learning</h3>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-black" :class="aiPrediction.prediksi === 'Lulus' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'">
                        {{ aiPrediction.prediksi }} ({{ ((aiPrediction.probabilitas || 0) * 100).toFixed(1) }}%)
                    </span>
                </div>
                <div class="text-xs text-gray-600">
                    <strong>Rekomendasi AI:</strong>
                    <ul class="list-disc pl-5 mt-1 space-y-1">
                        <li v-for="(rec, i) in (aiPrediction.rekomendasi || [])" :key="i">{{ rec }}</li>
                    </ul>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="flex border-b border-gray-100 px-6 pt-4 gap-6 text-sm font-semibold text-gray-500">
                    <button @click="activeTab = 'profil'" :class="['pb-4 border-b-2 transition', activeTab === 'profil' ? 'border-indigo-600 text-indigo-600' : 'border-transparent hover:text-gray-700']">
                        Biodata Siswa
                    </button>
                    <button @click="activeTab = 'nilai'" :class="['pb-4 border-b-2 transition', activeTab === 'nilai' ? 'border-indigo-600 text-indigo-600' : 'border-transparent hover:text-gray-700']">
                        Transkrip Nilai
                    </button>
                    <button @click="activeTab = 'pembayaran'" :class="['pb-4 border-b-2 transition', activeTab === 'pembayaran' ? 'border-indigo-600 text-indigo-600' : 'border-transparent hover:text-gray-700']">
                        Riwayat Pembayaran
                    </button>
                </div>

                <!-- Tab 1: Profil -->
                <div v-if="activeTab === 'profil'" class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                    <div class="space-y-4">
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase">Nama Lengkap</span>
                            <p class="font-semibold text-gray-800">{{ siswa.data?.nama_lengkap || siswa.nama_lengkap }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase">NISN / NIK</span>
                            <p class="font-mono text-gray-800">{{ siswa.data?.nisn || siswa.nisn }} / {{ siswa.data?.nik || '-' }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase">Tempat, Tanggal Lahir</span>
                            <p class="text-gray-800">{{ siswa.data?.tempat_lahir || '-' }}, {{ siswa.data?.tanggal_lahir || '-' }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase">Jenis Kelamin</span>
                            <p class="text-gray-800">{{ (siswa.data?.jenis_kelamin || siswa.jenis_kelamin) === 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase">Nama Orang Tua / Wali</span>
                            <p class="text-gray-800">{{ siswa.data?.nama_orang_tua || '-' }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase">Nomor Telepon</span>
                            <p class="text-gray-800">{{ siswa.data?.no_telepon || '-' }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase">Alamat Domisili</span>
                            <p class="text-gray-800">{{ siswa.data?.alamat || '-' }}</p>
                        </div>
                        <div>
                            <span class="text-xs font-bold text-gray-400 uppercase">Status Keaktifan</span>
                            <span class="inline-block mt-1 px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-full">
                                {{ siswa.data?.status || siswa.status }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Tab 2: Nilai -->
                <div v-if="activeTab === 'nilai'" class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                                <tr>
                                    <th class="py-3 px-4">Mata Pelajaran</th>
                                    <th class="py-3 px-4">Tugas</th>
                                    <th class="py-3 px-4">UTS</th>
                                    <th class="py-3 px-4">UAS</th>
                                    <th class="py-3 px-4">Nilai Akhir</th>
                                    <th class="py-3 px-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="n in (siswa.data?.nilais || [])" :key="n.id">
                                    <td class="py-3 px-4 font-semibold text-gray-800">{{ n.mata_pelajaran?.nama_mapel || 'Mapel' }}</td>
                                    <td class="py-3 px-4">{{ n.nilai_tugas ?? '-' }}</td>
                                    <td class="py-3 px-4">{{ n.nilai_uts ?? '-' }}</td>
                                    <td class="py-3 px-4">{{ n.nilai_uas ?? '-' }}</td>
                                    <td class="py-3 px-4 font-bold text-indigo-600">{{ n.nilai_akhir ?? '-' }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">
                                            {{ (n.nilai_akhir || 0) >= (n.mata_pelajaran?.kkm || 65) ? 'Tuntas' : 'Remedial' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr v-if="!(siswa.data?.nilais?.length)">
                                    <td colspan="6" class="py-8 text-center text-gray-400">Belum ada rekap nilai untuk siswa ini.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab 3: Pembayaran -->
                <div v-if="activeTab === 'pembayaran'" class="p-6">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-500 uppercase text-xs">
                                <tr>
                                    <th class="py-3 px-4">Periode</th>
                                    <th class="py-3 px-4">Jenis Tagihan</th>
                                    <th class="py-3 px-4">Nominal</th>
                                    <th class="py-3 px-4">Status</th>
                                    <th class="py-3 px-4">Tanggal Bayar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="p in (siswa.data?.pembayarans || [])" :key="p.id">
                                    <td class="py-3 px-4 font-medium">{{ p.bulan }} {{ p.tahun }}</td>
                                    <td class="py-3 px-4 uppercase text-xs font-bold text-gray-600">{{ p.jenis }}</td>
                                    <td class="py-3 px-4 font-mono">Rp {{ Number(p.nominal).toLocaleString('id-ID') }}</td>
                                    <td class="py-3 px-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold" :class="p.status === 'lunas' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800'">
                                            {{ p.status }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-500">{{ p.tanggal_bayar || '-' }}</td>
                                </tr>
                                <tr v-if="!(siswa.data?.pembayarans?.length)">
                                    <td colspan="5" class="py-8 text-center text-gray-400">Belum ada riwayat pembayaran tercatat.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
