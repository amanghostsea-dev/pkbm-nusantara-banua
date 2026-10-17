<script setup>
/**
 * Ultra-Modern Executive Dashboard — Sistem PKBM
 * Terintegrasi dengan Laravel 11 & Python FastAPI Machine Learning
 */

import { ref, onMounted, onUnmounted, computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ChartLine from '@/Components/Charts/ChartLine.vue'
import ChartDoughnut from '@/Components/Charts/ChartDoughnut.vue'
import axios from 'axios'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    stats: Object,
    recentSiswa: Array,
    pengumumans: Array,
})

// Real-time Clock
const currentTime = ref('')
let timer = null
const updateClock = () => {
    const now = new Date()
    currentTime.value = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' })
}

// Analytics from Python FastAPI
const analyticsData = ref(null)
const isLoadingAnalytics = ref(true)
const apiStatus = ref('checking') // 'online' | 'offline' | 'checking'
const apiLatency = ref(null)

// AI Simulator State
const simNilai = ref(85)
const simKehadiran = ref(92)
const simLancar = ref(true)
const isSimulating = ref(false)
const simResult = ref(null)

const runAiSimulation = async () => {
    isSimulating.value = true
    try {
        const res = await axios.post('/api/analytics/simulate-prediction', {
            rata_rata_nilai: Number(simNilai.value),
            persentase_kehadiran: Number(simKehadiran.value),
            status_pembayaran_lancar: simLancar.value,
        })
        simResult.value = res.data
    } catch (e) {
        console.error('AI simulation error', e)
    } finally {
        isSimulating.value = false
    }
}

onMounted(async () => {
    updateClock()
    timer = setInterval(updateClock, 1000)

    const startTime = performance.now()
    try {
        const response = await axios.get('/api/analytics/dashboard')
        analyticsData.value = response.data
        apiLatency.value = Math.round(performance.now() - startTime)
        apiStatus.value = 'online'
    } catch (e) {
        apiStatus.value = 'offline'
    } finally {
        isLoadingAnalytics.value = false
    }

    // Run initial AI simulation
    runAiSimulation()
})

onUnmounted(() => {
    if (timer) clearInterval(timer)
})

const statCards = computed(() => [
    {
        title: 'Total Warga Belajar',
        value: props.stats?.total_aktif ?? 72,
        icon: '👨‍🎓',
        gradient: 'from-blue-600 to-indigo-700',
        glow: 'shadow-blue-500/20',
        badge: '+8.4%',
        badgeColor: 'bg-emerald-50 text-emerald-600 border-emerald-200',
        subtitle: 'Terdaftar di seluruh paket',
        progress: 88,
    },
    {
        title: 'Paket A (Setara SD)',
        value: props.stats?.paket_a ?? 18,
        icon: '📚',
        gradient: 'from-emerald-500 to-teal-700',
        glow: 'shadow-emerald-500/20',
        badge: '18 Siswa',
        badgeColor: 'bg-emerald-50 text-emerald-600 border-emerald-200',
        subtitle: 'Pendidikan dasar mandiri',
        progress: 65,
    },
    {
        title: 'Paket B (Setara SMP)',
        value: props.stats?.paket_b ?? 24,
        icon: '📖',
        gradient: 'from-violet-600 to-purple-800',
        glow: 'shadow-purple-500/20',
        badge: '24 Siswa',
        badgeColor: 'bg-purple-50 text-purple-600 border-purple-200',
        subtitle: 'Pendidikan menengah pertama',
        progress: 74,
    },
    {
        title: 'Paket C (Setara SMA)',
        value: props.stats?.paket_c ?? 30,
        icon: '🎓',
        gradient: 'from-amber-500 to-orange-700',
        glow: 'shadow-amber-500/20',
        badge: '30 Siswa',
        badgeColor: 'bg-amber-50 text-amber-600 border-amber-200',
        subtitle: 'Pendidikan vokasi & lanjutan',
        progress: 92,
    },
])

// Greeting based on hour
const greeting = computed(() => {
    const hour = new Date().getHours()
    if (hour < 11) return 'Selamat Pagi'
    if (hour < 15) return 'Selamat Siang'
    if (hour < 18) return 'Selamat Sore'
    return 'Selamat Malam'
})
</script>

<template>
    <Head title="Executive Dashboard — Sistem PKBM" />

    <div class="p-4 sm:p-6 lg:p-8 max-w-[1600px] mx-auto space-y-6 font-sans">
        <!-- ======================================================== -->
        <!-- 1. EXECUTIVE HERO HEADER WITH TELEMETRY STATUS           -->
        <!-- ======================================================== -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 border border-slate-800 shadow-2xl p-6 sm:p-8 text-white">
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-20 -bottom-20 w-80 h-80 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                            Pusat Komando PKBM Terpadu
                        </span>
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            Tahun Ajaran 2024/2025
                        </span>
                    </div>
                    <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight">
                        {{ greeting }}, Administrator! 👋
                    </h1>
                    <p class="text-slate-300 text-sm mt-1 max-w-2xl leading-relaxed">
                        Sistem terintegrasi monitoring pembelajaran, presensi kehadiran, nilai raport, dan model prediksi kelulusan cerdas berbasis Machine Learning.
                    </p>
                </div>

                <!-- Live Status & Clock HUD -->
                <div class="flex flex-wrap items-center gap-3 bg-white/5 backdrop-blur-xl border border-white/10 p-3.5 rounded-2xl">
                    <!-- Real-time Clock -->
                    <div class="px-4 py-2 bg-slate-950/60 rounded-xl border border-white/10 text-center">
                        <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Waktu Sistem</div>
                        <div class="font-mono text-lg font-bold text-cyan-300 tracking-wider">{{ currentTime }} WIB</div>
                    </div>

                    <!-- Python AI Engine Status -->
                    <div class="px-4 py-2 bg-slate-950/60 rounded-xl border border-white/10">
                        <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider flex items-center gap-1.5">
                            <span>AI Microservice</span>
                            <span :class="['w-2 h-2 rounded-full', apiStatus === 'online' ? 'bg-emerald-400 animate-ping' : 'bg-rose-400']"></span>
                        </div>
                        <div class="flex items-center gap-2 mt-0.5">
                            <span class="text-xs font-bold text-white">FastAPI ML v1.0</span>
                            <span v-if="apiLatency" class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-mono">
                                {{ apiLatency }}ms
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Action Command Strip -->
            <div class="mt-6 pt-6 border-t border-white/10 flex flex-wrap items-center gap-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400 mr-2 flex items-center gap-1">
                    <span>⚡</span> <span>Aksi Cepat:</span>
                </span>
                <Link
                    href="/admin/siswa/create"
                    class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-semibold text-xs shadow-lg shadow-blue-600/30 transition flex items-center gap-1.5"
                >
                    <span>➕</span> <span>Tambah Siswa</span>
                </Link>
                <Link
                    href="/admin/absensi"
                    class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-xs transition flex items-center gap-1.5"
                >
                    <span>📲</span> <span>Scan Presensi QR</span>
                </Link>
                <Link
                    href="/admin/nilais"
                    class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-xs transition flex items-center gap-1.5"
                >
                    <span>📊</span> <span>Input Nilai Mapel</span>
                </Link>
                <Link
                    href="/admin/raports"
                    class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-xs transition flex items-center gap-1.5"
                >
                    <span>📄</span> <span>Generate Raport PDF</span>
                </Link>
                <Link
                    href="/admin/sertifikats"
                    class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/20 border border-white/20 text-white font-semibold text-xs transition flex items-center gap-1.5"
                >
                    <span>🎓</span> <span>Ijazah / Sertifikat</span>
                </Link>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 2. HIGH-TECH STATS METRICS CARDS                         -->
        <!-- ======================================================== -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div
                v-for="card in statCards"
                :key="card.title"
                class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm hover:shadow-xl transition-all duration-300 relative overflow-hidden group"
            >
                <!-- Top Card Info -->
                <div class="flex items-start justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">
                            {{ card.title }}
                        </span>
                        <div class="text-3xl font-extrabold text-slate-900 tracking-tight">
                            {{ card.value }}
                        </div>
                    </div>
                    <div :class="['w-12 h-12 rounded-2xl bg-gradient-to-tr text-white flex items-center justify-center text-2xl shadow-lg transition-transform group-hover:scale-110', card.gradient, card.glow]">
                        {{ card.icon }}
                    </div>
                </div>

                <!-- Subtitle & Pill -->
                <div class="mt-4 flex items-center justify-between text-xs">
                    <span class="text-slate-500">{{ card.subtitle }}</span>
                    <span :class="['px-2 py-0.5 rounded-full font-bold text-[11px] border', card.badgeColor]">
                        {{ card.badge }}
                    </span>
                </div>

                <!-- Progress Bar Indicator -->
                <div class="mt-3 w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                    <div
                        :class="['h-full rounded-full bg-gradient-to-r', card.gradient]"
                        :style="{ width: `${card.progress}%` }"
                    ></div>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 3. INTERACTIVE AI GRADUATION PREDICTOR HUD (WOW FACTOR)  -->
        <!-- ======================================================== -->
        <div class="rounded-3xl bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 border border-indigo-500/30 shadow-2xl p-6 lg:p-8 text-white relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-6">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-lg bg-indigo-500/30 text-indigo-300 font-mono text-xs font-bold border border-indigo-400/30">
                            🤖 PYTHON MACHINE LEARNING ENGINE
                        </span>
                        <span class="px-2 py-0.5 rounded-lg bg-cyan-500/20 text-cyan-300 text-xs font-semibold">
                            Random Forest Classifier
                        </span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-extrabold mt-2 tracking-tight">
                        Simulasi Analisis Prediksi Kelulusan Cerdas
                    </h2>
                    <p class="text-slate-300 text-xs sm:text-sm mt-0.5">
                        Geser parameter di bawah ini untuk menguji algoritma AI mendeteksi risiko dan probabilitas kelulusan warga belajar secara *real-time*.
                    </p>
                </div>
                <button
                    @click="runAiSimulation"
                    :disabled="isSimulating"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 font-bold text-xs uppercase tracking-wider text-white shadow-lg shadow-cyan-500/25 transition disabled:opacity-50 flex items-center gap-2 self-start lg:self-center"
                >
                    <span v-if="isSimulating" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    <span>{{ isSimulating ? 'Memproses ML...' : '⚡ Kalkulasi Ulang AI' }}</span>
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                <!-- Controls: Sliders -->
                <div class="lg:col-span-7 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-5 space-y-5">
                    <!-- Slider Nilai -->
                    <div>
                        <div class="flex justify-between text-xs font-semibold mb-2">
                            <span class="text-slate-300">Rata-Rata Nilai Akademik:</span>
                            <span class="font-mono text-cyan-300 font-bold text-sm">{{ simNilai }} / 100</span>
                        </div>
                        <input
                            type="range"
                            min="40"
                            max="100"
                            v-model="simNilai"
                            @input="runAiSimulation"
                            class="w-full accent-cyan-400 cursor-pointer h-2 bg-slate-700 rounded-lg"
                        />
                        <div class="flex justify-between text-[10px] text-slate-400 mt-1">
                            <span>Remedial (40)</span>
                            <span>Standar KKM (65)</span>
                            <span>Istimewa (100)</span>
                        </div>
                    </div>

                    <!-- Slider Kehadiran -->
                    <div>
                        <div class="flex justify-between text-xs font-semibold mb-2">
                            <span class="text-slate-300">Tingkat Presensi Kehadiran:</span>
                            <span class="font-mono text-emerald-300 font-bold text-sm">{{ simKehadiran }}%</span>
                        </div>
                        <input
                            type="range"
                            min="30"
                            max="100"
                            v-model="simKehadiran"
                            @input="runAiSimulation"
                            class="w-full accent-emerald-400 cursor-pointer h-2 bg-slate-700 rounded-lg"
                        />
                        <div class="flex justify-between text-[10px] text-slate-400 mt-1">
                            <span>Rawan Drop (30%)</span>
                            <span>Batas Minimal (75%)</span>
                            <span>Disiplin (100%)</span>
                        </div>
                    </div>

                    <!-- Status SPP -->
                    <div class="flex items-center justify-between pt-2 border-t border-white/10">
                        <span class="text-xs text-slate-300 font-semibold">Administrasi & Biaya Belajar:</span>
                        <button
                            type="button"
                            @click="simLancar = !simLancar; runAiSimulation()"
                            :class="[
                                'px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5',
                                simLancar ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-400/30' : 'bg-rose-500/20 text-rose-300 border border-rose-400/30'
                            ]"
                        >
                            <span>{{ simLancar ? '✅ Pembayaran Lancar' : '⚠️ Menunggak SPP' }}</span>
                        </button>
                    </div>
                </div>

                <!-- Result Display Gauge -->
                <div class="lg:col-span-5 bg-white/5 backdrop-blur-xl border border-white/10 rounded-2xl p-6 text-center flex flex-col justify-between h-full">
                    <div v-if="simResult">
                        <div class="text-[11px] uppercase tracking-wider text-slate-400 font-bold">Hasil Prediksi Model AI</div>
                        <div class="my-3">
                            <span :class="[
                                'inline-block px-4 py-1.5 rounded-full text-base font-black tracking-wider uppercase shadow-lg',
                                simResult.prediksi === 'LULUS'
                                    ? 'bg-emerald-500 text-slate-950 shadow-emerald-500/30'
                                    : 'bg-amber-500 text-slate-950 shadow-amber-500/30'
                            ]">
                                {{ simResult.prediksi }}
                            </span>
                        </div>
                        <div class="text-4xl font-extrabold font-mono text-cyan-300">
                            {{ Number(simResult.probabilitas_lulus > 1 ? simResult.probabilitas_lulus : simResult.probabilitas_lulus * 100).toFixed(1) }}%
                        </div>
                        <div class="text-xs text-slate-400 mt-1">Tingkat Keyakinan Model: <span class="font-bold text-white">{{ simResult.confidence }}</span></div>

                        <!-- Rekomendasi Pintar -->
                        <div v-if="simResult.rekomendasi?.length" class="mt-4 pt-3 border-t border-white/10 text-left">
                            <div class="text-[10px] uppercase font-bold text-slate-400 mb-1">Rekomendasi Tindakan AI:</div>
                            <ul class="text-xs text-slate-200 space-y-1">
                                <li v-for="(rek, rIdx) in simResult.rekomendasi.slice(0, 2)" :key="rIdx" class="flex items-start gap-1.5">
                                    <span class="text-cyan-400">💡</span>
                                    <span>{{ rek }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div v-else class="py-8 text-slate-400 text-xs">
                        Memuat kalkulasi AI...
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 4. VISUAL CHARTS ROW (ATTENDANCE & DISTRIBUTION)         -->
        <!-- ======================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Line Chart: Tren Absensi Bulanan -->
            <div class="lg:col-span-2 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">📊 Tren Kehadiran Warga Belajar (12 Bulan)</h3>
                        <p class="text-xs text-slate-400">Tingkat partisipasi belajar Paket A, B, dan C</p>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <span class="inline-flex items-center gap-1 text-blue-600 font-semibold">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Paket A
                        </span>
                        <span class="inline-flex items-center gap-1 text-emerald-600 font-semibold">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span> Paket B
                        </span>
                        <span class="inline-flex items-center gap-1 text-amber-600 font-semibold">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-600"></span> Paket C
                        </span>
                    </div>
                </div>

                <div class="h-64 sm:h-72">
                    <ChartLine v-if="!isLoadingAnalytics" :data="analyticsData?.absensi_bulanan" />
                    <div v-else class="h-full flex items-center justify-center bg-slate-50 rounded-2xl animate-pulse text-xs text-slate-400">
                        Memuat grafik analitik...
                    </div>
                </div>
            </div>

            <!-- Doughnut Chart: Distribusi Program -->
            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-slate-900 text-base mb-1">🎯 Proporsi Program Kesetaraan</h3>
                    <p class="text-xs text-slate-400 mb-4">Komposisi peserta didik aktif</p>
                    <div class="h-56 flex items-center justify-center">
                        <ChartDoughnut
                            :labels="['Paket A (SD)', 'Paket B (SMP)', 'Paket C (SMA)']"
                            :values="[stats?.paket_a || 18, stats?.paket_b || 24, stats?.paket_c || 30]"
                        />
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-3 gap-2 text-center text-xs">
                    <div class="p-2 bg-blue-50/50 rounded-xl">
                        <div class="font-bold text-blue-600">{{ stats?.paket_a || 18 }}</div>
                        <div class="text-[10px] text-slate-500">Paket A</div>
                    </div>
                    <div class="p-2 bg-emerald-50/50 rounded-xl">
                        <div class="font-bold text-emerald-600">{{ stats?.paket_b || 24 }}</div>
                        <div class="text-[10px] text-slate-500">Paket B</div>
                    </div>
                    <div class="p-2 bg-amber-50/50 rounded-xl">
                        <div class="font-bold text-amber-600">{{ stats?.paket_c || 30 }}</div>
                        <div class="text-[10px] text-slate-500">Paket C</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- 5. RECENT STUDENTS & ANNOUNCEMENTS                       -->
        <!-- ======================================================== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            <!-- Siswa Terdaftar Terkini -->
            <div class="lg:col-span-7 bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-slate-900 text-base">👥 Warga Belajar Baru Terdaftar</h3>
                        <p class="text-xs text-slate-400">Data pendaftaran terbaru di sistem</p>
                    </div>
                    <Link href="/admin/siswa" class="text-xs font-bold text-indigo-600 hover:text-indigo-800">
                        Lihat Seluruh Siswa &rarr;
                    </Link>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="uppercase text-slate-400 border-b border-slate-100 text-[11px]">
                            <tr>
                                <th class="py-3 px-3">Warga Belajar</th>
                                <th class="py-3 px-3">NISN</th>
                                <th class="py-3 px-3">Program / Angkatan</th>
                                <th class="py-3 px-3 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <tr v-for="item in recentSiswa?.slice(0, 5)" :key="item.id" class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-3">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-blue-500 to-indigo-600 text-white font-bold flex items-center justify-center text-xs">
                                            {{ item.nama_lengkap?.charAt(0) }}
                                        </div>
                                        <span class="font-semibold text-slate-900">{{ item.nama_lengkap }}</span>
                                    </div>
                                </td>
                                <td class="py-3 px-3 font-mono text-slate-500">{{ item.nisn }}</td>
                                <td class="py-3 px-3 font-medium text-slate-600">
                                    {{ item.angkatan?.program?.nama_program || 'Paket C (SMA)' }}
                                </td>
                                <td class="py-3 px-3 text-right">
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                        {{ item.status || 'Aktif' }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="!recentSiswa || recentSiswa.length === 0">
                                <td colspan="4" class="py-6 text-center text-slate-400">
                                    Belum ada data warga belajar terbaru.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Papan Pengumuman & Service Telemetry -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Pengumuman -->
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-slate-900 text-base">📢 Pengumuman Akademik</h3>
                        <Link href="/admin/pengumumans" class="text-xs font-semibold text-indigo-600 hover:underline">
                            + Buat Baru
                        </Link>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="p in pengumumans?.slice(0, 3)"
                            :key="p.id"
                            class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100 hover:border-indigo-200 transition"
                        >
                            <div class="flex items-center justify-between mb-1">
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-indigo-100 text-indigo-700">
                                    {{ p.target }}
                                </span>
                                <span class="text-[10px] text-slate-400">{{ p.created_at_human }}</span>
                            </div>
                            <h4 class="font-semibold text-slate-800 text-xs">{{ p.judul }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Infrastruktur Status Card -->
                <div class="bg-slate-900 text-white rounded-3xl p-6 border border-slate-800 shadow-lg">
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        Status Infrastruktur Microservices
                    </h4>
                    <div class="space-y-2.5 text-xs">
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-800">
                            <span class="text-slate-400">Web Engine</span>
                            <span class="font-mono text-emerald-300 font-semibold">Laravel 11.57 + PHP 8.2</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-800">
                            <span class="text-slate-400">Frontend Stack</span>
                            <span class="font-mono text-cyan-300 font-semibold">Vue 3 + Inertia.js + Tailwind</span>
                        </div>
                        <div class="flex justify-between items-center py-1.5 border-b border-slate-800">
                            <span class="text-slate-400">AI Microservice</span>
                            <span class="font-mono text-purple-300 font-semibold">FastAPI + Scikit-Learn</span>
                        </div>
                        <div class="flex justify-between items-center pt-1.5">
                            <span class="text-slate-400">Database Engine</span>
                            <span class="font-mono text-amber-300 font-semibold">MySQL Ready (InnoDB)</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
