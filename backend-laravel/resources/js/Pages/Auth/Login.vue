<script setup>
import { Head, useForm } from '@inertiajs/vue3'

defineProps({
    status: String,
})

const form = useForm({
    email: '',
    password: '',
    remember: false,
})

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    })
}
</script>

<template>
    <Head title="Masuk ke Sistem PKBM" />

    <div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-950 via-indigo-950 to-blue-950 px-4 py-8 relative overflow-hidden font-sans">
        <!-- Background Ambient Glow -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-md w-full bg-white/10 backdrop-blur-2xl border border-white/20 p-8 sm:p-10 rounded-3xl shadow-2xl text-white relative z-10 transition-all duration-300">
            <!-- Header Brand -->
            <div class="text-center mb-7">
                <div class="relative inline-block mb-3 group">
                    <div class="absolute -inset-1 bg-gradient-to-r from-amber-500 via-yellow-400 to-amber-600 rounded-full blur-md opacity-70 group-hover:opacity-100 transition duration-300"></div>
                    <img
                        src="/images/logo.png"
                        alt="Logo Yayasan Banua Insan Nusantara"
                        class="relative w-24 h-24 sm:w-28 sm:h-28 rounded-full object-cover border-2 border-amber-300/80 shadow-2xl mx-auto ring-4 ring-black/40 transform transition group-hover:scale-105 duration-300"
                    />
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight bg-gradient-to-r from-white via-amber-100 to-blue-200 bg-clip-text text-transparent">
                    PKBM BASARA
                </h1>
                <p class="text-amber-200/90 text-xs sm:text-sm font-semibold tracking-wide uppercase mt-1">
                    Yayasan Banua Insan Nusantara
                </p>
                <p class="text-blue-200/80 text-xs font-medium">Kalimantan Selatan</p>
                <div class="flex items-center justify-center gap-2 mt-3 text-xs">
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300">Paket A (SD)</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300">Paket B (SMP)</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-300">Paket C (SMA)</span>
                </div>
            </div>

            <!-- Public Verification Button -->
            <div class="mb-6">
                <a
                    href="/verifikasi"
                    class="w-full py-2.5 px-4 rounded-xl bg-teal-500/20 hover:bg-teal-500/30 border border-teal-400/30 text-teal-200 hover:text-white font-bold text-xs transition flex items-center justify-center gap-2 shadow-sm"
                >
                    <span>🔍</span>
                    <span>Pusat Verifikasi Keaslian Ijazah PKBM (Publik)</span>
                    <span>&rarr;</span>
                </a>
            </div>

            <div v-if="status" class="mb-4 p-3 rounded-xl bg-emerald-500/20 border border-emerald-400/30 font-medium text-xs text-emerald-300 text-center">
                {{ status }}
            </div>

            <!-- Form -->
            <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-blue-200/90 mb-1.5">Email</label>
                    <input
                        type="email"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="email"
                        class="w-full px-4 py-3 rounded-xl bg-slate-900/40 border border-white/20 focus:border-blue-400 focus:ring-2 focus:ring-blue-400/30 text-white placeholder-gray-400 outline-none transition"
                        placeholder="contoh@pkbm.sch.id"
                    />
                    <p v-if="form.errors.email" class="text-rose-400 text-xs mt-1.5">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wider text-blue-200/90 mb-1.5">Password</label>
                    <input
                        type="password"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        class="w-full px-4 py-3 rounded-xl bg-slate-900/40 border border-white/20 focus:border-blue-400 focus:ring-2 focus:ring-blue-400/30 text-white placeholder-gray-400 outline-none transition"
                        placeholder="Masukkan password akun Anda"
                    />
                    <p v-if="form.errors.password" class="text-rose-400 text-xs mt-1.5">{{ form.errors.password }}</p>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center space-x-2 cursor-pointer select-none">
                        <input type="checkbox" v-model="form.remember" class="rounded bg-white/10 border-white/30 text-blue-500 focus:ring-0" />
                        <span class="text-blue-200">Ingat sesi saya</span>
                    </label>
                </div>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="w-full mt-2 py-3.5 px-4 bg-gradient-to-r from-blue-500 via-indigo-600 to-blue-600 hover:from-blue-600 hover:to-indigo-700 font-bold rounded-xl text-white shadow-xl shadow-blue-500/30 transition-all duration-200 active:scale-[0.99] disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                >
                    <span v-if="form.processing" class="inline-block w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    <span>{{ form.processing ? 'Memverifikasi...' : 'Masuk ke Sistem PKBM' }}</span>
                </button>
            </form>

            <!-- Microservice Badges -->
            <div class="mt-8 pt-5 border-t border-white/10 flex items-center justify-between text-[11px] text-blue-300/70">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Laravel 11 UI Engine</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                    <span>Python FastAPI ML Engine</span>
                </div>
            </div>
        </div>
    </div>
</template>
