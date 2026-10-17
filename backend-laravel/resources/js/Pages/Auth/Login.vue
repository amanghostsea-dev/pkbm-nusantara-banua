<script setup>
import { ref } from 'vue'
import { Head, useForm } from '@inertiajs/vue3'

defineProps({
    status: String,
})

const selectedRole = ref('admin')

const form = useForm({
    email: 'admin@pkbm.sch.id',
    password: 'password',
    remember: false,
})

const demoAccounts = [
    {
        id: 'admin',
        title: 'Admin Utama',
        email: 'admin@pkbm.sch.id',
        password: 'password',
        icon: '👑',
        desc: 'Akses penuh ke seluruh sistem & pengaturan',
        color: 'from-amber-600 to-amber-700',
    },
    {
        id: 'staf',
        title: 'Staf Admin',
        email: 'staf.admin@pkbm.sch.id',
        password: 'password',
        icon: '🛡️',
        desc: 'Admin operasional di bawah Admin Utama',
        color: 'from-blue-600 to-indigo-600',
    },
    {
        id: 'tutor',
        title: 'Tutor / Guru',
        email: 'tutor@pkbm.sch.id',
        password: 'password',
        icon: '👨‍🏫',
        desc: 'Input nilai, absensi kelas & materi',
        color: 'from-emerald-600 to-teal-600',
    },
    {
        id: 'siswa',
        title: 'Siswa / WB',
        email: 'siswa@pkbm.sch.id',
        password: 'password',
        icon: '👨‍🎓',
        desc: 'Cek jadwal, nilai, absensi & raport',
        color: 'from-purple-600 to-pink-600',
    },
]

const setDemoAccount = (acc) => {
    selectedRole.value = acc.id
    form.email = acc.email
    form.password = acc.password
}

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

        <div class="max-w-xl w-full bg-white/10 backdrop-blur-2xl border border-white/20 p-8 sm:p-10 rounded-3xl shadow-2xl text-white relative z-10 transition-all duration-300">
            <!-- Header Brand -->
            <div class="text-center mb-8">
                <div class="inline-flex p-4 bg-gradient-to-tr from-blue-500/20 to-indigo-500/20 rounded-2xl mb-3 border border-white/20 shadow-inner">
                    <span class="text-4xl">🏫</span>
                </div>
                <h1 class="text-3xl font-extrabold tracking-tight bg-gradient-to-r from-white via-blue-100 to-indigo-200 bg-clip-text text-transparent">
                    Sistem Informasi PKBM
                </h1>
                <p class="text-blue-200/90 text-sm mt-1.5 font-medium">Pusat Kegiatan Belajar Masyarakat Terpadu</p>
                <div class="flex items-center justify-center gap-2 mt-3 text-xs">
                    <span class="px-2.5 py-0.5 rounded-full bg-blue-500/20 border border-blue-400/30 text-blue-300">Paket A (SD)</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/20 border border-emerald-400/30 text-emerald-300">Paket B (SMP)</span>
                    <span class="px-2.5 py-0.5 rounded-full bg-amber-500/20 border border-amber-400/30 text-amber-300">Paket C (SMA)</span>
                </div>
            </div>

            <!-- Role Selector Pills for Demo Helpers (Fills input form, still requires authentication) -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-2.5">
                    <label class="text-xs font-semibold uppercase tracking-wider text-blue-200/80">
                        🔑 Akun Demo (Klik untuk Isi Form Otomatis):
                    </label>
                </div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                    <button
                        type="button"
                        v-for="acc in demoAccounts"
                        :key="acc.id"
                        @click="setDemoAccount(acc)"
                        :class="[
                            'p-3 rounded-xl border transition-all duration-200 flex flex-col justify-between text-left group',
                            selectedRole === acc.id
                                ? 'bg-blue-600/30 border-blue-400 ring-2 ring-blue-400/30'
                                : 'bg-white/10 border-white/15 hover:bg-white/20 hover:border-blue-400'
                        ]"
                        :title="`Pilih akun ${acc.title}`"
                    >
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xl">{{ acc.icon }}</span>
                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-blue-500/30 text-blue-200 font-bold">PILIH</span>
                        </div>
                        <div>
                            <div class="font-bold text-xs leading-tight text-white group-hover:text-blue-300">{{ acc.title }}</div>
                            <div class="text-[10px] text-blue-200/70 truncate">{{ acc.email }}</div>
                        </div>
                    </button>
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
                        class="w-full px-4 py-3 rounded-xl bg-slate-900/40 border border-white/20 focus:border-blue-400 focus:ring-2 focus:ring-blue-400/30 text-white placeholder-gray-400 outline-none transition"
                        placeholder="••••••••"
                    />
                    <p v-if="form.errors.password" class="text-rose-400 text-xs mt-1.5">{{ form.errors.password }}</p>
                </div>

                <div class="flex items-center justify-between text-xs pt-1">
                    <label class="flex items-center space-x-2 cursor-pointer select-none">
                        <input type="checkbox" v-model="form.remember" class="rounded bg-white/10 border-white/30 text-blue-500 focus:ring-0" />
                        <span class="text-blue-200">Ingat sesi saya</span>
                    </label>
                    <span class="text-blue-300/80">Default password: <span class="font-mono text-white">password</span></span>
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
