<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

const props = defineProps({
    programs: Array,
    tahunList: Array,
    stats: Object,
    previewRows: Array,
    filters: Object,
    pythonOnline: Boolean,
})

const jenis = ref(props.filters?.jenis || 'semester')
const tahun = ref(props.filters?.tahun || new Date().getFullYear())
const program = ref(props.filters?.program || '')
const format = ref('pdf')
const loading = ref(false)
const notification = ref(null)

// Jenis Laporan Descriptions
const jenisInfo = {
    bulanan: {
        title: 'Laporan Bulanan Warga Belajar',
        desc: 'Memuat rekapitulasi kehadiran harian, absensi, rasio keaktifan belajar per rombel sesuai format dinas.',
        icon: '📋'
    },
    semester: {
        title: 'Laporan Semester & Capaian Akademik',
        desc: 'Daftar nilai rata-rata mata pelajaran, ketuntasan kurikulum, dan evaluasi hasil belajar semester warga belajar.',
        icon: '📊'
    },
    tahunan: {
        title: 'Laporan Tahunan Perkembangan Lembaga',
        desc: 'Laporan komprehensif data profil warga belajar, guru pembimbing, rombel, dan evaluasi capaian lembaga tahunan.',
        icon: '🏛️'
    },
    kelulusan: {
        title: 'Laporan Kelulusan & Ijazah',
        desc: 'Daftar nama calon peserta ujian kesetaraan, penerbitan ijazah/sertifikat, dan status kelulusan akhir.',
        icon: '🎓'
    },
    keuangan: {
        title: 'Laporan Administrasi SPP & Keuangan',
        desc: 'Rekapitulasi status pembayaran SPP siswa, tunggakan, serta rincian administrasi operasional pembelajaran.',
        icon: '💰'
    }
}

// Buka cetak PDF resmi langsung
function openCetakPdf() {
    const url = `/admin/laporan/cetak?jenis=${jenis.value}&tahun=${tahun.value}&program=${program.value}`
    window.open(url, '_blank')
}

// Download Excel (.csv UTF-8 compatible)
function downloadExcel() {
    const url = `/admin/laporan/export?jenis=${jenis.value}&tahun=${tahun.value}&program=${program.value}&format=excel`
    window.location.href = url
}

// Handler tombol Generate Utama
async function handleGenerate() {
    loading.value = true
    notification.value = null

    try {
        if (format.value === 'pdf') {
            openCetakPdf()
            notification.value = {
                type: 'success',
                message: 'Dokumen laporan PDF resmi dinas dibuka di tab baru untuk dicetak atau disimpan.',
            }
        } else {
            downloadExcel()
            notification.value = {
                type: 'success',
                message: 'File laporan Excel (.csv) sedang diunduh ke perangkat Anda.',
            }
        }
    } catch (e) {
        notification.value = {
            type: 'error',
            message: 'Gagal memproses laporan: ' + e.message,
        }
    } finally {
        setTimeout(() => {
            loading.value = false
        }, 800)
    }
}

// Filter ulang data preview di halaman
function applyFilter() {
    router.get('/admin/laporan', {
        jenis: jenis.value,
        tahun: tahun.value,
        program: program.value,
    }, {
        preserveState: true,
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Pusat Laporan Dinas & Ekspor PKBM" />

    <AdminLayout>
        <template #breadcrumb>
            <div class="flex items-center gap-2 text-xs sm:text-sm">
                <span class="text-slate-400">Administrasi</span>
                <span class="text-slate-300">/</span>
                <span class="font-bold text-slate-800">Pusat Laporan & Ekspor Data</span>
            </div>
        </template>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 sm:py-7 space-y-6 sm:space-y-8">
            
            <!-- Header Section -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 sm:p-7 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2.5">
                        <span class="text-2xl sm:text-3xl">📈</span>
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">Pusat Laporan Dinas & Ekspor Data</h1>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl">
                        Cetak dan unduh laporan resmi berkala berstandar Dinas Pendidikan (Format Excel Spreadsheet & PDF Resmi dengan Kop Surat).
                    </p>
                </div>
                
                <!-- Engine Status Badge -->
                <div class="flex items-center gap-2 self-start md:self-center">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>Engine Generator Siap</span>
                    </span>
                </div>
            </div>

            <!-- Stats Overview Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 sm:gap-4">
                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Warga Belajar</div>
                    <div class="text-lg sm:text-xl font-black text-slate-900 mt-0.5">{{ stats?.total_siswa || 0 }}</div>
                    <div class="text-[10px] text-slate-500 mt-0.5">Siswa terdaftar</div>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-emerald-600">Siswa Aktif</div>
                    <div class="text-lg sm:text-xl font-black text-emerald-700 mt-0.5">{{ stats?.siswa_aktif || 0 }}</div>
                    <div class="text-[10px] text-emerald-600/80 mt-0.5">Aktif belajar</div>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Tutor Pembimbing</div>
                    <div class="text-lg sm:text-xl font-black text-slate-900 mt-0.5">{{ stats?.total_tutor || 0 }}</div>
                    <div class="text-[10px] text-slate-500 mt-0.5">Guru / Pendidik</div>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rombongan Belajar</div>
                    <div class="text-lg sm:text-xl font-black text-slate-900 mt-0.5">{{ stats?.total_kelas || 0 }}</div>
                    <div class="text-[10px] text-slate-500 mt-0.5">Kelas binaan</div>
                </div>

                <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm col-span-2 sm:col-span-1">
                    <div class="text-[10px] font-bold uppercase tracking-wider text-indigo-600">Sertifikat Terbit</div>
                    <div class="text-lg sm:text-xl font-black text-indigo-700 mt-0.5">{{ stats?.total_sertifikat || 0 }}</div>
                    <div class="text-[10px] text-indigo-600/80 mt-0.5">Ijazah / kelulusan</div>
                </div>
            </div>

            <!-- Notifications -->
            <transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0 -translate-y-2" enter-to-class="opacity-100 translate-y-0">
                <div
                    v-if="notification"
                    :class="[
                        'p-4 rounded-2xl text-xs sm:text-sm font-semibold flex items-center justify-between shadow-sm border',
                        notification.type === 'success' ? 'bg-emerald-50 text-emerald-900 border-emerald-200' : 'bg-rose-50 text-rose-900 border-rose-200'
                    ]"
                >
                    <div class="flex items-center gap-2">
                        <span>{{ notification.type === 'success' ? '✅' : '❌' }}</span>
                        <span>{{ notification.message }}</span>
                    </div>
                    <button @click="notification = null" class="text-slate-400 hover:text-slate-700 text-xs">✕</button>
                </div>
            </transition>

            <!-- Configuration & Action Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 sm:gap-8 items-start">
                
                <!-- Main Generator Card (2 Cols) -->
                <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 sm:p-7 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h2 class="font-bold text-slate-900 text-base sm:text-lg">Konfigurasi Laporan</h2>
                            <p class="text-xs text-slate-500">Tentukan periode, program kesetaraan, dan format keluaran berkas.</p>
                        </div>
                        <span class="text-xl">⚙️</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">
                        <!-- Jenis Laporan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Jenis Laporan
                            </label>
                            <select
                                v-model="jenis"
                                @change="applyFilter"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-900 bg-slate-50/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition"
                            >
                                <option value="semester">📊 Laporan Semester & Capaian Akademik</option>
                                <option value="bulanan">📋 Laporan Bulanan Kehadiran Warga Belajar</option>
                                <option value="tahunan">🏛️ Laporan Tahunan Lembaga PKBM</option>
                                <option value="kelulusan">🎓 Laporan Kelulusan & Ijazah</option>
                                <option value="keuangan">💰 Laporan Pembayaran & SPP</option>
                            </select>
                        </div>

                        <!-- Tahun Ajaran / Periode -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Tahun Ajaran / Periode
                            </label>
                            <select
                                v-model="tahun"
                                @change="applyFilter"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-900 bg-slate-50/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition"
                            >
                                <option v-for="t in tahunList" :key="t" :value="t">{{ t }}</option>
                                <option :value="2025">Tahun 2025</option>
                                <option :value="2024">Tahun 2024</option>
                                <option :value="2023">Tahun 2023</option>
                            </select>
                        </div>

                        <!-- Program Kesetaraan -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Program Kesetaraan
                            </label>
                            <select
                                v-model="program"
                                @change="applyFilter"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-900 bg-slate-50/50 focus:outline-none focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition"
                            >
                                <option value="">Semua Program (Paket A, B, C)</option>
                                <option v-for="p in programs" :key="p.id" :value="p.jenjang">
                                    {{ p.nama_program }}
                                </option>
                            </select>
                        </div>

                        <!-- Format Output -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                                Pilihan Format Dokumen
                            </label>
                            <div class="grid grid-cols-2 gap-2">
                                <button
                                    type="button"
                                    @click="format = 'pdf'"
                                    :class="[
                                        'py-2 px-3 rounded-xl border text-xs sm:text-sm font-bold flex items-center justify-center gap-1.5 transition active:scale-95',
                                        format === 'pdf'
                                            ? 'bg-rose-50 border-rose-300 text-rose-700 shadow-sm'
                                            : 'border-slate-200 text-slate-600 hover:bg-slate-50'
                                    ]"
                                >
                                    <span>📄</span>
                                    <span>PDF Resmi</span>
                                </button>
                                <button
                                    type="button"
                                    @click="format = 'excel'"
                                    :class="[
                                        'py-2 px-3 rounded-xl border text-xs sm:text-sm font-bold flex items-center justify-center gap-1.5 transition active:scale-95',
                                        format === 'excel'
                                            ? 'bg-emerald-50 border-emerald-300 text-emerald-700 shadow-sm'
                                            : 'border-slate-200 text-slate-600 hover:bg-slate-50'
                                    ]"
                                >
                                    <span>📊</span>
                                    <span>Excel (.csv)</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Dynamic Description Box -->
                    <div class="bg-indigo-50/60 rounded-2xl p-4 border border-indigo-100 flex items-start gap-3">
                        <span class="text-xl">{{ jenisInfo[jenis]?.icon || '📄' }}</span>
                        <div class="text-xs">
                            <span class="font-bold text-indigo-950">{{ jenisInfo[jenis]?.title }}: </span>
                            <span class="text-indigo-900">{{ jenisInfo[jenis]?.desc }}</span>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="text-xs text-slate-500 flex items-center gap-2 order-2 sm:order-1">
                            <span>💡 Data bersumber langsung dari database aktif PKBM</span>
                        </div>

                        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto order-1 sm:order-2">
                            <!-- Direct Print PDF button -->
                            <button
                                type="button"
                                @click="openCetakPdf"
                                class="flex-1 sm:flex-initial px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold text-xs sm:text-sm border border-slate-200 transition flex items-center justify-center gap-1.5 active:scale-95"
                                title="Buka format cetak resmi ber-KOP"
                            >
                                <span>🖨️</span>
                                <span>Cetak / PDF</span>
                            </button>

                            <!-- Direct Excel button -->
                            <button
                                type="button"
                                @click="downloadExcel"
                                class="flex-1 sm:flex-initial px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs sm:text-sm shadow-md shadow-emerald-600/20 transition flex items-center justify-center gap-1.5 active:scale-95"
                                title="Unduh spreadsheet Excel"
                            >
                                <span>📊</span>
                                <span>Unduh Excel</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Info Guideline Card (1 Col) -->
                <div class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-7 text-white shadow-xl flex flex-col justify-between space-y-6">
                    <div>
                        <span class="px-3 py-1 bg-white/10 backdrop-blur rounded-full text-[11px] font-bold uppercase tracking-wider text-amber-300 border border-amber-300/30">
                            Pedoman Dinas Pendidikan
                        </span>
                        <h3 class="text-lg sm:text-xl font-black mt-3 text-white">Standar Pelaporan Dapodik Kesetaraan</h3>
                        <p class="text-slate-300 text-xs sm:text-sm mt-2 leading-relaxed">
                            Laporan memuat data agregat rombongan belajar, rekap absensi kehadiran bulanan, status kelulusan, dan nomor induk NISN yang tervalidasi sesuai standar resmi Dinas Pendidikan dan Kebudayaan.
                        </p>
                    </div>

                    <div class="p-4 bg-white/10 rounded-2xl border border-white/10 space-y-2 text-xs">
                        <div class="font-bold text-slate-200 flex items-center gap-2">
                            <span>🏛️</span>
                            <span>Kop Surat & Tanda Tangan:</span>
                        </div>
                        <p class="text-slate-300 text-[11px]">
                            Format PDF telah dilengkapi dengan Kop Surat instansi, identitas NPSN resmi, serta kolom pengesahan Kepala PKBM.
                        </p>
                    </div>
                </div>

            </div>

            <!-- Live Data Preview Table -->
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5 sm:p-7 space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base sm:text-lg">Pratinjau Data Terkini</h3>
                        <p class="text-xs text-slate-500">Menampilkan sampel data warga belajar yang akan dimuat ke dalam berkas laporan.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full bg-indigo-50 text-indigo-700 text-xs font-bold border border-indigo-200/60 self-start sm:self-auto">
                        {{ previewRows?.length || 0 }} Data Terpilih
                    </span>
                </div>

                <div class="overflow-x-auto -mx-5 sm:mx-0">
                    <table class="w-full text-left text-xs sm:text-sm min-w-[700px]">
                        <thead>
                            <tr class="bg-slate-50 text-slate-500 text-[11px] font-bold uppercase tracking-wider border-y border-slate-200/60">
                                <th class="py-3 px-4">No</th>
                                <th class="py-3 px-4">NISN</th>
                                <th class="py-3 px-4">Nama Lengkap</th>
                                <th class="py-3 px-4">L/P</th>
                                <th class="py-3 px-4">Program</th>
                                <th class="py-3 px-4 text-center">Kehadiran</th>
                                <th class="py-3 px-4 text-center">Rata-rata Nilai</th>
                                <th class="py-3 px-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="(row, idx) in previewRows" :key="row.id || idx" class="hover:bg-slate-50/60 transition">
                                <td class="py-3.5 px-4 font-bold text-slate-400">{{ idx + 1 }}</td>
                                <td class="py-3.5 px-4 font-mono font-bold text-slate-700">{{ row.nisn || '—' }}</td>
                                <td class="py-3.5 px-4 font-bold text-slate-900">{{ row.nama_lengkap }}</td>
                                <td class="py-3.5 px-4 text-slate-600">{{ row.jenis_kelamin || '—' }}</td>
                                <td class="py-3.5 px-4 font-medium text-slate-700">{{ row.program }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        {{ row.kehadiran }}%
                                    </span>
                                </td>
                                <td class="py-3.5 px-4 text-center font-bold text-indigo-700">
                                    {{ row.nilai_rata || '0' }}
                                </td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="inline-block px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">
                                        {{ row.status }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="!previewRows || previewRows.length === 0">
                                <td colspan="8" class="py-10 text-center text-slate-400">
                                    <div class="text-2xl mb-1">🔍</div>
                                    <p class="font-medium text-xs">Belum ada data warga belajar yang sesuai dengan kriteria yang dipilih.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AdminLayout>
</template>
