<script setup>
/**
 * Halaman Login PKBM BASARA
 * Tampilan Tenang, Elegan, dan Eksekutif (Stationary Luxury Glassmorphism)
 */
import { ref } from 'vue'
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

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Masuk ke Sistem PKBM Basara" />

    <div class="min-h-screen flex items-center justify-center bg-slate-950 px-4 py-8 relative overflow-hidden font-sans select-none">
        <!-- Calm Ambient Gradient Lighting (Stationary, Non-Moving) -->
        <div class="absolute inset-0 bg-radial-vignette pointer-events-none"></div>
        <div class="absolute -top-32 -left-32 w-[34rem] h-[34rem] bg-blue-900/20 rounded-full blur-[140px] pointer-events-none"></div>
        <div class="absolute -bottom-32 -right-32 w-[34rem] h-[34rem] bg-indigo-900/20 rounded-full blur-[150px] pointer-events-none"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[40rem] h-[40rem] bg-amber-500/5 rounded-full blur-[160px] pointer-events-none"></div>

        <!-- Main Stationary Luxury Login Card -->
        <div class="w-full max-w-md relative z-10">
            <!-- Subtle Ambient Halo Border -->
            <div class="absolute -inset-0.5 bg-gradient-to-r from-blue-500/20 via-amber-400/20 to-indigo-500/20 rounded-3xl blur-md opacity-60"></div>

            <!-- Solid, Elegant Frosted Glass Container -->
            <div class="relative w-full bg-slate-900/85 backdrop-blur-2xl border border-white/10 p-6 sm:p-10 rounded-3xl shadow-[0_25px_60px_-15px_rgba(0,0,0,0.85)] text-white overflow-hidden">
                <!-- Top Subtle Reflection Line -->
                <div class="absolute top-0 inset-x-8 h-[1px] bg-gradient-to-r from-transparent via-white/30 to-transparent pointer-events-none"></div>

                <!-- Central Official Emblem & Branding -->
                <div class="text-center mb-6">
                    <div class="relative inline-block mb-3">
                        <!-- Refined Golden Halo -->
                        <div class="absolute -inset-2 bg-gradient-to-tr from-amber-400/40 via-yellow-300/20 to-blue-500/30 rounded-full blur-md opacity-70 pointer-events-none"></div>

                        <!-- Stationary Plaque -->
                        <div class="relative w-24 h-24 sm:w-28 sm:h-28 mx-auto rounded-full p-1 bg-gradient-to-b from-amber-300 via-amber-500 to-amber-700 shadow-[0_12px_28px_rgba(0,0,0,0.6)]">
                            <img
                                src="/images/logo.png"
                                alt="Logo Yayasan Banua Insan Nusantara - BASARA"
                                class="w-full h-full rounded-full object-cover shadow-inner"
                            />
                        </div>
                    </div>

                    <!-- Institution Headers -->
                    <div class="mt-1">
                        <h1 class="text-2xl sm:text-3xl font-black tracking-tight bg-gradient-to-r from-white via-slate-100 to-amber-100 bg-clip-text text-transparent">
                            PKBM BASARA
                        </h1>
                        <p class="text-amber-300/90 text-xs sm:text-sm font-bold tracking-wider uppercase mt-1">
                            Yayasan Banua Insan Nusantara
                        </p>
                        <p class="text-slate-400 text-[11px] sm:text-xs font-medium tracking-wide mt-0.5">
                            Kalimantan Selatan • Indonesia
                        </p>
                    </div>

                    <!-- Package Badges -->
                    <div class="flex flex-wrap items-center justify-center gap-1.5 sm:gap-2 mt-3.5 text-[10px] sm:text-[11px] font-semibold">
                        <span class="px-2.5 py-0.5 rounded-full bg-blue-500/15 border border-blue-400/30 text-blue-200">Paket A (SD)</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/15 border border-emerald-400/30 text-emerald-200">Paket B (SMP)</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-amber-500/15 border border-amber-400/30 text-amber-200">Paket C (SMA)</span>
                    </div>
                </div>

                <!-- Public Verification CTA Banner -->
                <div class="mb-5">
                    <a
                        href="/verifikasi"
                        class="group relative w-full py-2.5 px-3 sm:px-4 rounded-xl bg-teal-500/15 hover:bg-teal-500/25 border border-teal-400/30 text-teal-200 hover:text-white font-bold text-xs transition-colors duration-200 flex items-center justify-center gap-2 shadow-sm"
                    >
                        <span class="text-sm">🔍</span>
                        <span>Verifikasi Keaslian Ijazah & Raport Publik</span>
                        <span class="group-hover:translate-x-0.5 transition-transform duration-150">&rarr;</span>
                    </a>
                </div>

                <!-- Session Status Message -->
                <div v-if="status" class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-400/30 font-medium text-xs text-emerald-300 text-center shadow-sm">
                    {{ status }}
                </div>

                <!-- Form Login -->
                <form @submit.prevent="submit" class="space-y-4">
                    <!-- Email Field -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5 flex items-center gap-1.5">
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
                                class="w-full pl-4 pr-4 py-3 rounded-xl bg-slate-950/70 border border-slate-700/80 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/20 text-white placeholder-slate-500 outline-none transition-colors duration-200 text-sm"
                                placeholder="contoh@pkbm.sch.id"
                            />
                        </div>
                        <p v-if="form.errors.email" class="text-rose-400 text-xs mt-1.5 font-medium">{{ form.errors.email }}</p>
                    </div>

                    <!-- Password Field with Toggle -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5 flex items-center gap-1.5">
                            <span>🔒</span>
                            <span>Kata Sandi / Password</span>
                        </label>
                        <div class="relative">
                            <input
                                :type="showPassword ? 'text' : 'password'"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                                class="w-full pl-4 pr-11 py-3 rounded-xl bg-slate-950/70 border border-slate-700/80 focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/20 text-white placeholder-slate-500 outline-none transition-colors duration-200 text-sm"
                                placeholder="Masukkan password akun Anda"
                            />
                            <button
                                type="button"
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-white p-1 rounded-lg transition-colors focus:outline-none"
                                :title="showPassword ? 'Sembunyikan password' : 'Lihat password'"
                            >
                                <span class="text-base">{{ showPassword ? '👁️' : '🙈' }}</span>
                            </button>
                        </div>
                        <p v-if="form.errors.password" class="text-rose-400 text-xs mt-1.5 font-medium">{{ form.errors.password }}</p>
                    </div>

                    <!-- Remember Session Checkbox -->
                    <div class="flex items-center justify-between text-xs pt-0.5">
                        <label class="flex items-center space-x-2.5 cursor-pointer select-none">
                            <input
                                type="checkbox"
                                v-model="form.remember"
                                class="w-4 h-4 rounded bg-slate-950/80 border-slate-700 text-indigo-600 focus:ring-0 focus:ring-offset-0 cursor-pointer transition"
                            />
                            <span class="text-slate-300 hover:text-white transition-colors">Ingat sesi login saya</span>
                        </label>
                    </div>

                    <!-- Submit Button (Calm, High-End Solid Gradient) -->
                    <div class="pt-2">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="relative w-full py-3.5 px-4 bg-gradient-to-r from-blue-600 via-indigo-600 to-blue-600 hover:from-blue-500 hover:to-indigo-500 font-extrabold text-sm rounded-xl text-white shadow-lg shadow-indigo-950/50 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2 border border-blue-400/30"
                        >
                            <span v-if="form.processing" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                            <span class="tracking-wide">{{ form.processing ? 'Memverifikasi Kredensial...' : 'Masuk ke Sistem PKBM' }}</span>
                            <span v-if="!form.processing" class="text-base">&rarr;</span>
                        </button>
                    </div>
                </form>

                <!-- Footer System Diagnostics -->
                <div class="mt-8 pt-5 border-t border-white/10 flex items-center justify-between text-[11px] text-slate-400">
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2 w-2">
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-400"></span>
                        </span>
                        <span>Laravel 11 Engine</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="relative flex h-2 w-2">
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-400"></span>
                        </span>
                        <span>Python ML Engine</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.bg-radial-vignette {
    background: radial-gradient(circle at 50% 40%, rgba(30, 41, 59, 0.5) 0%, rgba(2, 6, 23, 0.98) 85%);
}
</style>
