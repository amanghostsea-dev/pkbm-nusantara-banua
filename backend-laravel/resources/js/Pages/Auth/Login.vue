<script setup>
import { ref, computed } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'

defineProps({
    status: String,
})

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const showPassword = ref(false)

// 3D Parallax Tilt Logic
const cardRef = ref(null)
const rotateX = ref(0)
const rotateY = ref(0)
const shineX = ref(50)
const shineY = ref(50)
const isHovered = ref(false)

// Mouse Parallax for Floating Background & Foreground Elements
const mouseOffsetX = ref(0)
const mouseOffsetY = ref(0)

const handleMouseMove = (e) => {
    const windowWidth = window.innerWidth
    const windowHeight = window.innerHeight
    
    // Normalized -1 to 1 for global parallax
    mouseOffsetX.value = ((e.clientX - windowWidth / 2) / (windowWidth / 2))
    mouseOffsetY.value = ((e.clientY - windowHeight / 2) / (windowHeight / 2))

    if (!cardRef.value) return
    const rect = cardRef.value.getBoundingClientRect()
    const x = e.clientX - rect.left
    const y = e.clientY - rect.top
    const centerX = rect.width / 2
    const centerY = rect.height / 2

    // Max 12 deg tilt for sophisticated 3D feel
    rotateX.value = -((y - centerY) / centerY) * 12
    rotateY.value = ((x - centerX) / centerX) * 12

    shineX.value = (x / rect.width) * 100
    shineY.value = (y / rect.height) * 100
}

const handleMouseEnter = () => {
    isHovered.value = true
}

const handleMouseLeave = () => {
    isHovered.value = false
    rotateX.value = 0
    rotateY.value = 0
    shineX.value = 50
    shineY.value = 50
}

const cardTransformStyle = computed(() => {
    return {
        transform: isHovered.value
            ? `perspective(1100px) rotateX(${rotateX.value.toFixed(2)}deg) rotateY(${rotateY.value.toFixed(2)}deg) translateZ(10px)`
            : 'perspective(1100px) rotateX(0deg) rotateY(0deg) translateZ(0px)',
        transition: isHovered.value ? 'transform 0.08s ease-out' : 'transform 0.6s cubic-bezier(0.2, 0.8, 0.2, 1)',
    }
})

const shineStyle = computed(() => {
    return {
        background: `radial-gradient(circle 380px at ${shineX.value}% ${shineY.value}%, rgba(255, 255, 255, 0.2), transparent 70%)`,
    }
})

// Floating 3D Logos configuration (multi-layered depths and sizes)
const floatingLogos = [
    // Left Wing
    { id: 'left-1', top: '12%', left: '3%', size: 'w-24 h-24 xl:w-28 xl:h-28', depth: 45, floatClass: 'anim-float-1', delay: '0s', opacity: 'opacity-90', glow: 'bg-amber-400/30' },
    { id: 'left-2', top: '44%', left: '7%', size: 'w-16 h-16 xl:w-20 xl:h-20', depth: 30, floatClass: 'anim-float-2', delay: '1.8s', opacity: 'opacity-80', glow: 'bg-yellow-400/25' },
    { id: 'left-3', top: '74%', left: '2%', size: 'w-20 h-20 xl:w-24 xl:h-24', depth: 40, floatClass: 'anim-float-3', delay: '3.2s', opacity: 'opacity-85', glow: 'bg-amber-500/30' },
    { id: 'left-4', top: '28%', left: '14%', size: 'w-12 h-12 xl:w-14 xl:h-14', depth: 18, floatClass: 'anim-float-4', delay: '2.5s', opacity: 'opacity-55', glow: 'bg-blue-400/20' },

    // Right Wing
    { id: 'right-1', top: '10%', right: '4%', size: 'w-22 h-22 xl:w-26 xl:h-26', depth: 42, floatClass: 'anim-float-3', delay: '0.9s', opacity: 'opacity-90', glow: 'bg-amber-400/30' },
    { id: 'right-2', top: '42%', right: '6%', size: 'w-24 h-24 xl:w-30 xl:h-30', depth: 50, floatClass: 'anim-float-1', delay: '2.4s', opacity: 'opacity-95', glow: 'bg-yellow-300/35' },
    { id: 'right-3', top: '75%', right: '3%', size: 'w-18 h-18 xl:w-22 xl:h-22', depth: 28, floatClass: 'anim-float-2', delay: '1.2s', opacity: 'opacity-80', glow: 'bg-amber-500/25' },
    { id: 'right-4', top: '26%', right: '14%', size: 'w-12 h-12 xl:w-14 xl:h-14', depth: 16, floatClass: 'anim-float-4', delay: '3.7s', opacity: 'opacity-55', glow: 'bg-indigo-400/20' },
]

const getParallaxTransform = (depth) => {
    const px = (mouseOffsetX.value * depth * 0.75).toFixed(1)
    const py = (mouseOffsetY.value * depth * 0.75).toFixed(1)
    return `translate3d(${px}px, ${py}px, 0px)`
}

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Masuk ke Sistem PKBM Basara" />

    <div
        class="min-h-screen flex items-center justify-center bg-slate-950 px-4 py-8 relative overflow-hidden font-sans select-none"
        @mousemove="handleMouseMove"
    >
        <!-- 3D Space Horizon -->
        <div class="absolute inset-0 bg-radial-gradient pointer-events-none"></div>

        <!-- 3D Perspective Cyber Floor Grid -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none opacity-25">
            <div class="perspective-grid"></div>
        </div>

        <!-- Dynamic Ambient Glows -->
        <div class="absolute -top-32 -left-32 w-[32rem] h-[32rem] bg-blue-600/20 rounded-full blur-[140px] pointer-events-none animate-pulse-slow"></div>
        <div class="absolute -bottom-32 -right-32 w-[34rem] h-[34rem] bg-amber-500/15 rounded-full blur-[150px] pointer-events-none animate-pulse-slow delay-1000"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[48rem] h-[48rem] bg-indigo-600/10 rounded-full blur-[170px] pointer-events-none"></div>

        <!-- Floating 3D Geometric Accents -->
        <div class="floating-cube cube-1 pointer-events-none hidden xl:block"></div>
        <div class="floating-cube cube-2 pointer-events-none hidden xl:block"></div>
        <div class="floating-cube cube-3 pointer-events-none hidden xl:block"></div>

        <!-- =================================================================== -->
        <!-- FLOATING 3D EMBLEMS (RANDOM & ELEGANT ORBITING LOGOS)               -->
        <!-- =================================================================== -->
        <div
            v-for="item in floatingLogos"
            :key="item.id"
            class="absolute pointer-events-none hidden md:block z-0 transition-transform duration-100 ease-out"
            :style="{
                top: item.top,
                left: item.left,
                right: item.right,
                transform: getParallaxTransform(item.depth)
            }"
        >
            <div :class="[item.floatClass, 'transform-gpu']" :style="{ animationDelay: item.delay }">
                <div class="relative group/orbit">
                    <!-- Holographic Aura -->
                    <div :class="['absolute -inset-2.5 rounded-full blur-md opacity-80', item.glow, 'animate-pulse-slow']"></div>

                    <!-- 3D Plaque -->
                    <div
                        :class="[
                            item.size,
                            item.opacity,
                            'relative rounded-full p-1 bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700 shadow-[0_15px_35px_rgba(0,0,0,0.7),0_0_25px_rgba(245,158,11,0.35)] backdrop-blur-sm'
                        ]"
                    >
                        <img
                            src="/images/logo.png"
                            alt="Logo Yayasan Banua Insan Nusantara BASARA"
                            class="w-full h-full rounded-full object-cover shadow-inner ring-1 ring-white/30"
                        />
                    </div>

                    <!-- Floating Soft 3D Depth Ground Shadow -->
                    <div class="w-3/4 h-2.5 bg-black/80 mx-auto rounded-full blur-sm mt-1.5 transform scale-x-90"></div>
                </div>
            </div>
        </div>

        <!-- =================================================================== -->
        <!-- MAIN 3D PERSPECTIVE LOGIN CARD                                      -->
        <!-- =================================================================== -->
        <div class="w-full max-w-md relative z-10 perspective-container">
            <!-- Animated 3D Glow Border Backing -->
            <div class="absolute -inset-1 bg-gradient-to-r from-blue-500/40 via-amber-400/40 to-indigo-500/40 rounded-[2.5rem] blur-xl opacity-75 group-hover:opacity-100 transition duration-500 animate-border-glow"></div>

            <!-- Main Interactive 3D Card -->
            <div
                ref="cardRef"
                :style="cardTransformStyle"
                @mouseenter="handleMouseEnter"
                @mouseleave="handleMouseLeave"
                class="relative w-full bg-slate-900/75 backdrop-blur-3xl border border-white/20 p-8 sm:p-10 rounded-[2.25rem] shadow-[0_30px_70px_-15px_rgba(0,0,0,0.85),0_0_45px_rgba(59,130,246,0.18)] text-white preserve-3d overflow-hidden"
            >
                <!-- Specular Dynamic Glare Overlay -->
                <div
                    class="absolute inset-0 pointer-events-none transition-opacity duration-300 rounded-[2.25rem]"
                    :style="shineStyle"
                ></div>

                <!-- Top Edge Light Reflection -->
                <div class="absolute top-0 inset-x-8 h-[1px] bg-gradient-to-r from-transparent via-white/50 to-transparent pointer-events-none"></div>

                <!-- 3D Layer: Central Floating Official Emblem -->
                <div class="text-center mb-6 transform-layer-logo">
                    <div class="relative inline-block mb-3 group">
                        <!-- Holographic 3D Halo -->
                        <div class="absolute -inset-2.5 bg-gradient-to-tr from-amber-400/70 via-yellow-300/50 to-blue-500/70 rounded-full blur-lg opacity-85 group-hover:opacity-100 animate-spin-slow pointer-events-none"></div>

                        <!-- 3D Floating Plaque -->
                        <div class="relative w-28 h-28 sm:w-32 sm:h-32 mx-auto rounded-full p-1.5 bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700 shadow-[0_20px_40px_rgba(0,0,0,0.7),0_0_30px_rgba(245,158,11,0.4)]">
                            <img
                                src="/images/logo.png"
                                alt="Logo Yayasan Banua Insan Nusantara - BASARA"
                                class="w-full h-full rounded-full object-cover shadow-inner transform transition-transform duration-500 group-hover:scale-105"
                            />
                        </div>

                        <!-- Floating 3D Depth Shadow under Logo -->
                        <div class="w-24 h-3 bg-black/80 mx-auto rounded-full blur-sm mt-1 transform translate-z-[-10px]"></div>
                    </div>

                    <!-- Institution Headers -->
                    <div class="transform-layer-title mt-1">
                        <h1 class="text-3xl font-black tracking-tight bg-gradient-to-r from-white via-amber-200 to-blue-200 bg-clip-text text-transparent drop-shadow-[0_4px_12px_rgba(0,0,0,0.5)]">
                            PKBM BASARA
                        </h1>
                        <p class="text-amber-300/95 text-xs sm:text-sm font-bold tracking-wider uppercase mt-1 drop-shadow-sm">
                            Yayasan Banua Insan Nusantara
                        </p>
                        <p class="text-blue-200/80 text-xs font-medium tracking-wide">
                            Kalimantan Selatan • Indonesia
                        </p>
                    </div>

                    <!-- Package Badges with 3D Depth -->
                    <div class="flex items-center justify-center gap-2 mt-3 text-[11px] font-semibold transform-layer-badges">
                        <span class="px-3 py-1 rounded-full bg-blue-500/20 border border-blue-400/40 text-blue-200 shadow-sm backdrop-blur-sm">Paket A (SD)</span>
                        <span class="px-3 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/40 text-emerald-200 shadow-sm backdrop-blur-sm">Paket B (SMP)</span>
                        <span class="px-3 py-1 rounded-full bg-amber-500/20 border border-amber-400/40 text-amber-200 shadow-sm backdrop-blur-sm">Paket C (SMA)</span>
                    </div>
                </div>

                <!-- 3D Public Verification CTA Banner -->
                <div class="mb-5 transform-layer-cta">
                    <a
                        href="/verifikasi"
                        class="group/btn relative w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-teal-500/20 via-emerald-500/20 to-teal-500/20 hover:from-teal-500/30 hover:to-emerald-500/30 border border-teal-400/30 text-teal-200 hover:text-white font-bold text-xs transition-all duration-300 flex items-center justify-center gap-2 shadow-lg shadow-teal-950/40 active:scale-[0.98]"
                    >
                        <span class="text-base group-hover/btn:scale-125 transition-transform duration-200">🔍</span>
                        <span>Verifikasi Keaslian Ijazah & Raport Publik</span>
                        <span class="group-hover/btn:translate-x-1 transition-transform duration-200">&rarr;</span>
                    </a>
                </div>

                <div v-if="status" class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-400/40 font-medium text-xs text-emerald-300 text-center shadow-lg">
                    {{ status }}
                </div>

                <!-- 3D Interactive Form -->
                <form @submit.prevent="submit" class="space-y-4 transform-layer-form">
                    <!-- Email Field -->
                    <div class="relative group/input">
                        <label class="block text-xs font-bold uppercase tracking-wider text-blue-200/90 mb-1.5 flex items-center gap-1.5">
                            <span>📧</span>
                            <span>Alamat Email</span>
                        </label>
                        <div class="relative">
                            <input
                                type="email"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="email"
                                class="w-full pl-4 pr-4 py-3 rounded-xl bg-slate-950/60 border border-white/20 focus:border-blue-400 focus:ring-4 focus:ring-blue-500/20 text-white placeholder-gray-500 outline-none transition-all duration-300 text-sm shadow-inner"
                                placeholder="contoh@pkbm.sch.id"
                            />
                        </div>
                        <p v-if="form.errors.email" class="text-rose-400 text-xs mt-1.5 font-medium">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password Field with 3D Eye Toggle -->
                    <div class="relative group/input">
                        <label class="block text-xs font-bold uppercase tracking-wider text-blue-200/90 mb-1.5 flex items-center gap-1.5">
                            <span>🔒</span>
                            <span>Kata Sandi / Password</span>
                        </label>
                        <div class="relative">
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                class="w-full pl-4 pr-11 py-3 rounded-xl bg-slate-950/60 border border-white/20 focus:border-blue-400 focus:ring-4 focus:ring-blue-500/20 text-white placeholder-gray-500 outline-none transition-all duration-300 text-sm shadow-inner"
                                placeholder="Masukkan password akun Anda"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-white p-1 rounded-lg transition-colors focus:outline-none"
                                :title="showPassword ? 'Sembunyikan password' : 'Lihat password'"
                            >
                                <span class="text-base">{{ showPassword ? '👁️' : '🙈' }}</span>
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="text-rose-400 text-xs mt-1.5 font-medium">{{ form.errors.password }}</p>
                    </div>

                    <!-- Remember Session Checkbox -->
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center space-x-2.5 cursor-pointer select-none group/check">
                            <input
                                type="checkbox"
                                v-model="form.remember"
                                class="w-4 h-4 rounded bg-slate-950/60 border-white/30 text-blue-500 focus:ring-0 focus:ring-offset-0 cursor-pointer transition"
                            />
                            <span class="text-blue-200/90 group-hover/check:text-white transition">Ingat sesi login saya</span>
                        </label>
                    </div>

                    <!-- 3D Tactile Action Button -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="relative w-full py-3.5 px-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-500 hover:from-blue-500 hover:to-indigo-500 active:translate-y-0.5 font-extrabold text-sm rounded-xl text-white shadow-[0_8px_0_rgb(30,58,138),0_15px_25px_rgba(37,99,235,0.4)] active:shadow-[0_2px_0_rgb(30,58,138),0_5px_10px_rgba(37,99,235,0.3)] transition-all duration-150 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 border border-blue-400/40"
                        >
                            <span v-if="form.processing" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                            <span class="tracking-wide">{{ form.processing ? 'Memverifikasi Kredensial...' : 'Masuk ke Sistem PKBM' }}</span>
                            <span v-if="!form.processing" class="text-base">&rarr;</span>
                        </button>
                    </div>
                </form>

                <!-- 3D Footer Diagnostics -->
                <div class="mt-8 pt-5 border-t border-white/10 flex items-center justify-between text-[11px] text-blue-300/70 transform-layer-footer">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                        </span>
                        <span>Laravel 11 Engine</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                        </span>
                        <span>Python ML Engine</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* 3D Transform Contexts */
.preserve-3d {
    transform-style: preserve-3d;
}

.perspective-container {
    perspective: 1200px;
}

/* Layered 3D Depth Offsets */
.transform-layer-logo {
    transform: translateZ(50px);
}

.transform-layer-title {
    transform: translateZ(40px);
}

.transform-layer-badges {
    transform: translateZ(32px);
}

.transform-layer-cta {
    transform: translateZ(28px);
}

.transform-layer-form {
    transform: translateZ(35px);
}

.transform-layer-footer {
    transform: translateZ(20px);
}

/* 3D Cyber Perspective Grid in Background */
.bg-radial-gradient {
    background: radial-gradient(circle at 50% 40%, rgba(30, 58, 138, 0.25) 0%, rgba(2, 6, 23, 0.95) 75%);
}

.perspective-grid {
    position: absolute;
    bottom: -150px;
    left: -50%;
    right: -50%;
    height: 600px;
    background-image: 
        linear-gradient(to right, rgba(59, 130, 246, 0.25) 1px, transparent 1px),
        linear-gradient(to bottom, rgba(59, 130, 246, 0.25) 1px, transparent 1px);
    background-size: 50px 50px;
    transform: perspective(400px) rotateX(65deg);
    transform-origin: bottom center;
    mask-image: linear-gradient(to top, rgba(0, 0, 0, 1) 0%, transparent 80%);
    animation: grid-slide 20s linear infinite;
}

@keyframes grid-slide {
    0% {
        background-position: 0 0;
    }
    100% {
        background-position: 0 50px;
    }
}

/* Keyframe Animations */
@keyframes spinSlow {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin-slow {
    animation: spinSlow 12s linear infinite;
}

.animate-pulse-slow {
    animation: pulse 6s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Floating 3D Geometric Accents */
.floating-cube {
    position: absolute;
    width: 60px;
    height: 60px;
    border: 1px solid rgba(59, 130, 246, 0.3);
    border-radius: 12px;
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.05), rgba(59, 130, 246, 0.1));
    backdrop-filter: blur(8px);
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
}

.cube-1 {
    top: 15%;
    left: 18%;
    transform: rotate(25deg) skew(-10deg);
    animation: float1 8s ease-in-out infinite;
}

.cube-2 {
    bottom: 18%;
    left: 14%;
    width: 45px;
    height: 45px;
    border-color: rgba(245, 158, 11, 0.3);
    transform: rotate(-15deg);
    animation: float2 9s ease-in-out infinite 1s;
}

.cube-3 {
    top: 20%;
    right: 18%;
    width: 70px;
    height: 70px;
    border-color: rgba(99, 102, 241, 0.35);
    transform: rotate(45deg);
    animation: float3 10s ease-in-out infinite 2s;
}

/* 4 Distinct Floating Physics Animations for Multiple Logos */
.anim-float-1 {
    animation: floatPath1 7s ease-in-out infinite;
}

.anim-float-2 {
    animation: floatPath2 9s ease-in-out infinite;
}

.anim-float-3 {
    animation: floatPath3 8.5s ease-in-out infinite;
}

.anim-float-4 {
    animation: floatPath4 11s ease-in-out infinite;
}

@keyframes floatPath1 {
    0%, 100% {
        transform: translateY(0px) rotate(-6deg) scale(1);
    }
    50% {
        transform: translateY(-22px) rotate(4deg) scale(1.04);
    }
}

@keyframes floatPath2 {
    0%, 100% {
        transform: translateY(0px) rotate(8deg) scale(0.98);
    }
    50% {
        transform: translateY(-18px) rotate(-4deg) scale(1.02);
    }
}

@keyframes floatPath3 {
    0%, 100% {
        transform: translateY(0px) rotate(-10deg) scale(1.02);
    }
    50% {
        transform: translateY(-26px) rotate(6deg) scale(0.99);
    }
}

@keyframes floatPath4 {
    0%, 100% {
        transform: translateY(0px) rotate(5deg) scale(0.95);
    }
    50% {
        transform: translateY(-15px) rotate(-8deg) scale(1.03);
    }
}

@keyframes float1 {
    0%, 100% {
        transform: translateY(0px) rotate(25deg);
    }
    50% {
        transform: translateY(-20px) rotate(35deg);
    }
}

@keyframes float2 {
    0%, 100% {
        transform: translateY(0px) rotate(-15deg);
    }
    50% {
        transform: translateY(-15px) rotate(-5deg);
    }
}

@keyframes float3 {
    0%, 100% {
        transform: translateY(0px) rotate(45deg);
    }
    50% {
        transform: translateY(-25px) rotate(55deg);
    }
}
</style>
