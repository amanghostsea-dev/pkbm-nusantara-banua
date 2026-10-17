<script setup>
import { ref } from 'vue'
import { Head } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const jenis = ref('semester')
const tahun = ref(2024)
const program = ref('paket_c')
const format = ref('pdf')
const loading = ref(false)
const result = ref(null)

const generateLaporan = async () => {
    loading.value = true
    result.value = null
    try {
        const res = await fetch('/admin/laporan/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            },
            body: JSON.stringify({
                jenis: jenis.value,
                tahun: tahun.value,
                program: program.value,
                format: format.value,
            }),
        })
        const data = await res.json()
        result.value = data
        if (data.download_url) {
            window.open(data.download_url, '_blank')
        }
    } catch (e) {
        result.value = {
            success: false,
            message: 'Gagal membuat laporan: ' + e.message,
        }
    } finally {
        loading.value = false
    }
}
</script>

<template>
    <Head title="Pusat Laporan Dinas & Ekspor PKBM" />

    <AdminLayout>
        <div class="p-6 max-w-7xl mx-auto space-y-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">Pusat Laporan Dinas & Ekspor Data</h1>
                <p class="text-gray-500 text-sm">Cetak dan generate laporan resmi berkala untuk Dinas Pendidikan (Format Excel & PDF)</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Generator Card -->
                <div class="md:col-span-2 bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-5">
                    <h3 class="font-bold text-gray-900 text-lg border-b pb-3">Konfigurasi Laporan</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Jenis Laporan</label>
                            <select v-model="jenis" class="w-full px-3 py-2 border rounded-xl text-sm bg-gray-50/50">
                                <option value="bulanan">Laporan Bulanan Warga Belajar</option>
                                <option value="semester">Laporan Semester & Capaian Akademik</option>
                                <option value="tahunan">Laporan Tahunan Lembaga PKBM</option>
                                <option value="kelulusan">Laporan Kelulusan & Ijazah</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Tahun Ajaran</label>
                            <input v-model.number="tahun" type="number" class="w-full px-3 py-2 border rounded-xl text-sm bg-gray-50/50 font-mono" />
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Program Kesetaraan</label>
                            <select v-model="program" class="w-full px-3 py-2 border rounded-xl text-sm bg-gray-50/50">
                                <option value="">Semua Program (Paket A, B, C)</option>
                                <option value="paket_a">Paket A (Setara SD)</option>
                                <option value="paket_b">Paket B (Setara SMP)</option>
                                <option value="paket_c">Paket C (Setara SMA)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-gray-700 uppercase mb-1">Format Output</label>
                            <div class="grid grid-cols-2 gap-2 mt-1">
                                <button
                                    type="button"
                                    @click="format = 'pdf'"
                                    :class="['py-2 px-3 rounded-xl border text-sm font-bold flex items-center justify-center gap-2 transition', format === 'pdf' ? 'bg-rose-50 border-rose-300 text-rose-700 shadow-sm' : 'border-gray-200 text-gray-600 hover:bg-gray-50']"
                                >
                                    <span>📄</span>
                                    <span>PDF Resmi</span>
                                </button>
                                <button
                                    type="button"
                                    @click="format = 'excel'"
                                    :class="['py-2 px-3 rounded-xl border text-sm font-bold flex items-center justify-center gap-2 transition', format === 'excel' ? 'bg-emerald-50 border-emerald-300 text-emerald-700 shadow-sm' : 'border-gray-200 text-gray-600 hover:bg-gray-50']"
                                >
                                    <span>📊</span>
                                    <span>Excel (.xlsx)</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t flex items-center justify-between">
                        <div class="text-xs text-gray-500 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Didukung Python ReportLab & OpenPyXL Engine</span>
                        </div>
                        <button
                            @click="generateLaporan"
                            :disabled="loading"
                            class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 disabled:opacity-50 text-white rounded-xl font-bold text-sm shadow-md transition flex items-center gap-2"
                        >
                            <span>{{ loading ? '⏳' : '⚡' }}</span>
                            <span>{{ loading ? 'Membuat Laporan...' : 'Generate & Download' }}</span>
                        </button>
                    </div>

                    <div v-if="result" class="p-4 rounded-xl text-sm" :class="result.success ? 'bg-emerald-50 text-emerald-900 border border-emerald-200' : 'bg-rose-50 text-rose-900 border border-rose-200'">
                        <div class="font-bold">{{ result.success ? '✅ Laporan Berhasil Dibuat!' : '❌ Terjadi Kesalahan' }}</div>
                        <p class="text-xs mt-1">{{ result.message || 'File laporan dinas siap diunduh.' }}</p>
                        <a v-if="result.download_url" :href="result.download_url" target="_blank" class="inline-block mt-2 font-bold text-indigo-700 underline text-xs">
                            Klik di sini jika unduhan otomatis tidak terbuka &rarr;
                        </a>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="bg-gradient-to-br from-indigo-700 via-indigo-800 to-purple-900 rounded-3xl p-6 text-white shadow-xl flex flex-col justify-between">
                    <div>
                        <span class="px-3 py-1 bg-white/20 backdrop-blur rounded-full text-xs font-semibold uppercase tracking-wider">
                            Standar Kemendikbud
                        </span>
                        <h4 class="text-xl font-extrabold mt-3">Pedoman Pelaporan Dinas</h4>
                        <p class="text-indigo-100 text-sm mt-2 leading-relaxed">
                            Laporan memuat data agregat rombongan belajar, rekap absensi kehadiran bulanan, status kelulusan, dan nomor induk NISN yang tervalidasi sesuai standar Dapodik Kesetaraan.
                        </p>
                    </div>

                    <div class="p-4 bg-white/10 rounded-2xl border border-white/20 text-xs space-y-1">
                        <div class="font-bold text-white">Status Microservice Python:</div>
                        <div class="text-emerald-300">✓ FastAPI Backend Online</div>
                        <div class="text-indigo-200">✓ ReportLab Engine Siap</div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
