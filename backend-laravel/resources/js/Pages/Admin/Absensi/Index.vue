<script setup>
/**
 * Fitur Presensi Canggih — Sistem PKBM
 * Mendukung Presensi Biometrik Kamera Wajah AI (Face Recognition),
 * Live Camera QR Scanner HUD, Proyektor QR Dinamis (Python FastAPI),
 * dan USB Barcode Gun.
 */

import { ref, onMounted, onUnmounted, computed, watch } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import axios from 'axios'
import jsQR from 'jsqr'

defineOptions({ layout: AdminLayout })

const props = defineProps({
    absensis: Array,
    jadwals: Array,
    siswas: Array,
    stats: Object,
    today: String,
})

// Tab Active: 'face' | 'scanner' | 'projector' | 'records'
const activeTab = ref('face')

// Selected Jadwal for Attendance
const selectedJadwalId = ref(props.jadwals?.[0]?.id || null)
const selectedJadwal = computed(() => {
    return props.jadwals?.find(j => j.id === Number(selectedJadwalId.value)) || props.jadwals?.[0]
})

// ==========================================
// ==========================================
// SHARED CAMERA STREAM & DEVICE SELECTION
// ==========================================
const videoFaceRef = ref(null)
const videoQrRef = ref(null)
const canvasRef = ref(null)
const fileInputRef = ref(null)
const cameraActive = ref(false)
const cameraLoading = ref(false)
const cameraError = ref(null)
const availableCameras = ref([])
const selectedCameraId = ref('')
let stream = null
let animationFrameId = null

// Synthesize pleasant high-tech sound
const playBeep = (isSuccess = true) => {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)()
        const osc = audioCtx.createOscillator()
        const gain = audioCtx.createGain()

        osc.type = isSuccess ? 'sine' : 'sawtooth'
        osc.frequency.setValueAtTime(isSuccess ? 880 : 330, audioCtx.currentTime)
        if (isSuccess) {
            osc.frequency.exponentialRampToValueAtTime(1760, audioCtx.currentTime + 0.15)
        }

        gain.gain.setValueAtTime(0.15, audioCtx.currentTime)
        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.2)

        osc.connect(gain)
        gain.connect(audioCtx.destination)

        osc.start()
        osc.stop(audioCtx.currentTime + 0.2)
    } catch (e) {
        // Audio not allowed yet
    }
}

// Enumerate connected cameras
const getAvailableCameras = async () => {
    try {
        if (!navigator.mediaDevices?.enumerateDevices) return
        const devices = await navigator.mediaDevices.enumerateDevices()
        const videoInputs = devices.filter(d => d.kind === 'videoinput')
        availableCameras.value = videoInputs.map((d, i) => ({
            id: d.deviceId,
            label: d.label || `Kamera ${i + 1}`
        }))
        if (!selectedCameraId.value && availableCameras.value.length > 0) {
            selectedCameraId.value = availableCameras.value[0].id
        }
    } catch (e) {
        console.warn('Gagal membaca daftar perangkat kamera', e)
    }
}

// Attach active stream to visible video element
const attachStreamToVideos = async () => {
    if (!stream) return

    const setupVideo = async (videoEl) => {
        if (!videoEl) return
        if (videoEl.srcObject !== stream) {
            videoEl.srcObject = stream
        }
        videoEl.setAttribute('playsinline', 'true')
        videoEl.setAttribute('autoplay', 'true')
        videoEl.muted = true
        try {
            await videoEl.play()
        } catch (err) {
            videoEl.onloadedmetadata = () => {
                videoEl.play().catch(e => console.warn('Video play error:', e))
            }
        }
    }

    if (activeTab.value === 'face' && videoFaceRef.value) {
        await setupVideo(videoFaceRef.value)
    } else if (activeTab.value === 'scanner' && videoQrRef.value) {
        await setupVideo(videoQrRef.value)
    }
}

// Start Camera Stream with intelligent fallback
const startCamera = async (forceDeviceId = null) => {
    cameraError.value = null
    cameraLoading.value = true

    if (stream) {
        stream.getTracks().forEach(t => t.stop())
        stream = null
    }

    const deviceIdToUse = forceDeviceId || selectedCameraId.value

    try {
        let constraints = {}
        if (deviceIdToUse) {
            constraints = {
                video: {
                    deviceId: { exact: deviceIdToUse },
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
                },
                audio: false
            }
        } else {
            constraints = {
                video: {
                    facingMode: 'user',
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
                },
                audio: false
            }
        }

        try {
            stream = await navigator.mediaDevices.getUserMedia(constraints)
        } catch (initialErr) {
            console.warn('Constraint kamera ideal ditolak, mencoba constraint standar...', initialErr)
            // Fallback: try basic video without strict resolution/facingMode
            stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false })
        }

        cameraActive.value = true
        await attachStreamToVideos()
        await getAvailableCameras()

        if (activeTab.value === 'scanner') {
            requestAnimationFrame(tickQrScan)
        }
    } catch (err) {
        console.error('Camera access error:', err)
        cameraActive.value = false
        if (err.name === 'NotAllowedError' || err.name === 'PermissionDeniedError') {
            cameraError.value = 'Izin kamera ditolak browser. Klik ikon gembok / kamera di bilah alamat browser untuk mengizinkan akses kamera.'
        } else if (err.name === 'NotFoundError' || err.name === 'DevicesNotFoundError') {
            cameraError.value = 'Kamera tidak ditemukan pada perangkat Anda. Pastikan webcam terpasang.'
        } else if (err.name === 'NotReadableError' || err.name === 'TrackStartError') {
            cameraError.value = 'Kamera sedang dipakai aplikasi lain (seperti Zoom, Google Meet, Teams, atau WhatsApp). Tutup aplikasi tersebut dan coba lagi.'
        } else {
            cameraError.value = `Gagal mengaktifkan kamera: ${err.message || 'Periksa izin kamera pada browser Anda.'}`
        }
    } finally {
        cameraLoading.value = false
    }
}

// Stop Camera
const stopCamera = () => {
    if (stream) {
        stream.getTracks().forEach(t => t.stop())
        stream = null
    }
    if (videoFaceRef.value) videoFaceRef.value.srcObject = null
    if (videoQrRef.value) videoQrRef.value.srcObject = null
    if (animationFrameId) {
        cancelAnimationFrame(animationFrameId)
        animationFrameId = null
    }
    cameraActive.value = false
}

const onCameraChange = () => {
    startCamera(selectedCameraId.value)
}

// ==========================================
// 1. BIOMETRIC FACE ATTENDANCE (AI FACE HUD)
// ==========================================
const selectedSiswaIdForFace = ref(props.siswas?.[0]?.id || '')
const isVerifyingFace = ref(false)
const faceSnapshot = ref(null)
const faceResult = ref(null)
const faceNotification = ref(null)
const countdownTimerFace = ref(null)
const faceCountdown = ref(0)

const captureAndVerifyFace = async () => {
    const video = videoFaceRef.value
    const canvas = canvasRef.value
    if (!video || !canvas) return
    if (!selectedJadwalId.value) {
        alert('Silakan pilih jadwal kelas terlebih dahulu!')
        return
    }

    const width = video.videoWidth || 640
    const height = video.videoHeight || 480
    canvas.width = width
    canvas.height = height

    const ctx = canvas.getContext('2d')
    ctx.drawImage(video, 0, 0, width, height)

    const base64Image = canvas.toDataURL('image/jpeg', 0.85)
    faceSnapshot.value = base64Image

    isVerifyingFace.value = true
    faceNotification.value = null

    try {
        const response = await axios.post('/admin/absensi/face-scan', {
            jadwal_id: selectedJadwalId.value,
            siswa_id: selectedSiswaIdForFace.value || null,
            image_base64: base64Image,
        })

        if (response.data.success) {
            playBeep(true)
            faceResult.value = response.data
            faceNotification.value = {
                type: 'success',
                text: response.data.message,
            }
        }
    } catch (err) {
        playBeep(false)
        faceNotification.value = {
            type: 'error',
            text: err.response?.data?.message || 'Wajah tidak terverifikasi atau pencahayaan kurang jelas!',
        }
    } finally {
        isVerifyingFace.value = false
    }
}

// Fallback: Upload Foto dari Galeri / File
const handleFileUploadForFace = (event) => {
    const file = event.target.files?.[0]
    if (!file) return

    const reader = new FileReader()
    reader.onload = async (e) => {
        const base64Image = e.target.result
        faceSnapshot.value = base64Image
        isVerifyingFace.value = true
        faceNotification.value = null

        try {
            const response = await axios.post('/admin/absensi/face-scan', {
                jadwal_id: selectedJadwalId.value,
                siswa_id: selectedSiswaIdForFace.value || null,
                image_base64: base64Image,
            })

            if (response.data.success) {
                playBeep(true)
                faceResult.value = response.data
                faceNotification.value = {
                    type: 'success',
                    text: response.data.message,
                }
            }
        } catch (err) {
            playBeep(false)
            faceNotification.value = {
                type: 'error',
                text: err.response?.data?.message || 'Wajah pada foto tidak terverifikasi atau format tidak valid!',
            }
        } finally {
            isVerifyingFace.value = false
        }
    }
    reader.readAsDataURL(file)
}

// Auto countdown snap (3.. 2.. 1.. Snap)
const startFaceCountdown = () => {
    if (faceCountdown.value > 0 || isVerifyingFace.value) return
    faceCountdown.value = 3
    countdownTimerFace.value = setInterval(() => {
        faceCountdown.value--
        if (faceCountdown.value <= 0) {
            clearInterval(countdownTimerFace.value)
            captureAndVerifyFace()
        }
    }, 1000)
}

// ==========================================
// 2. LIVE QR CODE SCANNER (jsQR)
// ==========================================
const manualNisn = ref('')
const isSubmittingScan = ref(false)
const lastScannedStudent = ref(null)
const scanNotification = ref(null)

const tickQrScan = () => {
    const video = videoQrRef.value
    if (!video || video.readyState !== video.HAVE_ENOUGH_DATA) {
        if (cameraActive.value && activeTab.value === 'scanner') {
            animationFrameId = requestAnimationFrame(tickQrScan)
        }
        return
    }

    const canvas = canvasRef.value
    if (!canvas) return
    const ctx = canvas.getContext('2d', { willReadFrequently: true })

    canvas.height = video.videoHeight
    canvas.width = video.videoWidth
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height)

    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height)
    const code = jsQR(imageData.data, imageData.width, imageData.height, {
        inversionAttempts: 'dontInvert',
    })

    if (code && code.data && !isSubmittingScan.value) {
        handleScannedCode(code.data)
        setTimeout(() => {
            if (cameraActive.value && activeTab.value === 'scanner') {
                animationFrameId = requestAnimationFrame(tickQrScan)
            }
        }, 2500)
        return
    }

    if (cameraActive.value && activeTab.value === 'scanner') {
        animationFrameId = requestAnimationFrame(tickQrScan)
    }
}

const handleScannedCode = async (rawCode) => {
    if (!selectedJadwalId.value) {
        alert('Silakan pilih jadwal kelas terlebih dahulu!')
        return
    }

    isSubmittingScan.value = true
    try {
        const response = await axios.post('/admin/absensi/scan', {
            jadwal_id: selectedJadwalId.value,
            identifier: rawCode,
        })

        if (response.data.success) {
            playBeep(true)
            lastScannedStudent.value = response.data.siswa
            scanNotification.value = {
                type: 'success',
                text: response.data.message,
            }
            manualNisn.value = ''
        }
    } catch (err) {
        playBeep(false)
        scanNotification.value = {
            type: 'error',
            text: err.response?.data?.message || 'Kode QR atau NISN tidak dikenali!',
        }
    } finally {
        isSubmittingScan.value = false
    }
}

const submitManual = () => {
    if (!manualNisn.value) return
    handleScannedCode(manualNisn.value)
}

// ==========================================
// 3. CLASSROOM DYNAMIC QR PROJECTOR
// ==========================================
const projectorQrBase64 = ref(null)
const projectorToken = ref(null)
const isLoadingQr = ref(false)
const remainingSeconds = ref(1800)
let countdownTimer = null

const fetchProjectorQr = async () => {
    if (!selectedJadwalId.value) return
    isLoadingQr.value = true
    try {
        const res = await axios.get(`/admin/absensi/qr/${selectedJadwalId.value}?expired_minutes=30`)
        if (res.data.success) {
            projectorQrBase64.value = res.data.qr_image_base64
            projectorToken.value = res.data.token
            remainingSeconds.value = 1800
        }
    } catch (e) {
        console.error('Failed to generate projector QR', e)
    } finally {
        isLoadingQr.value = false
    }
}

const formattedCountdown = computed(() => {
    const m = Math.floor(remainingSeconds.value / 60).toString().padStart(2, '0')
    const s = (remainingSeconds.value % 60).toString().padStart(2, '0')
    return `${m}:${s}`
})

// Tab Switch Watcher
watch(activeTab, async (newTab) => {
    if (newTab === 'face' || newTab === 'scanner') {
        if (!stream || !cameraActive.value) {
            await startCamera()
        } else {
            await attachStreamToVideos()
            if (newTab === 'scanner') {
                requestAnimationFrame(tickQrScan)
            }
        }
    } else {
        stopCamera()
    }

    if (newTab === 'projector') {
        fetchProjectorQr()
        if (countdownTimer) clearInterval(countdownTimer)
        countdownTimer = setInterval(() => {
            if (remainingSeconds.value > 0) {
                remainingSeconds.value--
            } else {
                fetchProjectorQr()
            }
        }, 1000)
    } else {
        if (countdownTimer) clearInterval(countdownTimer)
    }
})

watch(selectedJadwalId, () => {
    if (activeTab.value === 'projector') {
        fetchProjectorQr()
    }
})

onMounted(() => {
    startCamera()
})

onUnmounted(() => {
    stopCamera()
    if (countdownTimer) clearInterval(countdownTimer)
    if (countdownTimerFace.value) clearInterval(countdownTimerFace.value)
})
</script>

<template>
    <Head title="Presensi Kamera Wajah & QR — Sistem PKBM" />

    <div class="p-4 sm:p-6 lg:p-8 max-w-[1500px] mx-auto space-y-6 font-sans">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="px-3 py-0.5 rounded-full text-xs font-bold bg-cyan-100 text-cyan-800 uppercase tracking-wider">
                        ⚡ AI Biometric Attendance Hub
                    </span>
                    <span class="text-xs text-slate-400 font-mono">{{ today }}</span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Presensi Kamera Wajah & Pemindai QR</h1>
                <p class="text-slate-500 text-xs sm:text-sm">Verifikasi biometrik pengenalan wajah real-time dan pemindai QR pintar terintegrasi.</p>
            </div>

            <!-- Schedule Selector -->
            <div class="w-full lg:w-auto">
                <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">Sesi Pelajaran Aktif:</label>
                <select
                    v-model="selectedJadwalId"
                    class="w-full lg:w-auto max-w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-800 text-xs font-bold focus:ring-2 focus:ring-cyan-500 focus:border-cyan-500 outline-none truncate"
                >
                    <option v-for="j in jadwals" :key="j.id" :value="j.id">
                        {{ j.kelas?.nama_kelas }} • {{ j.mata_pelajaran?.nama_mapel }} ({{ j.hari }}, {{ j.jam_mulai }})
                    </option>
                </select>
            </div>
        </div>

        <!-- Mode Switcher Tabs -->
        <div class="flex border-b border-slate-200 gap-2 sm:gap-4 text-xs font-bold overflow-x-auto pb-1 scrollbar-none">
            <button
                @click="activeTab = 'face'"
                :class="[
                    'pb-3 px-3 sm:px-4 border-b-2 transition flex items-center gap-2 whitespace-nowrap',
                    activeTab === 'face'
                        ? 'border-indigo-600 text-indigo-600'
                        : 'border-transparent text-slate-400 hover:text-slate-600'
                ]"
            >
                <span class="text-sm">👤</span>
                <span>Presensi Kamera Wajah AI (Biometric HUD)</span>
            </button>

            <button
                @click="activeTab = 'scanner'"
                :class="[
                    'pb-3 px-3 sm:px-4 border-b-2 transition flex items-center gap-2 whitespace-nowrap',
                    activeTab === 'scanner'
                        ? 'border-cyan-600 text-cyan-600'
                        : 'border-transparent text-slate-400 hover:text-slate-600'
                ]"
            >
                <span class="text-sm">📷</span>
                <span>Live QR Scanner HUD</span>
            </button>

            <button
                @click="activeTab = 'projector'"
                :class="[
                    'pb-3 px-3 sm:px-4 border-b-2 transition flex items-center gap-2 whitespace-nowrap',
                    activeTab === 'projector'
                        ? 'border-purple-600 text-purple-600'
                        : 'border-transparent text-slate-400 hover:text-slate-600'
                ]"
            >
                <span class="text-sm">📽️</span>
                <span>Proyektor QR Dinamis Kelas</span>
            </button>

            <button
                @click="activeTab = 'records'"
                :class="[
                    'pb-3 px-3 sm:px-4 border-b-2 transition flex items-center gap-2 whitespace-nowrap',
                    activeTab === 'records'
                        ? 'border-slate-800 text-slate-800'
                        : 'border-transparent text-slate-400 hover:text-slate-600'
                ]"
            >
                <span class="text-sm">📋</span>
                <span>Rekap Absensi Hari Ini ({{ absensis?.length || 0 }})</span>
            </button>
        </div>

        <!-- ======================================================== -->
        <!-- TAB 1: PRESENSI BIOMETRIK KAMERA WAJAH AI (FACE HUD)    -->
        <!-- ======================================================== -->
        <div v-show="activeTab === 'face'" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <!-- Face Viewfinder View -->
            <div class="lg:col-span-7 bg-slate-950 rounded-3xl p-4 sm:p-6 border border-slate-800 shadow-2xl relative overflow-hidden flex flex-col items-center justify-center min-h-[420px] sm:min-h-[500px]">
                <!-- Top Status Bar -->
                <div class="w-full flex items-center justify-between text-xs text-slate-400 mb-3 px-2 z-20 gap-2 flex-wrap">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400" :class="{ 'animate-ping': cameraActive }"></span>
                        <span class="font-mono text-emerald-300 font-bold uppercase text-[11px]">
                            {{ cameraActive ? 'AI BIOMETRIC FACE SCANNER ACTIVE' : 'KAMERA NONAKTIF' }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2">
                        <!-- Camera Selector Dropdown (if multiple devices found) -->
                        <select
                            v-if="availableCameras.length > 1"
                            v-model="selectedCameraId"
                            @change="onCameraChange"
                            class="px-2 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white text-[11px] font-semibold transition border border-white/10 outline-none max-w-[170px] truncate"
                            title="Pilih Perangkat Kamera"
                        >
                            <option v-for="cam in availableCameras" :key="cam.id" :value="cam.id" class="bg-slate-900 text-white">
                                📹 {{ cam.label }}
                            </option>
                        </select>

                        <button
                            @click="cameraActive ? stopCamera() : startCamera()"
                            class="px-2.5 py-1 rounded-lg bg-white/10 hover:bg-white/20 text-white text-[11px] font-semibold transition whitespace-nowrap active:scale-95"
                        >
                            {{ cameraActive ? 'Matikan Kamera' : 'Nyalakan Kamera' }}
                        </button>
                    </div>
                </div>

                <!-- Video Frame with Face Oval Reticle -->
                <div class="relative w-full max-w-md aspect-square bg-slate-900 rounded-2xl overflow-hidden border border-slate-800 flex items-center justify-center shadow-inner">
                    <video 
                        ref="videoFaceRef" 
                        class="w-full h-full object-cover transform -scale-x-100" 
                        autoplay 
                        playsinline 
                        muted
                    ></video>
                    <canvas ref="canvasRef" class="hidden"></canvas>

                    <!-- Inactive Camera Overlay Placeholder -->
                    <div v-if="!cameraActive && !cameraLoading && !cameraError" class="absolute inset-0 bg-slate-950/90 flex flex-col items-center justify-center p-6 text-center z-10">
                        <div class="w-16 h-16 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-3xl mb-3">
                            📷
                        </div>
                        <h4 class="text-sm font-bold text-white mb-1">Kamera Belum Aktif</h4>
                        <p class="text-slate-400 text-xs max-w-xs mb-4">
                            Klik tombol di bawah untuk menyalakan webcam atau unggah foto wajah jika kamera sedang digunakan aplikasi lain.
                        </p>
                        <div class="flex items-center gap-2">
                            <button
                                @click="startCamera()"
                                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-md"
                            >
                                <span>▶️</span> Nyalakan Kamera
                            </button>
                            <button
                                @click="fileInputRef?.click()"
                                class="px-3.5 py-2 bg-white/10 hover:bg-white/20 active:scale-95 text-slate-200 rounded-xl text-xs font-semibold transition"
                            >
                                📁 Unggah Foto
                            </button>
                        </div>
                    </div>

                    <!-- Oval Face Targeting Reticle -->
                    <div v-if="cameraActive" class="absolute inset-0 pointer-events-none flex flex-col items-center justify-center p-4">
                        <!-- Oval Outline -->
                        <div class="w-44 sm:w-56 h-56 sm:h-72 border-2 border-dashed border-cyan-400/80 rounded-[50%] relative flex items-center justify-center shadow-[0_0_20px_rgba(34,211,238,0.25)]">
                            <!-- Reticle Crosshairs -->
                            <div class="absolute -top-3 w-4 h-1 bg-cyan-400"></div>
                            <div class="absolute -bottom-3 w-4 h-1 bg-cyan-400"></div>
                            <div class="absolute -left-3 h-4 w-1 bg-cyan-400"></div>
                            <div class="absolute -right-3 h-4 w-1 bg-cyan-400"></div>

                            <!-- Animated Scanning Wave -->
                            <div class="absolute inset-x-4 h-0.5 bg-gradient-to-r from-transparent via-emerald-400 to-transparent shadow-[0_0_12px_#34d399] animate-bounce-slow"></div>

                            <!-- Center Landmark Target -->
                            <div class="w-12 h-12 border border-cyan-400/40 rounded-full flex items-center justify-center text-[10px] text-cyan-300 font-mono">
                                +
                            </div>
                        </div>

                        <!-- Instruction text overlay -->
                        <div class="mt-4 px-3 py-1 bg-slate-950/80 backdrop-blur rounded-full text-[11px] font-semibold text-cyan-300 border border-cyan-500/30">
                            Posisikan wajah Anda tepat di dalam lingkaran oval
                        </div>
                    </div>

                    <!-- Countdown Number Overlay -->
                    <div v-if="faceCountdown > 0" class="absolute inset-0 bg-slate-950/70 flex items-center justify-center z-30">
                        <span class="text-7xl font-black font-mono text-cyan-300 animate-ping">
                            {{ faceCountdown }}
                        </span>
                    </div>

                    <!-- Loading Overlay -->
                    <div v-if="cameraLoading" class="absolute inset-0 bg-slate-950/85 flex flex-col items-center justify-center text-white text-xs gap-2 z-20">
                        <div class="w-8 h-8 border-2 border-emerald-400 border-t-transparent rounded-full animate-spin"></div>
                        <span class="font-medium text-emerald-300">Menghubungkan ke Perangkat Kamera...</span>
                    </div>

                    <!-- Error Overlay with Troubleshooting -->
                    <div v-if="cameraError" class="absolute inset-0 bg-slate-950/95 p-6 flex flex-col items-center justify-center text-center text-slate-300 text-xs z-20">
                        <span class="text-3xl mb-2">⚠️</span>
                        <h4 class="text-sm font-bold text-rose-400 mb-1">Kamera Bermasalah</h4>
                        <p class="text-slate-300 text-[11px] mb-3 max-w-xs leading-relaxed">{{ cameraError }}</p>
                        <div class="flex items-center gap-2">
                            <button
                                @click="startCamera()"
                                class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-bold transition"
                            >
                                🔄 Coba Lagi
                            </button>
                            <button
                                @click="fileInputRef?.click()"
                                class="px-3.5 py-1.5 bg-white/10 hover:bg-white/20 text-slate-200 rounded-lg text-xs font-semibold transition"
                            >
                                📁 Gunakan Foto File
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action Controls Below Camera -->
                <div class="mt-5 flex flex-wrap items-center justify-center gap-3 w-full">
                    <button
                        @click="captureAndVerifyFace"
                        :disabled="isVerifyingFace || !cameraActive"
                        class="px-6 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-2xl font-bold text-xs uppercase tracking-wider shadow-lg shadow-emerald-500/25 transition flex items-center gap-2 disabled:opacity-50"
                    >
                        <span v-if="isVerifyingFace" class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        <span>{{ isVerifyingFace ? 'Menganalisis Biometrik...' : '📸 Ambil Presensi Wajah Sekarang' }}</span>
                    </button>

                    <button
                        @click="startFaceCountdown"
                        :disabled="isVerifyingFace || !cameraActive || faceCountdown > 0"
                        class="px-4 py-3 bg-white/10 hover:bg-white/20 border border-white/20 text-white rounded-2xl font-semibold text-xs transition disabled:opacity-40"
                    >
                        ⏱️ Timer 3 Detik
                    </button>

                    <!-- Hidden File Input for fallback photo upload -->
                    <input
                        ref="fileInputRef"
                        type="file"
                        accept="image/*"
                        capture="user"
                        class="hidden"
                        @change="handleFileUploadForFace"
                    />
                    <button
                        @click="fileInputRef?.click()"
                        type="button"
                        class="px-3.5 py-3 bg-slate-800/80 hover:bg-slate-800 border border-slate-700 text-slate-300 rounded-2xl font-semibold text-xs transition flex items-center gap-1.5"
                        title="Unggah foto wajah jika webcam tidak aktif"
                    >
                        <span>📁</span>
                        <span>Unggah Foto</span>
                    </button>
                </div>
            </div>

            <!-- Face Verification Details Panel -->
            <div class="lg:col-span-5 space-y-6">
                <!-- Select Student Target -->
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-900 mb-1 flex items-center gap-2">
                        <span>👤</span> <span>Target Identifikasi Warga Belajar</span>
                    </h3>
                    <p class="text-xs text-slate-500 mb-3">Pilih warga belajar yang sedang berada di depan kamera:</p>

                    <div>
                        <select
                            v-model="selectedSiswaIdForFace"
                            class="w-full px-4 py-3 rounded-xl border border-slate-200 bg-slate-50 text-slate-900 text-xs font-bold focus:ring-2 focus:ring-indigo-500 outline-none"
                        >
                            <option v-for="s in siswas" :key="s.id" :value="s.id">
                                {{ s.nama_lengkap }} (NISN: {{ s.nisn }})
                            </option>
                        </select>
                    </div>

                    <!-- Notification -->
                    <div
                        v-if="faceNotification"
                        :class="[
                            'mt-3 p-3 rounded-xl text-xs font-semibold flex items-center gap-2',
                            faceNotification.type === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'
                        ]"
                    >
                        <span>{{ faceNotification.type === 'success' ? '✅' : '❌' }}</span>
                        <span>{{ faceNotification.text }}</span>
                    </div>
                </div>

                <!-- Verified Biometric Result Card -->
                <div v-if="faceResult" class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 text-white border border-emerald-500/30 shadow-2xl relative overflow-hidden">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-[10px] uppercase font-bold text-emerald-300 tracking-wider flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            BIOMETRIC MATCH VERIFIED
                        </span>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-[10px] font-bold">
                            HADIR
                        </span>
                    </div>

                    <div class="flex items-center gap-4">
                        <!-- Captured snapshot thumbnail -->
                        <div class="relative w-16 h-16 rounded-2xl overflow-hidden border-2 border-emerald-400 shadow-md">
                            <img v-if="faceSnapshot" :src="faceSnapshot" alt="Face Snapshot" class="w-full h-full object-cover" />
                            <div v-else class="w-full h-full bg-slate-800 flex items-center justify-center">👤</div>
                        </div>

                        <div>
                            <h4 class="text-lg font-extrabold text-white leading-tight">{{ faceResult.siswa?.nama_lengkap }}</h4>
                            <div class="font-mono text-xs text-emerald-300 mt-0.5">NISN: {{ faceResult.siswa?.nisn }}</div>
                            <div class="text-xs text-slate-300 mt-1">{{ faceResult.siswa?.program }}</div>
                        </div>
                    </div>

                    <!-- Metrics Grid -->
                    <div class="mt-4 pt-4 border-t border-white/10 grid grid-cols-2 gap-3 text-xs">
                        <div class="p-2.5 bg-white/5 rounded-xl border border-white/10">
                            <div class="text-[10px] text-slate-400 uppercase">Kecocokan Wajah</div>
                            <div class="text-base font-extrabold text-cyan-300 font-mono">{{ faceResult.confidence }}%</div>
                        </div>
                        <div class="p-2.5 bg-white/5 rounded-xl border border-white/10">
                            <div class="text-[10px] text-slate-400 uppercase">Skor Liveness AI</div>
                            <div class="text-base font-extrabold text-emerald-300 font-mono">{{ faceResult.liveness }}%</div>
                        </div>
                    </div>
                </div>

                <!-- Guide Tip -->
                <div class="bg-blue-50/70 rounded-3xl p-5 border border-blue-100 text-xs text-blue-900 space-y-1.5">
                    <div class="font-bold flex items-center gap-1.5">
                        <span>💡</span> <span>Tips Presensi Biometrik Optimal:</span>
                    </div>
                    <ul class="list-disc pl-4 space-y-1 text-blue-800 text-[11px]">
                        <li>Pastikan wajah menghadap lurus ke kamera dan tidak tertutup masker.</li>
                        <li>Gunakan pencahayaan ruangan yang cukup terang (tidak backlight).</li>
                        <li>Sistem otomatis menghitung kemiripan biometrik & ketajaman tekstur.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- TAB 2: LIVE CAMERA QR SCANNER                            -->
        <!-- ======================================================== -->
        <div v-show="activeTab === 'scanner'" class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
            <div class="lg:col-span-7 bg-slate-950 rounded-3xl p-6 border border-slate-800 shadow-2xl relative overflow-hidden flex flex-col items-center justify-center min-h-[460px]">
                <div class="w-full flex items-center justify-between text-xs text-slate-400 mb-3 px-2 z-20">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-cyan-400 animate-ping"></span>
                        <span class="font-mono text-cyan-300 font-bold uppercase">OPTICAL QR SCANNER ACTIVE</span>
                    </div>
                </div>

                <div class="relative w-full max-w-md aspect-square bg-slate-900 rounded-2xl overflow-hidden border border-slate-800 flex items-center justify-center">
                    <video 
                        ref="videoQrRef" 
                        class="w-full h-full object-cover" 
                        autoplay 
                        playsinline 
                        muted
                    ></video>

                    <div v-if="cameraActive" class="absolute inset-0 pointer-events-none p-6 flex flex-col justify-between">
                        <div class="absolute inset-x-0 h-1 bg-gradient-to-r from-transparent via-cyan-400 to-transparent shadow-[0_0_15px_#22d3ee] animate-bounce-slow"></div>
                        <div class="flex justify-between">
                            <div class="w-8 h-8 border-t-2 border-l-2 border-cyan-400 rounded-tl-lg"></div>
                            <div class="w-8 h-8 border-t-2 border-r-2 border-cyan-400 rounded-tr-lg"></div>
                        </div>
                        <div class="flex justify-between">
                            <div class="w-8 h-8 border-b-2 border-l-2 border-cyan-400 rounded-bl-lg"></div>
                            <div class="w-8 h-8 border-b-2 border-r-2 border-cyan-400 rounded-br-lg"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Side Input -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm">
                    <h3 class="text-sm font-bold text-slate-900 mb-1 flex items-center gap-2">
                        <span>🔫</span> <span>Input Cepat NISN / Barcode Gun</span>
                    </h3>
                    <p class="text-xs text-slate-500 mb-4">Mendukung pemindai USB barcode fisik atau pengetikan langsung nomor NISN.</p>

                    <form @submit.prevent="submitManual" class="space-y-3">
                        <div class="relative">
                            <input
                                type="text"
                                v-model="manualNisn"
                                placeholder="Scan kartu atau ketik NISN..."
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 text-slate-900 font-mono text-sm font-bold placeholder-slate-400 focus:border-cyan-500 focus:ring-2 focus:ring-cyan-500/20 outline-none"
                            />
                            <button
                                type="submit"
                                :disabled="!manualNisn || isSubmittingScan"
                                class="absolute right-2 top-2 px-3 py-1.5 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg text-xs font-bold transition disabled:opacity-40"
                            >
                                {{ isSubmittingScan ? '...' : 'Absen' }}
                            </button>
                        </div>
                    </form>

                    <div
                        v-if="scanNotification"
                        :class="[
                            'mt-3 p-3 rounded-xl text-xs font-semibold flex items-center gap-2',
                            scanNotification.type === 'success' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200'
                        ]"
                    >
                        <span>{{ scanNotification.type === 'success' ? '✅' : '❌' }}</span>
                        <span>{{ scanNotification.text }}</span>
                    </div>
                </div>

                <div v-if="lastScannedStudent" class="bg-gradient-to-br from-slate-900 via-indigo-950 to-slate-900 rounded-3xl p-6 text-white border border-cyan-500/30 shadow-2xl">
                    <div class="text-[10px] uppercase font-bold text-cyan-300 tracking-wider mb-2">PRESENSI BERHASIL</div>
                    <div class="text-base font-extrabold text-white">{{ lastScannedStudent.nama_lengkap }}</div>
                    <div class="font-mono text-xs text-cyan-300">NISN: {{ lastScannedStudent.nisn }}</div>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- TAB 3: PROYEKTOR QR DINAMIS KELAS                         -->
        <!-- ======================================================== -->
        <div v-show="activeTab === 'projector'" class="bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 rounded-3xl p-8 text-white border border-indigo-500/30 shadow-2xl flex flex-col items-center justify-center text-center">
            <div class="max-w-xl mx-auto space-y-4">
                <span class="inline-block px-3 py-1 bg-indigo-500/20 border border-indigo-400/30 rounded-full text-xs font-semibold text-indigo-300">
                    PYTHON FASTAPI DYNAMIC QR ENGINE
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold">
                    {{ selectedJadwal?.kelas?.nama_kelas }} — {{ selectedJadwal?.mata_pelajaran?.nama_mapel }}
                </h2>

                <div class="my-6 p-6 bg-white rounded-3xl shadow-2xl inline-block border-4 border-indigo-400/40">
                    <img
                        v-if="projectorQrBase64"
                        :src="`data:image/png;base64,${projectorQrBase64}`"
                        alt="QR Code Presensi"
                        class="w-64 h-64 sm:w-80 sm:h-80 mx-auto"
                    />
                    <div v-else-if="isLoadingQr" class="w-64 h-64 sm:w-80 sm:h-80 flex flex-col items-center justify-center text-slate-400 text-xs">
                        <div class="w-10 h-10 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin mb-3"></div>
                        <span>Men-generate QR dari Python...</span>
                    </div>
                </div>

                <div class="flex items-center justify-center gap-4">
                    <div class="px-5 py-2.5 bg-white/5 border border-white/10 rounded-2xl">
                        <div class="text-[10px] uppercase font-bold text-slate-400">Masa Berlaku:</div>
                        <div class="font-mono text-2xl font-black text-cyan-300">{{ formattedCountdown }}</div>
                    </div>
                    <button
                        @click="fetchProjectorQr"
                        class="px-5 py-3.5 bg-gradient-to-r from-indigo-600 to-cyan-600 rounded-2xl font-bold text-xs uppercase tracking-wider text-white shadow-lg transition"
                    >
                        🔄 Perbarui QR Sekarang
                    </button>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- TAB 4: REKAP ABSENSI HARI INI                            -->
        <!-- ======================================================== -->
        <div v-show="activeTab === 'records'" class="space-y-6">
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm text-center">
                    <span class="text-xs font-bold uppercase text-slate-400">Total Hadir</span>
                    <div class="text-3xl font-extrabold text-emerald-600 mt-1">{{ stats?.total_hadir || 0 }}</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm text-center">
                    <span class="text-xs font-bold uppercase text-slate-400">Sakit</span>
                    <div class="text-3xl font-extrabold text-blue-600 mt-1">{{ stats?.total_sakit || 0 }}</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm text-center">
                    <span class="text-xs font-bold uppercase text-slate-400">Izin</span>
                    <div class="text-3xl font-extrabold text-amber-600 mt-1">{{ stats?.total_izin || 0 }}</div>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm text-center">
                    <span class="text-xs font-bold uppercase text-slate-400">Alpha</span>
                    <div class="text-3xl font-extrabold text-rose-600 mt-1">{{ stats?.total_alpha || 0 }}</div>
                </div>
            </div>

            <div class="bg-white rounded-3xl p-6 border border-slate-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-bold text-slate-900 text-base">Daftar Warga Belajar Hadir Hari Ini</h3>
                    <span class="text-xs text-slate-400 font-mono">{{ absensis?.length || 0 }} record tersimpan</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="uppercase text-slate-400 border-b border-slate-100 text-[11px]">
                            <tr>
                                <th class="py-3 px-4">Warga Belajar</th>
                                <th class="py-3 px-4">NISN</th>
                                <th class="py-3 px-4">Mata Pelajaran & Kelas</th>
                                <th class="py-3 px-4">Metode Absen</th>
                                <th class="py-3 px-4 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <tr v-for="a in absensis" :key="a.id" class="hover:bg-slate-50/70 transition">
                                <td class="py-3 px-4 font-semibold text-slate-900">{{ a.siswa?.nama_lengkap }}</td>
                                <td class="py-3 px-4 font-mono text-slate-500">{{ a.siswa?.nisn }}</td>
                                <td class="py-3 px-4 text-slate-600">
                                    {{ a.jadwal?.mata_pelajaran?.nama_mapel }} • {{ a.jadwal?.kelas?.nama_kelas }}
                                </td>
                                <td class="py-3 px-4 font-mono text-slate-500">
                                    <span v-if="a.qr_token?.startsWith('BIOMETRIC_FACE')" class="px-2 py-0.5 rounded bg-purple-100 text-purple-700 text-[10px] font-bold">
                                        👤 Face AI
                                    </span>
                                    <span v-else class="px-2 py-0.5 rounded bg-cyan-100 text-cyan-700 text-[10px] font-bold">
                                        📷 QR / Barcode
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700">
                                        HADIR
                                    </span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes bounceSlow {
    0%, 100% { transform: translateY(-70px); }
    50% { transform: translateY(70px); }
}

.animate-bounce-slow {
    animation: bounceSlow 2.2s ease-in-out infinite;
}
</style>
